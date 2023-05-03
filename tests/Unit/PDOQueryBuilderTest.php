<?php

namespace Tests\Unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDOQueryBuilder;
use App\Database\PDODatabaseConnection;

class PDOQueryBuilderTest extends TestCase
{
      public function testItCanCreateData()
      {
            $pdoConnection = new PDODatabaseConnection($this->getConfig());
            $queryBuilder = new PDOQueryBuilder($pdoConnection->connect());
            $data = [
                  'name' => 'First Bug Report',
                  'link' => 'http://link.com',
                  'user' => 'User Name',
                  'email' => 'EmailAddress@gmail.com'
            ];
            $result = $queryBuilder->table('bugs')->create($data);
            $this->assertIsInt($result);
            $this->assertGreaterThan(0, $result);
      }
      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
}
