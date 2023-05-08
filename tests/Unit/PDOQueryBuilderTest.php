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
            $result = $this->insertIntoDb();
            $this->assertIsInt($result);
            $this->assertGreaterThan(0, $result);
      }
      public function testItCanUpdateData()
      {
            $this->insertIntoDb();

            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'User Name')
                  ->update(['email' => 'useremailaddress@yahoo.com', 'name' => 'user name after update']);  
            $this->assertEquals(1, $result);
      }
      public function testItCanDeleteRecord()
      {
            $this->insertIntoDb();
            $this->insertIntoDb();
            $this->insertIntoDb();
            $this->insertIntoDb();

            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('name', 'First Bug Report')
                  ->delete();
            $this->assertEquals(4, $result);
      }
      private function insertIntoDb()
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
