<?php

function mkdirWithDirmode($path)
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

function cleardir($dir)
{
    if (is_dir($dir) && !is_link($dir)) {
        array_map('cleardir', glob($dir . DS . '*', GLOB_ONLYDIR));
        array_map('unlink', glob($dir . DS . '*'));
        rmdir($dir);
    }
}
