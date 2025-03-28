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
        $gotNamespace = $this->filesCache->getNamespaceByPath($path)??'';
        $classNamespace = trim($gotNamespace, '\\');
        if (str_starts_with($classNamespace, trim($this->namespace, '\\'))) {
            return $this->filesCache->getClassByPath($path);
        }
        return null;
    }

    public function collectForSomeInterface(string $path): ?InterfaceType
    {
        $classNamespace = trim($this->filesCache->getNamespaceByPath($path)??'', '\\');
        if (str_starts_with($classNamespace, trim($this->namespace, '\\'))) {
            return $this->filesCache->getInterfaceByPath($path);
        }
        return null;
    }

    public function collectForSomeNamespace(string $path): ?string
    {
        $classNamespace = trim($this->filesCache->getNamespaceByPath($path)??'', '\\');
        if (str_starts_with($classNamespace, trim($this->namespace, '\\'))) {
            return $classNamespace;
        }
        return null;
    }

    public function collectForSomeTrait(string $path): ?TraitType
    {
        $classNamespace = trim($this->filesCache->getNamespaceByPath($path)??'', '\\');
        if (str_starts_with($classNamespace, trim($this->namespace, '\\'))) {
            return $this->filesCache->getTraitByPath($path);
        }
        return null;
    }

    public function collectForSomeMethod(string $path): ?array
    {
        $entity = $this->filesCache->detectEntityByPath($path);
        $classNamespace = trim($this->filesCache->getNamespaceByPath($path)??'', '\\');
        if (str_starts_with($classNamespace, trim($this->namespace, '\\'))) {
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