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

    private function process(array $input)
    {
        $connections = $this->getConnections();

        $result = $this->find($connections, [$input['from']], 0, $input);

        if (count($result)) {
            return reset($result);
        }

        throw new Exception('No solution found');
    }

    private function find($connections, $chain, $total_time, $input): array
    {
        $output = [];
        foreach ($connections as $connection) {
            if (in_array($connection['to'], $chain)) {
                continue;
            }

            $mid = end($chain);
            if ($mid != $connection['from']) {
                continue;
            }

            $new_total_time = $total_time + $connection['latency'];
            if ($new_total_time > $input['latency']) {
                continue;
            }

            $new_chain = array_merge($chain, [$connection['to']]);

            if ($connection['to'] == $input['to']) {
                $output[] = [$new_chain, $new_total_time];
            } else {
                $output = array_merge($output,
                    $this->find($connections, $new_chain, $new_total_time,
                        $input));
            }
        }
        return $output;
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
                $this->__connections[] = [
                    'from' => $path[1],
                    'to' => $path[0],
                    'latency' => $path[2],
                ];
            }
        }

        return $this->__connections;
    }
}