<?php

namespace Vladitot\ArchChecker\Factories;

use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Printer\Printer;

class ArchManagerFactory
{
    public static function create(
        ?Printer $printer = null,
        ?FilesCache $filesCache = null
    ): ArchManager
    {
        if (!$printer) {
            $printer = new Printer();
        }
        if (!$filesCache) {
            $filesCache = new FilesCache();
        }
        return new ArchManager($printer, $filesCache);
    }
}