<?php


namespace phena109;

use Exception;
use Hoa\Console\Readline\Readline;
use InvalidArgumentException;
use League\Csv\Reader;

class App
{
    private array $__connections = [];
    private string $file = ROOT . DS . 'data/paths.csv';

    public function run()
    {
        $rl = new Readline();
        do {
            $input = $rl->readLine("What is the input: ");
            echo "\n";

            try {
                $parsed = $this->parseInput($input);
                list($path, $time) = $this->process($parsed);

                echo implode(' => ', $path) . " => " . $time . "\n";
            } catch (Exception $e) {
                if (!$this->isQuit($input)) {
                    echo "Path not found\n";
                }
            }
        } while (false !== $input && !$this->isQuit($input));
    }

    private function process(array $parsed)
    {
        $connections = $this->getConnections();
        $path = ['A', 'B', 'C'];
        $time = 500;
        return [$path, $time];
    }

    private function isQuit($input): bool
    {
        return strtolower($input) == 'quit';
    }

    private function parseInput(string $input): array
    {
        $_input = strtoupper(trim($input));
        preg_match_all("/^([A-Z])\s+([A-Z])\s+([0-9]+)$/", $_input, $parsed);

        if (!count($parsed[0])) {
            throw new InvalidArgumentException('Invalid input');
        }

        return [
            'from' => $parsed[1][0],
            'to' => $parsed[2][0],
            'latency' => $parsed[3][0],
        ];
    }

    private function getConnections()
    {
        if (!count($this->__connections)) {
            $reader = Reader::createFromPath($this->file, 'r');
            $paths = $reader->getRecords();

            foreach ($paths as $path) {
                $this->__connections[] = [
                    'from' => $path[0],
                    'to' => $path[1],
                    'latency' => $path[2],
                ];
            }
        }

        return $this->__connections;
    }
}