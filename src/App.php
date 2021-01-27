<?php


namespace phena109;

use Iterator;
use League\Csv\Reader;

class App
{
    private Iterator $__paths;
    private string $file = ROOT . DS . 'data/paths.csv';

    public function run()
    {
        $paths = $this->getPaths();

        foreach ($paths as $path) {
            echo implode(',', $path) . "\n";
        }
    }

    private function getPaths(): Iterator
    {
        if (!isset($this->__paths)) {
            $reader = Reader::createFromPath($this->file, 'r');
            $this->__paths = $reader->getRecords();
        }

        return $this->__paths;
    }
}