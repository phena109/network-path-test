<?php


namespace phena109;

use Exception;
use Hoa\Console\Readline\Readline;
use InvalidArgumentException;
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

        echo "\n";

        $this->process();
    }

    private function process()
    {
        $rl = new Readline();
        do {
            $input = $rl->readLine("What is the input: ");
            echo "\n";

            try {
                $parsed = $this->parseInput($input);
                echo implode(' ', $parsed) . "\n";
            } catch (Exception $e) {
                echo "\nPath not found\n";
            }
        } while (false !== $input && 'quit' !== strtolower($input));
    }

    private function parseInput(string $input)
    {
        $_input = strtoupper(trim($input));
        preg_match_all("/^([A-Z]\s+[A-Z]\s+[0-9]+)$/", $_input, $parsed);

        if (count($parsed) != 2) {
            throw new InvalidArgumentException('Invalid input');
        }
        if (count($parsed[1]) == 0) {
            throw new InvalidArgumentException('Invalid input');
        }

        return $parsed[1];
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