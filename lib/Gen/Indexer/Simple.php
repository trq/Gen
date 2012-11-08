<?php

namespace Gen\Indexer;

class Simple extends IndexerAbstract
{
    public function process() {
        if ($this->validMeta) {

            $indexer = (new \Gen\Indexer($this->meta['data'], new \Gen\Util))->build($this->path);

            $loader = new \Twig_Loader_Filesystem([$this->config->get('src') . '/' . $this->config->get('templates'), $this->path]);
            $twig = new \Twig_Environment($loader);

            if (is_dir($this->config->get('src') . '/' . $this->config->get('extensions'))) {
                require_once 'Twig/ExtensionBase.php';
                foreach (glob($this->config->get('src') . '/' . $this->config->get('extensions') . '/*.php') as $file) {
                    require_once $file;
                    $extension = '\\Gen\\Twig\\' . basename($file, '.php');
                    $twig->addExtension(new $extension($this->path, $this->meta['template'], $this->config, []));
                }
            }

            $template = $twig->loadTemplate($this->meta['template']);
            $path     = str_replace($this->config->get('src') . '/' . $this->config->get('content'), $this->config->get('dest'), $this->path);

            file_put_contents($path . '/index.html', $template->render($indexer->get()));

        } else {
            throw new \RuntimeException('Invalid meta data');
        }
    }
}
