<?php

namespace DanBallance\OasDocs\Components;

class LocalFileManager implements LocalFileInterface
{
    protected $basePath;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ? $basePath : '/';
    }

    public function getMarkdown(string $id) : string
    {
        $filepath = "{$this->basePath}{$this->pathFromId($id)}";
        return file_get_contents($filepath);
    }

    protected function pathFromId(string $id) : string
    {
        $replaceCount = max(0, substr_count($id, '.') - 1);
        if ($replaceCount > 0) {
            return str_replace('.', '/', $id, $replaceCount);
        }
        return $id;
    }
}
