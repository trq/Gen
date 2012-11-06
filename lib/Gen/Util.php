<?php

namespace Gen;

class Util
{
    protected $verbose;

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
    }

    public function log($msg)
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

    public function scan($path = '', $extension = null, &$name = array())
    {
        $path = $path == '' ? dirname(__FILE__) : $path;
        $lists = scandir($path);

        if (!empty($lists)) {
            foreach($lists as $file) {

                if (is_dir($path . '/' . $file) && $file != '.' & $file != '..') {
                    $this->scan($path . '/' . $file, $extension, $name);
                } else {
                    if ($extension !== null) {
                        if (is_file($path . '/' . $file) && pathinfo($path . '/' . $file)['extension'] == $extension) {
                            $name[] = ['path' => $path, 'file' => $file];
                        }
                    } else {
                        if (is_file($path . '/' . $file)) {
                            $name[] = ['path' => $path, 'file' => $file];
                        }
                    }
                }
            }
      }

      return $name;

    }

    public function replaceExtension($filename, $extension) {
         return pathinfo($filename)['filename'] . '.' . $extension;
    }
}
