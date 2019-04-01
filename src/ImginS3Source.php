<?php
namespace App;

use App\Util;
use App\ImginSource;
use Aws\S3\S3Client;

class ImginS3Source implements ImginSource
{
    private $client;
    private $bucket;
    private $prefix;

    public function __construct(S3Client $client, $bucket, $prefix = '')
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

    public function getType()
    {
        return 'S3';
    }

    /**
     * 画像をS3から取得して、cache用のディレクトリに保存する
     *
     * @param string $key
     * @return string
     */
    public function getPath($key)
    {
        $tmpPath = DS . 'tmp' . DS . 'imgincache' . DS . $key;
        if (defined('IMGIN_CACHE_DIR')) {
            $tmpPath = IMGIN_CACHE_DIR . DS . $key;
        }
        Util::cleardir(dirname($tmpPath));

        return $this->createObject($key, $tmpPath);
    }

    /**
     * S3から画像を取得して指定された場所に保存する
     *
     * @param string $key
     * @param string $path
     * @return string
     */
    public function createObject($key, $path)
    {
        try {
            if (!is_dir(dirname($path))) {
                $dirmode = 0755;
                if (defined('IMGIN_DIR_MODE')) {
                    $dirmode = IMGIN_DIR_MODE;
                }
                Util::mkdirWithDirmode(dirname($path), $dirmode, true);
            }
            $result = $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $this->prefix . $key,
                'SaveAs' => $path,
            ));
            $filemode = 0644;
            if (defined('IMGIN_FILE_MODE')) {
                $filemode = IMGIN_FILE_MODE;
            }
            chmod($path, $filemode);

            return $path;
        } catch (Exception $e) {
            unlink($path);
            error_log($e->getMessage(), 0);

            return $path;
        }
    }
}
