<?php

namespace Gen;

abstract class TwigExtension extends \Twig_Extension
{
    protected $currentDirectory;
    protected $currentFile;
    protected $ops;
    protected $data;

    public function __construct($currentDirectory, $currentFile, $ops, $data)
    {
        $this->currentDirectory = $currentDirectory;
        $this->currentFile      = $currentFile;
        $this->ops              = $ops;
        $this->data             = $data;
    }
}
