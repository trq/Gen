<?php

namespace Gen;

class Blog
{
    protected $util;

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    public function getIndex($dir) {

        $out = ['blogs' => []];

        foreach ($this->util->scan($dir) as $entry) {
            $out['blogs'][] = $entry;
        }

        return $out;
    }
}
