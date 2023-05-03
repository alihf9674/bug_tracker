<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;

class PDOQueryBuilder
{
      protected $table;
      protected $connection;

      public function __construct(DatabaseConnectionInterface $connection)
      {
            $this->connection = $connection->getConnection();
      }
      public function table(string $table)
      {
            $this->table = $table;
            return $this;
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
}
