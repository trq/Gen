<?php

namespace Gen;

class Builder {
    use \Gen\Twig\WorkerTrait;

    protected $util;
    protected $config;

    public function __construct(Config $config, Util $util)
    {
        $this->util   = $util;
        $this->config = $config;
    }

    public function build($src, $dest = null) {

        $data = [];

        $this->config->init($src, $dest);

        // Make sure any $dest passed to build() takes priority over
        // anything defined within any config file. Loaded in init().
        if ($dest !== null) {
            $this->config->set('dest', $dest);
        }
        $data = new Data;
        $data->merge($this->config->get('src') . '/' . $this->config->get('global'));

        if (!is_dir($this->config->get('dest'))) {
            $this->util->log("Creating: {$this->config->get('dest')}");
            mkdir($this->config->get('dest'));
        }

        foreach ($this->config->get('assets') as $assetDir) {
            if (is_dir($this->config->get('src') . '/' . $assetDir)) {
                $this->util->cp($this->config->get('src') . '/' . $assetDir, $this->config->get('dest') . '/' . $assetDir);
            }
        }

        foreach ($this->util->scan($this->config->get('src') . '/' . $this->config->get('content'), 'twig') as $entry) {
            if (file_exists($entry['path'] . '/indexer.php')) {
                $indexer_meta = (array) include $entry['path'] . '/indexer.php';
                if (isset($indexer_meta['plugin'])) {
                    $plugin  = $indexer_meta['plugin'];
                    $indexer = new $plugin($this->util, $this->config, $entry['path'], $indexer_meta);
                    if ($indexer instanceof \Gen\Indexer\IndexerAbstract) {
                        $indexer->build(['indexer.php']);
                    }
                }
                continue;
            }

            $data->merge($entry['path'] . '/' . $this->config->get('local'));
            $data->merge($entry['path'] . '/' . $this->util->replaceExtension($entry['file'], 'php'));

            $twig = $this->getTwig(
                $this->config,
                $entry['path'],
                $entry['file'],
                $data()
            );

            $output = $this->writeHtmlFromTwig(
                $twig,
                $this->config,
                $entry['path'],
                $entry['file'],
                $this->util->replaceExtension($entry['file'], 'html'),
                $data()
            );
            $this->util->log("Creating: $output");
        }
    }
}
