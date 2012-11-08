<?php

namespace Gen;

class Indexer
{
    protected $name;
    protected $util;
    protected $index = [];

    public function __construct($name, Util $util)
    {
        $this->name  = $name;
        $this->util  = $util;
        $this->index = [$this->name => []];
    }

    public function build($dir, $skip = []) {
        foreach ($this->util->scan($dir, 'twig') as $entry) {
            if (!in_array($entry['file'], $skip)) {
                /**
                 * TODO: This hard coded *content* must be removed.
                 */
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

                    $this->index[$this->name][] = [
                        'title'   => $title,
                        'url'     => $partUrl . '/' . $file,
                        'path'    => $entry['path'],
                        'file'    => $entry['file'],
                        'content' => function() { return file_get_contents($entry['path']); },
                        'meta'    => $meta
                    ];
                }
            }
        }
        return $this;
    }

    public function get()
    {
        return $this->index;
    }
}
