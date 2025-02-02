<?php

namespace Vladitot\ArchChecker\Filters;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\TraitType;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;

class UnderNamespace extends AbstractFilter
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function collectForSomeClass(string $path): ?ClassType
    {
        $classNamespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        if ($classNamespace===trim($this->namespace, '\\')) {
            return FilesCache::getClassByPath($path);
        }
        return null;
    }

    public function collectForSomeInterface(string $path): ?InterfaceType
    {
        $classNamespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        if ($classNamespace===trim($this->namespace, '\\')) {
            return FilesCache::getInterfaceByPath($path);
        }
        return null;
    }

    public function collectForSomeNamespace(string $path): ?string
    {
        $classNamespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        if ($classNamespace===trim($this->namespace, '\\')) {
            return $classNamespace;
        }
        return null;
    }

    public function collectForSomeTrait(string $path): ?TraitType
    {
        $classNamespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        if ($classNamespace===trim($this->namespace, '\\')) {
            return FilesCache::getTraitByPath($path);
        }
        return null;
    }

    public function collectForSomeMethod(string $path): ?array
    {
        $entity = FilesCache::detectEntityByPath($path);
        $classNamespace = trim(FilesCache::getNamespaceByPath($path), '\\');
        if ($classNamespace===trim($this->namespace, '\\')) {
            return $entity->getMethods();
        }
        return null;
    }

    public function filterAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
        ];
    }
}