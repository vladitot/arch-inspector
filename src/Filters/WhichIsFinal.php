<?php

namespace Vladitot\ArchChecker\Filters;

use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;

class WhichIsFinal extends AbstractFilter
{

    public function filterAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS
        ];
    }
}