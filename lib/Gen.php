<?php

namespace Gen;

class Gen {

    protected $verbose;

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
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

                if ($this->verbose) {
                    echo "Copying: $pathDir => $destination/$readdirectory\n";
                }
                copy($pathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();

        } else {
            if ($this->verbose) {
                echo "Copying: $source => $destination\n";
            }
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

    public function build($src, $destination) {

        $data = [];

        if (file_exists($src . '/global.php')) {
            $data = (array) include $src . '/global.php';
        } else {
            $data = [];
        }

        if (file_exists(realpath(__DIR__) . '/../../../autoload.php')) {
            require_once realpath(__DIR__) . '/../../../autoload.php';
        } else if (file_exists(realpath(__DIR__) . '/../vendor/autoload.php')) {
            require_once realpath(__DIR__) . '/../vendor/autoload.php';
        } else {
            throw new \RuntimeException('composer autoloader not found - unabled to load dependencies.');
        }

        if (!is_dir($destination)) {
            if ($this->verbose) {
                echo "Creating: $destination\n";
            }
            mkdir($destination);
        }

        if (is_dir($src . '/assets')) {
            $this->cp($src . '/assets', $destination . '/assets');
        }

        foreach ($this->scan($src . '/content') as $entry) {
            if (pathinfo($entry['file'], PATHINFO_EXTENSION) == 'twig') {
                $loader = new \Twig_Loader_Filesystem([$src . '/templates', $entry['path']]);
                $twig = new \Twig_Environment($loader);

                if (is_dir($src . '/extensions')) {
                    require_once 'TwigExtension.php';
                    foreach (glob($src . '/extensions/*.php') as $file) {
                        require_once $file;
                        $extension = 'Gen\\' . basename($file, '.php');
                        $twig->addExtension(new $extension($entry['path'], $entry['file']));
                    }
                }

                $template = $twig->loadTemplate($entry['file']);

                $path = str_replace($src . '/content', $destination, $entry['path']);
                $file = $this->replaceExtension($entry['file'], 'html');

                $local = $entry['path'] . '/local.php';

                if (file_exists($local)) {
                    $data = array_merge($data, (array) include $local);
                }

                $phpFile = $entry['path'] . '/' . $this->replaceExtension($entry['file'], 'php');

                if (file_exists($phpFile)) {
                    $data = array_merge($data, (array) include $phpFile);
                }

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                if ($this->verbose) {
                    echo "Creating: $path/$file\n";
                }

                file_put_contents($path . '/' . $file, $template->render($data));
            }
        }

    }
}