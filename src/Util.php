<?php
namespace App;

class Util
{
    /**
     * 設定されているパーミッションでディレクトリを作る
     *
     * @param string $path
     * @return bool
     */
    static function mkdirWithDirmode($path)
    {
        $dirmode = 0755;
        if (defined('IMGIN_DIR_MODE')) {
            $dirmode = IMGIN_DIR_MODE;
        }
        $mask = umask();
        umask(000);
        $result = mkdir($path, $dirmode, true);
        umask($mask);

        return $result;
    }

    /**
     * 指定したディレクトリを削除する
     * ディレクトリの下にファイルや別のディレクトリがあっても、再帰的に全部削除する
     *
     * @author kaneko
     * @param string $dir
     * @return null
     */
    static function cleardir($dir)
    {
        if (is_dir($dir) && !is_link($dir)) {
            $subDirs = glob($dir . DS . '*', GLOB_ONLYDIR);
            foreach ($subDirs as $sdir) {
                self::cleardir($sdir);
            }
            array_map('unlink', glob($dir . DS . '*'));
            rmdir($dir);
        }
    }
}