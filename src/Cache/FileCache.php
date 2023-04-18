<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

class FileCache
{
    /* @var $cache FilesystemTagAwareAdapter */
    public $cache;

    public function __construct(string $projectDir, string $env) {
        $this->cache = new FilesystemTagAwareAdapter(
            'CustomFilesystemCache',
            $TTL = 3600,
            $projectDir . DIRECTORY_SEPARATOR . "var/cache/ertan"
        );
    }
}