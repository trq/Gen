<?php

namespace Gen;

class Data
{
    protected $data = [];

    public function merge($path)
    {
        if (file_exists($path)) {
            $this->data = array_merge($this->data, (array) include $path);
        }
    }

    public function __invoke()
    {
        return $this->data;
    }
}
