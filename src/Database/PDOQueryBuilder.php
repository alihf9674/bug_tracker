<?php

namespace App\Database;

use PDO;
use App\Contracts\DatabaseConnectionInterface;

class PDOQueryBuilder
{
      protected $table;
      protected $connection;
      protected $conditions;
      protected $values;

      public function __construct(DatabaseConnectionInterface $connection)
      {
            $this->connection = $connection->getConnection();
      }
      public function table(string $table)
      {
            $this->table = $table;
            return $this;
      }

      public function get(array $columns = ['*'])
      {
            $conditions = implode(' AND ', $this->conditions);
            $columns = implode(', ', $columns);
            $sql = "SELECT {$columns} FROM {$this->table} WHERE {$conditions}";
            $query = $this->connection->prepare($sql);
            $query->execute($this->values);
            return $query->fetchAll();
      }
      public function first(array $columns = ['*'])
      {
            $data = $this->get($columns);
            return empty($data) ? null : $data[0];
      }
      public function create(array $data)
      {
            $palceholder = [];
            foreach ($data as $column => $value) {
                  $palceholder[] = '?';
            }
            $fields = implode(',', array_keys($data));
            $palceholder = implode(',', $palceholder);
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$palceholder})";
            $query = $this->connection->prepare($sql);
            $query->execute(array_values($data));
            return (int)$this->connection->lastInsertId();
      }

      public function where(string $column, string $value)
      {
            $this->conditions[] = "{$column}=?";
            $this->values[] = $value;
            return $this;
      }
      public function update(array $data)
      {
            $fields = [];
            foreach ($data as $column => $value) {
                  $fields[] = "{$column}='{$value}'";
            }
            $fields = implode(', ', $fields);
            $conditions = implode(' AND ', $this->conditions);
            $sql = "UPDATE {$this->table} SET {$fields} WHERE {$conditions}";
            $query = $this->connection->prepare($sql);
            $query->execute($this->values);
            return $query->rowCount();
      }
      public function truncateAllTable()
      {
            $query = $this->connection->prepare("SHOW TABLES");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $table) {
                  $this->connection->prepare("TRUNCATE TABLE `{$table}`")->execute();
            }
      }
      public function delete()
      {
            $conditions = implode(' AND ', $this->conditions);
            $sql = "DELETE FROM {$this->table} WHERE {$conditions}";
            $query = $this->connection->prepare($sql);
            $query->execute($this->values);
            return $query->rowCount();
      }
}
