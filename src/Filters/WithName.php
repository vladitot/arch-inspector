<?php

namespace Vladitot\ArchChecker\Filters;

use Nette\NotImplementedException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitType;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;

class WithName extends AbstractFilter
{
    /**
     * @var string
     */
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function collectForSomeClass(string $path): ?ClassType
    {
        $class = FilesCache::getClassByPath($path);
        if ($class->getName() === $this->name) {
            return $class;
        } else {
            return null;
        }
    }

    public function collectForSomeNamespace(string $path): string
    {
        $namespace = FilesCache::getNamespaceByPath($path);
        if ($this->name=== $namespace) {
            return $namespace;
        } else {
            return '';
        }
    }

    public function collectForSomeInterface(string $path): ?InterfaceType
    {
        $interface = FilesCache::getInterfaceByPath($path);
        if ($this->name === $interface->getName()) {
            return $interface;
        } else {
            return null;
        }
    }

    public function collectForSomeTrait(string $path): ?TraitType
    {
        $trait = FilesCache::getTraitByPath($path);
        if ($this->name === $trait->getName()) {
            return $trait;
        } else {
            return null;
        }
    }

    public function collectForSomeMethod(string $path): ?array
    {
        $methods = FilesCache::getMethodsByPath($path);
        $filteredMethods = [];
        foreach ($methods as $method) {
            if ($method->getName() === $this->name) {
                $filteredMethods[] = $method;
            }
        }
        return $filteredMethods;
    }

    public function filterAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
            self::ALLOWED_FOR_INTERFACE,
            self::ALLOWED_FOR_TRAIT,
            self::ALLOWED_FOR_NAMESPACE,
            self::ALLOWED_FOR_METHOD,
        ];
    }
}