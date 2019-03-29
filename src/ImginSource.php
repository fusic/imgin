<?php
namespace App;

interface ImginSource
{
    public function getType();
    public function getPath($key);
}