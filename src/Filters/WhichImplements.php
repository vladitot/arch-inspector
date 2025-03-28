<?php

namespace Vladitot\ArchChecker\Filters;

use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;

class WhichImplements extends AbstractFilter
{
    public function filterAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
            self::ALLOWED_FOR_INTERFACE,
            self::ALLOWED_FOR_TRAIT
        ];
    }
}