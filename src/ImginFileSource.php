<?php
namespace App;

use App\ImginSource;

// File
class ImginFileSource implements ImginSource
{
    private $rootPath;
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }
    public function getType()
    {
        return 'File';
    }
    public function getPath($key)
    {
        return $this->rootPath . DS . $key;
    }
}