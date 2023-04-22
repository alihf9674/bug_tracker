<?php

namespace Tests\Unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Exceptions\CofigFileNotFoundException;

class ConfigTest extends TestCase
{
      public function testGetContentsReturnsArray()
      {
            $config = Config::getFileContents('database');
            $this->assertIsArray($config);
      }

      public function testItThrowsExceptionIfNotFound()
      {
            $this->expectException(CofigFileNotFoundException::class);
            $config = Config::getFileContents('test');
      }

      public function testGetMethodReturnsValidData()
      {
            $config = Config::get('database', 'pdo');
            $expectedData = [
                  'driver' => 'mysql',
                  'host' => '127.0.0.1',
                  'database' => 'bug_tracker',
                  'db_user' => 'root',
                  'db_password' => '123456'
            ];
            $this->assertEquals($config, $expectedData);
      }
}
