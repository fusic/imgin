<?php
/*
  Imgin configs

  $imagine
  $allowCachePattern
  $source
*/
if (!defined('IMGIN_DIR_MODE')) {
   define('IMGIN_DIR_MODE', 0755);
}
if (!defined('IMGIN_FILE_MODE')) {
   define('IMGIN_FILE_MODE', 0644);
}
if (!defined('IMGIN_CACHE_DIR')) {
   define('IMGIN_CACHE_DIR', '/tmp/imgincache');
}

// ImagineInterface
$imagine = new Imagine\Gd\Imagine();

// allow manipurated image cache pattern
$allowCachePattern = array(
    $dirRegex, // full open
    // '100x200',
    // '140x100',
);

// ImginSource
// File
$source = new ImginFileSource($rootPath);

/*
// S3
$s3Config = [
    'region' => TestConfig::REGION,
    'version' => 'latest',
    'credentials' => [
        'key' => TestConfig::ACCESS_KEY_ID,
        'secret'  => TestConfig::SECRET_ACCESS_KEY,
    ]
];

$this->$client = S3Client::factory($s3Config);
*/
