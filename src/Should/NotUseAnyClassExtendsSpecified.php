<?php

namespace Vladitot\ArchChecker\Should;

use Nette\PhpGenerator\ClassType;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

class NotUseAnyClassExtendsSpecified extends AbstractShould
{

    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function checksShouldAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
            self::ALLOWED_FOR_INTERFACE,
            self::ALLOWED_FOR_TRAIT
        ];
    }

    public function checkIfShouldForClass(ClassType $class, string $path, string $ruleName): ?string
    {
        $uses = FilesCache::getUsesByPath($path);
        foreach ($uses as $use) {
            $usePath = FilesCache::getPathByFullClassName($use);
            if (!$usePath) {
                continue;
            }
            $class = FilesCache::getClassByPath($usePath);
            if (trim($class->getExtends(), '\\') === trim($this->className, '\\')) {
                return "Class {$class->getName()} should not use any childrens({$use}) of class {$this->className} by rule: \"{$ruleName}\"";
            }
        }

        return null;
    }
}