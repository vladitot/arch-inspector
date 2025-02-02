<?php

namespace Vladitot\ArchChecker\Should;

use Nette\PhpGenerator\ClassType;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

class NotToBeInANamespace extends AbstractShould
{
    protected string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function checkIfShouldForClass(ClassType $class, string $path, string $ruleName): ?string
    {
        $namespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        $namespaceToCheck = trim($this->namespace, '\\');
        if ($namespace === $namespaceToCheck) {
            return "Class {$class->getName()} should not be in namespace {$namespaceToCheck} by rule: \"{$ruleName}\"";
        }
        return null;
    }

    public function checksShouldAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
            self::ALLOWED_FOR_INTERFACE,
            self::ALLOWED_FOR_TRAIT
        ];
    }
}