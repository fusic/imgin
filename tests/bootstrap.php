<?php
const TEST_APP = __DIR__;

$_SERVER['PHP_SELF'] = '/';

if (!defined('IMGIN_DIR_MODE')) {
    define('IMGIN_DIR_MODE', 0755);
 }
 if (!defined('IMGIN_FILE_MODE')) {
    define('IMGIN_FILE_MODE', 0644);
 }
 if (!defined('IMGIN_CACHE_DIR')) {
    define('IMGIN_CACHE_DIR', '/tmp/imgincache');
 }

