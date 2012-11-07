<?php

namespace Gen;

class Builder {

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
            $data->merge($entry['path'] . '/' . $this->config->get('local'));
            $data->merge($entry['path'] . '/' . $this->util->replaceExtension($entry['file'], 'php'));

            $loader = new \Twig_Loader_Filesystem([$this->config->get('src') . '/' . $this->config->get('templates'), $entry['path']]);
            $twig = new \Twig_Environment($loader);

            if (is_dir($this->config->get('src') . '/' . $this->config->get('extensions'))) {
                require_once 'Twig/ExtensionBase.php';
                foreach (glob($this->config->get('src') . '/' . $this->config->get('extensions') . '/*.php') as $file) {
                    require_once $file;
                    $extension = 'Gen\\Twig\\' . basename($file, '.php');
                    $twig->addExtension(new $extension($entry['path'], $entry['file'], $this->config, $data()));
                }
            }

            $template = $twig->loadTemplate($entry['file']);

            $path = str_replace($this->config->get('src') . '/' . $this->config->get('content'), $this->config->get('dest'), $entry['path']);
            $file = $this->util->replaceExtension($entry['file'], 'html');

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $this->util->log("Creating: $path/$file");

            file_put_contents($path . '/' . $file, $template->render($data()));
        }
    }
}
