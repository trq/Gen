<?php

namespace Gen\Indexer;

abstract class IndexerAbstract
{
    use \Gen\Twig\WorkerTrait;

    protected $validMeta = false;
    protected $util;
    protected $config;
    protected $path;
    protected $meta;

    public function __construct(\Gen\Util $util, \Gen\Config $config, $path, array $meta)
    {
        if (isset($meta['plugin']) && isset($meta['template']) && isset($meta['data'])) {
            $this->validMeta = true;
            $this->meta      = $meta;
        }
        $this->util      = $util;
        $this->config    = $config;
        $this->path      = $path;
    }

    abstract public function build();
}
