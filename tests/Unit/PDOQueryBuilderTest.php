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
      public function testItcanUpdateWithMultipleWhere()
      {
            $this->insertIntoDb();
            $this->insertIntoDb(['user' => 'sara']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'User Name')
                  ->where('link', 'http://link.com')
                  ->update(['name' => 'after multiple where']);

            $this->assertEquals(1, $result);
      }
      public function testItCanUpdateData()
      {
            $this->insertIntoDb();
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'User Name')
                  ->update([
                        'email' => 'useremailaddress@yahoo.com', 'name' => 'user name after update'
                  ]);

            $this->assertEquals(1, $result);
      }
      public function testItCanFetchSpecificColumns()
      {

            $this->multipleInsertIntoDb(10);
            $this->multipleInsertIntoDb(10, ['name' => 'New']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('name', 'New')
                  ->get(['name', 'user']);

            $this->assertIsArray($result);
            $this->assertObjectHasAttribute('name', $result[0]);
            $this->assertObjectHasAttribute('user', $result[0]);
            $result = json_decode(json_encode($result[0]), true);
            $this->assertEquals(['name', 'user'], array_keys($result));
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
      public function testItCanFetchData()
      {
            $this->multipleInsertIntoDb(10);
            $this->multipleInsertIntoDb(10, ['user' => 'Cyrus']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'Cyrus')->get();

            $this->assertIsArray($result);
            $this->assertCount(10, $result);
      }
      private function insertIntoDb($options = [])
      {
            $data = array_merge([
                  'name' => 'First Bug Report',
                  'link' => 'http://link.com',
                  'user' => 'User Name',
                  'email' => 'EmailAddress@gmail.com'
            ], $options);

            return $this->queryBuilder->table('bugs')->create($data);
      }
      private function getConfig()
      {
            return Config::get('database', 'pdo_testing');
      }
      private function multipleInsertIntoDb($count, $options = [])
      {
            for ($i = 1; $i <= $count; $i++) {
                  $this->insertIntoDb($options);
            }
      }
      public function tearDown(): void
      {
            $this->queryBuilder->truncateAllTable();
            parent::tearDown();
      }
}
