<?php
require TEST_APP . '/TestConfig.php';
require TEST_APP . '/ImginS3Source.php';

use PHPUnit\Framework\TestCase;
use Aws\S3\S3Client;

class S3Test extends TestCase
{
    public function test_getType()
    {
        $s3Config = [
            'key' => TestConfig::ACCESS_KEY_ID,
            'secret' => TestConfig::SECRET_ACCESS_KEY,
            'region' => TestConfig::REGION
        ];

        $client = S3Client::factory($s3Config);
        $source = new ImginS3Source($client, 'imgin-test-fusic');

        $this->assertEquals('S3', $source->getType());
    }
}