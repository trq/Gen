<?php

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
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $pathDir = $source . '/' . $readdirectory;
                if (is_dir($pathDir)) {
                    $this->cp( $pathDir, $destination . '/' . $readdirectory);
                    continue;
                }

                if ($this->verbose) {
                    echo "Copying: $pathDir => $destination/$readdirectory\n";
                }
                copy($pathDir, $destination . '' . $readdirectory);
            }
            $directory->close();

        } else {
            if ($this->verbose) {
                echo "Copying: $source => $destination\n";
            }
            copy($source, $destination);
        }
    }

    public function scan($path = '', &$name = array() )
    {
        $path = $path == '' ? dirname(__FILE__) : $path;
        $lists = scandir($path);

        if (!empty($lists)) {
            foreach($lists as $file) {

                if (is_dir($path . '/' . $file) && $file != '.' & $file != '..') {
                    //$this->scan($path . '/' . $file, $name);
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

        require dirname(__FILE__) . '/../vendor/autoload.php';

        $loader = new Twig_Loader_Filesystem([$src . '/templates', $src . '/content']);
        $twig = new Twig_Environment($loader);

        foreach ($this->scan($src . '/content') as $entry) {
            $template = $twig->loadTemplate($entry['file']);

            $path = str_replace($src . '/content', $destination, $entry['path']);
            $file = $this->replaceExtension($entry['file'], 'html');

            if (!is_dir($destination)) {
                if ($this->verbose) {
                    echo "Creating: $destination\n";
                }
                mkdir($destination);
            }

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $this->cp($src . '/assets', $destination . '/assets');

            if ($this->verbose) {
                echo "Creating: $path/$file\n";
            }

            file_put_contents($path . '/' . $file, $template->render([]));
        }

    }
}
