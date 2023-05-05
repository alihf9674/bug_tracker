<?php

namespace Tests\Unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDOQueryBuilder;
use App\Database\PDODatabaseConnection;

class PDOQueryBuilderTest extends TestCase
{
      private $queryBuilder;

      public function setUp(): void
      {
            $pdoConnection = new PDODatabaseConnection($this->getConfig());
            $this->queryBuilder = new PDOQueryBuilder($pdoConnection->connect());
            parent::setUp();
      }
      public function testItCanCreateData()
      {
            $result = $this->InserIntoDb();
            $this->assertIsInt($result);
            $this->assertGreaterThan(0, $result);
      }
      public function testItCanUpdateData()
      {
            $this->inserIntoDb();
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'User Name')
                  ->where('email', 'EmailAddress@gmail.com')
                  ->update(['email' => 'useremail@gmail.com', 'name' => 'User name']);
            $this->assertEquals(1, $result);
      }
      private function InserIntoDb()
      {
            $data = [
                  'name' => 'First Bug Report',
                  'link' => 'http://link.com',
                  'user' => 'User Name',
                  'email' => 'EmailAddress@gmail.com'
            ];
            return $this->queryBuilder->table('bugs')->create($data);
      }
      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
      public function tearDown(): void
      {
            $this->queryBuilder->truncateAllTable();
            parent::tearDown();
      }
}
