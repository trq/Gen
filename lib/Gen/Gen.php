<?php

namespace Gen;

class Gen {

    protected $verbose;

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
    }

    protected function log($msg)
    {
        if ($this->verbose) {
            echo "$msg\n";
        }
    }

    public function cp( $source, $destination )
    {
        if (is_dir($source)) {
            @mkdir($destination);
            $directory = dir( $source );
            while (false !== ($readdirectory = $directory->read())) {
                if ($readdirectory == '.' || $readdirectory == '..') {
                    continue;
                }
                $pathDir = $source . '/' . $readdirectory;
                if (is_dir($pathDir)) {
                    $this->cp($pathDir, $destination . '/' . $readdirectory);
                    continue;
                }

                $this->log("Copying: $pathDir => $destination/$readdirectory");
                copy($pathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();

        } else {
            $this->log("Copying: $source => $destination");
            copy($source, $destination);
        }
    }

    public function scan($path = '', &$name = array())
    {
        $path = $path == '' ? dirname(__FILE__) : $path;
        $lists = scandir($path);

        if (!empty($lists)) {
            foreach($lists as $file) {

                if (is_dir($path . '/' . $file) && $file != '.' & $file != '..') {
                    $this->scan($path . '/' . $file, $name);
                } else {
                    if ($file != '..' && $file != '.') {
                        $name[] = ['path' => $path, 'file' => $file];
                    }
                }
            }
      }

      return $name;

    }

    public function replaceExtension($filename, $extension) {
        return preg_replace('/\..+$/', '.' . $extension, $filename);
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
            $ops = array_merge((array) include $opt['src'] . '/gen.conf.php', $ops);
        }

        if (file_exists($ops['src'] . '/' . $ops['global'])) {
            $data = (array) include $ops['src'] . '/' . $ops['global'];
        } else {
            $data = [];
        }

        if (!is_dir($ops['dest'])) {
            $this->log("Creating: {$ops['dest']}");
            mkdir($ops['dest']);
        }

        foreach ($ops['assets'] as $assetDir) {
            if (is_dir($ops['src'] . '/' . $assetDir)) {
                $this->cp($ops['src'] . '/' . $assetDir, $ops['dest'] . '/' . $assetDir);
            }
        }

        foreach ($this->scan($ops['src'] . '/' . $ops['content']) as $entry) {
            if (pathinfo($entry['file'], PATHINFO_EXTENSION) == 'twig') {

                $local = $entry['path'] . '/' . $ops['local'];

                if (file_exists($local)) {
                    $data = array_merge($data, (array) include $local);
                }

                $phpFile = $entry['path'] . '/' . $this->replaceExtension($entry['file'], 'php');

                if (file_exists($phpFile)) {
                    $data = array_merge($data, (array) include $phpFile);
                }

                $loader = new \Twig_Loader_Filesystem([$ops['src'] . '/' . $ops['templates'], $entry['path']]);
                $twig = new \Twig_Environment($loader);

                if (is_dir($ops['src'] . '/' . $ops['extensions'])) {
                    require_once 'TwigExtension.php';
                    foreach (glob($ops['src'] . '/' . $ops['extensions'] . '/*.php') as $file) {
                        require_once $file;
                        $extension = 'Gen\\' . basename($file, '.php');
                        $twig->addExtension(new $extension($entry['path'], $entry['file'], $ops, $data));
                    }
                }

                $template = $twig->loadTemplate($entry['file']);

                $path = str_replace($ops['src'] . '/' . $ops['content'], $ops['dest'], $entry['path']);
                $file = $this->replaceExtension($entry['file'], 'html');

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $this->log("Creating: $path/$file");

                file_put_contents($path . '/' . $file, $template->render($data));
            }
        }

    }
}
