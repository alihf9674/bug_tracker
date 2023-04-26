<?php

namespace Tests\Unit;

use PDO;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Contracts\DatabaseConnectionInterface;

class PDODatabaseConnectionTest extends TestCase
{
      public function testPdoDatabaseConnectionImplementsDatabaseConnectionInterface()
      {
            $config = $this->getConfig();
            $pdoConnection = new PDODatabaseConnection($config);
            $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
      }
      public function testConnectMethodShouldBeConnectToDatabase()
      {
            $config = $this->getConfig();
            $pdoConnection = new PDODatabaseConnection($config);
            $pdoConnection->connect();
            $this->assertInstanceOf(PDO::class, $pdoConnection->getConnection());
      }
      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
}
