<?php
require TEST_APP . '/TestConfig.php';
require TEST_APP . '/../ImginS3Source.php';

use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;

class S3Test extends TestCase
{
    private $client = null;
    private $source = null;

    public function setUp(): void
    {
        parent::setUp();

        $s3Config = [
            'region' => TestConfig::REGION,
            'version' => 'latest',
            'credentials' => [
                'key' => TestConfig::ACCESS_KEY_ID,
                'secret' => TestConfig::SECRET_ACCESS_KEY,
            ],
        ];

        $this->$client = S3Client::factory($s3Config);
        $this->$source = new ImginS3Source($this->$client, 'imgin-test-fusic');
    }

    public function test_getType()
    {
        $this->assertEquals('S3', $this->$source->getType());
    }

    /**
     * S3上のオブジェクトを取得して指定された場所に保存する
     *
     * @param string $key S3オブジェクトのkey
     * @param string $path 画像ファイルの保存先
     * @return null
     */
    public function test_createObject()
    {
        $s3ImgKey = 'test-img.png';
        $saveAs = TEST_APP . '/tmp/test-img.png';

        $this->$source->createObject($s3ImgKey, $saveAs);

        $this->assertTrue(file_exists($saveAs), 'ファイルを保存できているか');
        $this->assertTrue(mime_content_type($saveAs) === 'image/png', '画像ファイルか');
        $savedPerm = substr(sprintf('%o', fileperms($saveAs)), -4);
        $this->assertTrue($savedPerm === '0644', 'パーミッション');

        // 後片付け
        unlink($saveAs);
    }

    /**
     * cache用にディレクトリを作って、その中に画像ファイルを保存する
     * 画像ファイルの保存はcreateObject()でやっているので、ここではテストしてない
     *
     * @param string $key S3上の画像オブジェクトkey
     * @return null
     */
    public function test_getPath()
    {
        $expectedCacheDir = '/tmp/imgincache/';
        $s3ImgKey = 'test-img.png';

        $this->$source->getPath($s3ImgKey);

        $this->assertTrue(file_exists($expectedCacheDir . $s3ImgKey)); // 無いと思うけど、windows上でテスト走らせたら失敗するはず

        // 後片付け
        unlink($expectedCacheDir . $s3ImgKey);
    }
}
