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
            $this->queryBuilder->beginTransaction();
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
                  ->update([
                        'email' => 'Mehrdad@gmail.com',
                        'name' => 'First After Update'
                  ]);

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

      public function testItCanGetFirstRow()
      {
            $this->multipleInsertIntoDb(10, ['name' => 'First Row']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('name', 'First Row')
                  ->first();

            $this->assertIsObject($result);
            $this->assertObjectHasAttribute('id', $result);
            $this->assertObjectHasAttribute('name', $result);
            $this->assertObjectHasAttribute('link', $result);
            $this->assertObjectHasAttribute('email', $result);
            $this->assertObjectHasAttribute('user', $result);
      }

      public function testItCanFindWithId()
      {
            $this->insertIntoDb();
            $id = $this->insertIntoDb(['name' => 'Find']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->find($id);

            $this->assertIsObject($result);
            $this->assertEquals('Find', $result->name);
      }

      public function testItCanFindBy()
      {
            $this->insertIntoDb();
            $id = $this->insertIntoDb(['name' => 'Find By']);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->findBy('name', 'Find By');

            $this->assertIsObject($result);
            $this->assertEquals($id, $result->id);
      }

      public function testItReturnsZeroWhenRecordNotFoundForUpdate()
      {
            $this->multipleInsertIntoDb(5);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'dummy')
                  ->update(['name' => 'test']);

            $this->assertEquals(0, $result);
      }

      public function testItReturnEmptyArrayWhenRecordNotFound()
      {
            $this->multipleInsertIntoDb(5);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'Dummy')
                  ->get();
            $this->assertIsArray($result);
            $this->assertEmpty($result);
      }

      public function testItReturnNullWhenFirstRecordNotFound()
      {
            $this->multipleInsertIntoDb(5);
            $result = $this->queryBuilder
                  ->table('bugs')
                  ->where('user', 'Dummy')
                  ->first();
            $this->assertNull($result);
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
            $this->queryBuilder->rollBack();
            parent::tearDown();
      }
}
