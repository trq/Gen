<?php

namespace Gen\Indexer;

class Simple extends IndexerAbstract
{
    public function build(array $skip = []) {
        if ($this->validMeta) {

            $indexer = (new \Gen\Indexer($this->meta['data'], new \Gen\Util))->build($this->path, $skip, true);

            $twig = $this->getTwig(
                $this->config,
                $this->path,
                $this->meta['template'],
                $indexer->get()
            );

            $output = $this->writeHtmlFromTwig(
                $twig,
                $this->config,
                $this->path,
                $this->meta['template'],
                'index.html',
                $indexer->get()
            );
            $this->util->log("Creating: $output");

        } else {
            throw new \RuntimeException('Invalid meta data');
        }
        return $this;
    }
}
