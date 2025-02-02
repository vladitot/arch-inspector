<?php

namespace Vladitot\ArchChecker\Should;

use Nette\PhpGenerator\ClassType;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

class NotExist extends AbstractShould
{
    public function checksShouldAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
        ];
    }

    public function checkIfShouldForClass(ClassType $class, string $path, string $ruleName): ?string
    {
        return 'Class ' . $class->getName() . ' should not exist by rule "' . $ruleName.'"';
    }
}