<?php

namespace Gen\Twig;

abstract class ExtensionBase extends \Twig_Extension
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
