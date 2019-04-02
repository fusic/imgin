<?php

use App\Util;

/**
 * imgin
 *
 * ## Support pattern
 *
 * - /100x80/
 */
require dirname(__FILE__) . '/vendor/autoload.php';

$rootPath = dirname(__FILE__);

$dirRegex = '(\d+)x(\d+)(-[^/]+)?';

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Load config.php
require dirname(__FILE__) . '/config.php';

/**
 * Clear manipulated image by CLI
 *
 */
if (php_sapi_name() == 'cli') {
    $imgin = new Commando\Command();
    $imgin->option()
        ->require()
        ->describedAs('Clear manipulated image')
        ->must(function ($cmd) {
            return in_array($cmd, array('clearcache'));
        })
        ->option()
        ->describedAs('Original image path')
        ->must(function ($originalImagePath) {
            if (is_null($originalImagePath)) {
                return true;
            }
            if (!file_exists($originalImagePath)) {
                throw new \Exception(sprintf('%s not exists', $originalImagePath));
            }

            return true;
        })
        ->option('a')
        ->aka('all')
        ->describedAs('When clear cache all, use this option')
        ->boolean();

    // clearcache
    if ($imgin[0] === 'clearcache') {

        // --all
        if ($imgin['all']) {
            foreach (glob($rootPath . DS . '*', GLOB_ONLYDIR) as $dirname) {
                if (preg_match('#/' . $dirRegex . '$#', $dirname)) {
                    Util::cleardir($dirname);
                }
            }

            return;
        }

        $originalImagePath = $imgin[1];
        if (preg_match('#^' . $rootPath . '(.+)#', $originalImagePath, $matches)) {
            $relativeImagePath = $matches[1];
            foreach (glob($rootPath . DS . '*', GLOB_ONLYDIR) as $dirname) {
                if (preg_match('#/' . $dirRegex . '$#', $dirname, $matches)) {
                    array_shift($matches);
                    $resizedImagePath = $rootPath . DS . implode('', $matches) . $relativeImagePath;
                    if (file_exists($resizedImagePath)) {
                        unlink($resizedImagePath);
                    }
                }
            }
        }
        // S3: Clear original cache image
        if ($source->getType() === 'S3') {
            unlink($originalImagePath);
        }

        return;
    }
}

/**
 * Manipurate image by HTTP Request
 *
 */
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
$dirname = basename(dirname($_SERVER['SCRIPT_NAME']));
$requestUri = urldecode($_SERVER['REQUEST_URI']);
$imageUrl = preg_replace('#.+' . $dirname . '/#', '/', $requestUri);
if (is_dir($rootPath . $imageUrl)) {
    header('HTTP', true, 403);
    exit;
}

if (preg_match('#^' . DS . $dirRegex . DS . '(.+)$#', $imageUrl, $matches)) {
    $width = $matches[1];
    $height = $matches[2];
    $suffix = $matches[3];
    $originalImageKey = $matches[4];
} else {
    // S3: Create original cache image
    if ($source->getType() === 'S3') {
        $originalImageKey = preg_replace('#^' . DS . '#', '', $imageUrl);
        $cacheImagePath = $rootPath . $imageUrl;
        $path = $source->createObject($originalImageKey, $cacheImagePath);
        if (file_exists($path)) {
            header('Location: ' . $requestUri, true, 307);
            exit;
        }
    }
    header('HTTP', true, 404);
    exit;
}

// allow manipulated image cache pattern
$allow = false;
foreach ($allowCachePattern as $pattern) {
    if (preg_match('#^' . DS . $pattern . DS . '#', $imageUrl)) {
        $allow = true;
    }
}
if (!$allow) {
    header('HTTP', true, 404);
    exit;
}

$originalImagePath = $source->getPath($originalImageKey);
$resizedImagePath = $rootPath . $imageUrl;

if (!file_exists($originalImagePath)) {
    header('HTTP', true, 404);
    exit;
}

try {
    if (!is_dir(dirname($resizedImagePath))) {
        umask(0);
        $dirmode = 0755;
        if (defined('IMGIN_DIR_MODE')) {
            $dirmode = IMGIN_DIR_MODE;
        }
        $result = Util::mkdirWithDirmode(dirname($resizedImagePath), $dirmode, true);
        if (!$result) {
            throw new OutOfBoundsException('Directory permission denied');
        }
    }
    $image = $imagine->open($originalImagePath);

    if (($image->getSize()->getWidth() / $width) > ($image->getSize()->getHeight() / $height)) {
        if ($image->getSize()->getWidth() != $width) {
            $relative = new Imagine\Filter\Advanced\RelativeResize('widen', $width);
            $relative->apply($image)
                ->save($resizedImagePath);
        } else {
            copy($originalImagePath, $resizedImagePath);
        }
    } else {
        if ($image->getSize()->getHeight() != $height) {
            $relative = new Imagine\Filter\Advanced\RelativeResize('heighten', $height);
            $relative->apply($image)
                ->save($resizedImagePath);
        } else {
            copy($originalImagePath, $resizedImagePath);
        }
    }

    $filemode = 0644;
    if (defined('IMGIN_FILE_MODE')) {
        $filemode = IMGIN_FILE_MODE;
    }
    chmod($resizedImagePath, $filemode);

    header('Location: ' . $requestUri, true, 307);
} catch (Exception $e) {
    header('HTTP', true, 500);
    echo $e->getMessage();
    exit;
}
