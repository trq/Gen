<?php

namespace Gen\Indexer;

abstract class IndexerAbstract
{
    protected $validMeta = false;
    protected $config;
    protected $path;
    protected $meta;

    public function __construct(\Gen\Config $config, $path, array $meta)
    {
        if (isset($meta['plugin']) && isset($meta['template']) && isset($meta['data'])) {
            $this->validMeta = true;
            $this->config    = $config;
            $this->path      = $path;
            $this->meta      = $meta;
        }
    }

    abstract public function process();
}
