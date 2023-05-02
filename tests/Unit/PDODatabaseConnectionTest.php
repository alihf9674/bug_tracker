<?php

namespace Tests\Unit;

use PDO;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Exceptions\ConfigNotValidException;
use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\DatabaseConnectionException;

class PDODatabaseConnectionTest extends TestCase
{
      public function testPdoDatabaseConnectionImplementsDatabaseConnectionInterface()
      {
            $config = $this->getConfig();
            $pdoConnection = new PDODatabaseConnection($config);
            $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
      }
      public function testConnectMethodShouldReturnValidInstance()
      {
            $config = $this->getConfig();
            $pdoConnection = new PDODatabaseConnection($config);
            $pdoHandler = $pdoConnection->connect();
            $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);
            return $pdoHandler;
      }
      /**
       * @depends testConnectMethodShouldReturnValidInstance
       */
      public function testConnectMethodShouldBeConnectToDatabase($pdoHandler)
      {
            $this->assertInstanceOf(PDO::class, $pdoHandler->getConnection());
      }
      public function testItThrowsExceptionIfConfigIsInvalid()
      {
            $this->expectException(DatabaseConnectionException::class);
            $config = $this->getConfig();
            $config['database'] = 'test';
            $pdoConnection = new PDODatabaseConnection($config);
            $pdoConnection->connect();
      }
      public function testReceivedConfigHaveRequiredKey()
      {
            $this->expectException(ConfigNotValidException::class);
            $config = $this->getConfig();
            unset($config['db_user']);
            $pdoConnection = new PDODatabaseConnection($config);
            $pdoConnection->connect();
      }
      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
}
