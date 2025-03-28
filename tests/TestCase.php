<?php

namespace Vladitot\ArchChecker\Tests;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Printer\Printer;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->filesCache = new FilesCache();
        $this->printer = new Printer();
        $this->printer->testMode = true;
        $this->manager = new ArchManager($this->printer, $this->filesCache);
    }
}