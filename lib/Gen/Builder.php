<?php

namespace Gen;

class Builder {

    protected $util;

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    public function build($src, $dest = null) {

        $data = [];

        if ($dest === null) {
            $dest = $src . '/build';
        }

        $ops = [
            'extensions'    => 'extensions',
            'content'       => 'content',
            'templates'     => 'templates',
            'assets'        => ['assets'],
            'global'        => 'global.php',
            'local'         => 'local.php',
            'src'           => $src,
            'dest'          => $dest
        ];

        if (file_exists($ops['src'] . '/gen.conf.php')) {
            $ops = array_merge($ops, (array) include $ops['src'] . '/gen.conf.php');
        }

        if (file_exists($ops['src'] . '/' . $ops['global'])) {
            $data = (array) include $ops['src'] . '/' . $ops['global'];
        } else {
            $data = [];
        }

        if (!is_dir($ops['dest'])) {
            $this->util->log("Creating: {$ops['dest']}");
            mkdir($ops['dest']);
        }

        foreach ($ops['assets'] as $assetDir) {
            if (is_dir($ops['src'] . '/' . $assetDir)) {
                $this->util->cp($ops['src'] . '/' . $assetDir, $ops['dest'] . '/' . $assetDir);
            }
        }

        foreach ($this->util->scan($ops['src'] . '/' . $ops['content']) as $entry) {
            if (pathinfo($entry['file'], PATHINFO_EXTENSION) == 'twig') {

                $local = $entry['path'] . '/' . $ops['local'];

                if (file_exists($local)) {
                    $data = array_merge($data, (array) include $local);
                }

                $phpFile = $entry['path'] . '/' . $this->util->replaceExtension($entry['file'], 'php');

                if (file_exists($phpFile)) {
                    $data = array_merge($data, (array) include $phpFile);
                }

                $loader = new \Twig_Loader_Filesystem([$ops['src'] . '/' . $ops['templates'], $entry['path']]);
                $twig = new \Twig_Environment($loader);

                if (is_dir($ops['src'] . '/' . $ops['extensions'])) {
                    require_once 'Twig/ExtensionBase.php';
                    foreach (glob($ops['src'] . '/' . $ops['extensions'] . '/*.php') as $file) {
                        require_once $file;
                        $extension = 'Gen\\Twig\\' . basename($file, '.php');
                        $twig->addExtension(new $extension($entry['path'], $entry['file'], $ops, $data));
                    }
                }

                $template = $twig->loadTemplate($entry['file']);

                $path = str_replace($ops['src'] . '/' . $ops['content'], $ops['dest'], $entry['path']);
                $file = $this->util->replaceExtension($entry['file'], 'html');

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $this->util->log("Creating: $path/$file");

                file_put_contents($path . '/' . $file, $template->render($data));
            }
        }

    }
}
