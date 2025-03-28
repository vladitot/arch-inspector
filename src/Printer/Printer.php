<?php

namespace Vladitot\ArchChecker\Printer;

class Printer
{
    public const MODE_CONSOLE = 'console';

    public const MODE_BASELINE = 'baseline';

    public string $mode = self::MODE_CONSOLE;

    public bool $testMode = false;

    /**
     * @codeCoverageIgnore
     * @param string $mode
     * @param bool $testMode
     */
    public function __construct(string $mode = self::MODE_CONSOLE, bool $testMode = false)
    {
        $this->mode = $mode;
        $this->testMode = $testMode;
    }

    public function resetBaseline() {
        if ($this->mode === self::MODE_BASELINE) {
            file_put_contents('baseline.json', '[]');
        }
    }

    public function printError(string $error): bool
    {
        if (!file_exists('baseline.json')) {
            file_put_contents('baseline.json', '[]');
        }
        $currentBaseline = json_decode(file_get_contents('baseline.json'), true);
        if ($this->mode === self::MODE_CONSOLE) {
            if (in_array($error, $currentBaseline)) {
                return false;
            }
            if (!$this->testMode) {
                echo $error . PHP_EOL;
            }
            return true;
        } elseif ($this->mode === self::MODE_BASELINE) {
            $currentBaseline[] = $error;
            file_put_contents('baseline.json', json_encode($currentBaseline));
            return true;
        } else {
            throw new \Exception('Unknown Printer mode '.$this->mode);
        }
    }

}