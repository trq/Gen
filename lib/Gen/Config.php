<?php

namespace Gen;

class Config
{
    protected $ops;

    public function init($src, $dest = null)
    {
        if ($dest === null) {
            $dest = $src . '/build';
        }

        $this->ops = [
            'extensions'    => 'extensions',
            'content'       => 'content',
            'templates'     => 'templates',
            'assets'        => ['assets'],
            'global'        => 'global.php',
            'local'         => 'local.php',
            'src'           => $src,
            'dest'          => $dest,
            'cache'         => 'cache'
        ];

        if (file_exists($this->ops['src'] . '/gen.conf.php')) {
            $this->ops = array_merge($this->ops, (array) include $this->ops['src'] . '/gen.conf.php');
        }
    }

    public function get($index, $default = null)
    {
        if (isset($this->ops[$index])) {
            return $this->ops[$index];
        }

        return $default;
    }

    public function set($index, $value)
    {
        $this->ops[$index] = $value;
        return $this;
    }
}
