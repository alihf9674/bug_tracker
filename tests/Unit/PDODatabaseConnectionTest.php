<?php

namespace Tests\Unit;

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

      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
}
