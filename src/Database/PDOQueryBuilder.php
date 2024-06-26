<?php

namespace App\Database;

use PDO;
use App\Contracts\DatabaseConnectionInterface;
use PDOStatement;

class PDOQueryBuilder
{
      protected $table;
      protected $connection;
      protected $conditions;
      protected $values;
      protected $statement;

      public function __construct(DatabaseConnectionInterface $connection)
      {
            $this->connection = $connection->getConnection();
      }

      public function table(string $table)
      {
            $this->table = $table;
            return $this;
      }

      public function beginTransaction()
      {
            $this->connection->beginTransaction();
      }

      public function rollBack()
      {
            $this->connection->rollback();
      }

      public function create(array $data)
      {
            $palceholder = [];
            foreach ($data as $column => $value) {
                  $palceholder[] = '?';
            }
            $fields = implode(',', array_keys($data));
            $palceholder = implode(',', $palceholder);
            $this->values = array_values($data);
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$palceholder})";
            $this->execute($sql);
            return (int)$this->connection->lastInsertId();
      }

      public function update(array $data)
      {
            $fields = [];
            foreach ($data as $column => $value) {
                  $fields[] = "{$column}='{$value}'";
            }
            $fields = implode(', ', $fields);
            $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->conditions}";
            $this->execute($sql);
            return $this->statement->rowCount();
      }

      public function delete()
      {
            $sql = "DELETE FROM {$this->table} WHERE {$this->conditions}";
            $this->execute($sql);
            return $this->statement->rowCount();
      }


      public function get(array $columns = ['*'])
      {
            $columns = implode(', ', $columns);
            $sql = "SELECT {$columns} FROM {$this->table} WHERE {$this->conditions}";
            $this->execute($sql);
            return $this->statement->fetchAll();
      }

      public function first(array $columns = ['*'])
      {
            $data = $this->get($columns);
            return empty($data) ? null : $data[0];
      }

      public function find($id)
      {
            return $this->where('id', $id)->first();
      }

      public function findBy(string $column, $value)
      {
            return $this->where($column, $value)->first();
      }

      public function where(string $column, string $value)
      {
            if (is_null($this->conditions))
                  $this->conditions = "{$column}=?";
            else
                  $this->conditions .= "AND {$column}=?";
            $this->values[] = $value;
            return $this;
      }

      public function truncateAllTable()
      {
            $query = $this->connection->prepare("SHOW TABLES");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $table) {
                  $this->connection->prepare("TRUNCATE TABLE `{$table}`")->execute();
            }
      }

      private function execute(string $sql)
      {
            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute($this->values);
            $this->values = [];
            return $this;
      }
}
