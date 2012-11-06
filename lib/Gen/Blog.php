<?php

namespace Gen;

class Blog
{
    protected $util;
    protected $index = [];

    public function __construct(Util $util)
    {
        $this->util  = $util;
        $this->index = ['blogs' => []];
    }

    public function buildIndex($dir, $skip = []) {
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

                    if (isset($meta['title'])) {
                        $title = $meta['title'];
                    } else {
                        $title = ucfirst(str_replace('-', ' ', $this->util->replaceExtension($file, '')));
                    }

                    $this->index['blogs'][] = [
                        'title' => $title,
                        'url' => $partUrl . '/' . $file,
                        'path' => $entry['path'],
                        'file' => $entry['file'],
                        'meta' => $meta
                    ];
                }
            }
        }
        return $this;
    }

    public function getIndex()
    {
        return $this->index;
    }
}
