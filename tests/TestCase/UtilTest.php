<?php
namespace App\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use App\Util;

class UtilTest extends TestCase
{
    /**
     * 指定したディレクトリを削除する
     * ディレクトリの下にファイルや別のディレクトリがあっても、再帰的に全部削除する
     *
     * @author kaneko
     * @param string $dir
     * @return null
     */
    public function test_cleardir()
    {
        // テスト実行の下ごしらえ
        $testTmpDir = TEST_APP . '/tmp';
        $testTmpHogeDir = $testTmpDir . '/hoge';
        $testImgName = 'test.png';
        $testImgName2 = 'test2.png';
        $imagePaths = [
            $testTmpDir . DS . $testImgName,
            $testTmpDir . DS . $testImgName2,
            $testTmpHogeDir . DS . $testImgName,
            $testTmpHogeDir . DS . $testImgName2
        ];
        $this->helper_cleardir($testTmpDir, $testTmpHogeDir, $imagePaths);

        Util::cleardir($testTmpDir);

        $paths = array_merge([$testTmpDir, $testTmpHogeDir], $imagePaths);
        foreach ($paths as $path) {
            $this->assertTrue(!\file_exists($path), '削除されていません:' . $path);
        }

    }

    private function helper_cleardir($testTmpDir, $testTmpHogeDir, $imagePaths)
    {
        // テスト用のディレクトリ作る
        if (!\file_exists($testTmpDir)) {
            mkdir($testTmpDir);
        }
        if (!\file_exists($testTmpHogeDir)) {
            mkdir($testTmpHogeDir);
        }

        // テスト用の画像ファイルつくる
        $imageId = imagecreatetruecolor(100, 100);
        foreach ($imagePaths as $path) {
            imagepng($imageId, $path);
        }
    }
}