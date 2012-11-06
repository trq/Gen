<?php

namespace Gen;

class Blog
{
    protected $util;

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    public function getIndex($dir, $skip = []) {

        $out = ['blogs' => []];

        foreach ($this->util->scan($dir, 'twig') as $entry) {
            if (!in_array($entry['file'], $skip)) {
                if (preg_match('#content(.*+)#', $entry['path'], $results)) {

                    $meta = [];
                    $metaFile = $this->util->replaceExtension($entry['file'], 'meta.php');
                    if (file_exists($entry['path'] . '/' . $metaFile)) {
                        $meta = (array) include $entry['path'] . '/' . $metaFile;
                    }

                    $partUrl = $results[1];
                    $file = $this->util->replaceExtension($entry['file'], 'html');
                    $title = ucfirst(str_replace('-', ' ', $this->util->replaceExtension($file, '')));

                    $out['blogs'][] = [
                        'title' => $title,
                        'path' => $path . '/' . $file
                    ];
                }
            }
        }
        return $out;
    }
}
