<?php

namespace Gen;

abstract class TwigExtension extends \Twig_Extension
{
    protected $currentDirectory;

    protected $currentFile;

    public function __construct($currentDirectory, $currentFile)
    {
        $this->currentDirectory = $currentDirectory;
        $this->currentFile      = $currentFile;
    }
}
