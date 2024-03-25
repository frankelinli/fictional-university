<?php
class Dotenv {
  public static function load($filePath)
  {
    if (!file_exists($filePath)) {
      throw new \Exception("未找到环境配置文件: $filePath");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) {
        continue;
      }

      list($name, $value) = explode('=', $line, 2);
      putenv(trim($name) . '=' . trim($value));
    }
  }
}
Dotenv::load(FIC_UNI_PATH . '/.env');
?>