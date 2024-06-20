<?php

namespace App\Helpers;

use App\Exceptions\CofigFileNotFoundException;

class Config
{
      public static function getFileContents($file_name)
      {
            $filePath = realpath(__DIR__ . '/../configs/' . $file_name . '.php');
            if (!file_exists($filePath))
                  throw new CofigFileNotFoundException();
            return require $filePath;
      }
      
      public static function get(string $file_name, string $key = null)
      {
            $fileContents = self::getFileContents($file_name);
            if (is_null($key))
                  return $fileContents;
            return $fileContents[$key] ?? null;
      }
}
