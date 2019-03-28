<?php
require TEST_APP . '/TestConfig.php';
require TEST_APP . '/ImginS3Source.php';

use PHPUnit\Framework\TestCase;
use Aws\S3\S3Client;

class S3Test extends TestCase
{
    private $client = null;
    private $source = null;

    public function setUp() :void
    {
        parent::setUp();

        $s3Config = [
            'region' => TestConfig::REGION,
            'version' => 'latest',
            'credentials' => [
                'key' => TestConfig::ACCESS_KEY_ID,
                'secret'  => TestConfig::SECRET_ACCESS_KEY,
            ]
        ];

        $this->$client = S3Client::factory($s3Config);
        $this->$source = new ImginS3Source($this->$client, 'imgin-test-fusic');
    }

    public function test_getType()
    {
        $this->assertEquals('S3', $this->$source->getType());
    }

    public function test_createObject()
    {
        $result = $this->$source->createObject('test-img.png', './tests/tmp/test-img.png');

        $this->assertTrue(true);
    }
}