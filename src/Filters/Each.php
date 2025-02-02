<?php

namespace Vladitot\ArchChecker\Filters;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitType;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;

class Each extends AbstractFilter
{

    public function collectForSomeClass(string $path): ?ClassType {
        return FilesCache::getClassByPath($path);
    }

    public function collectForSomeNamespace(string $path): ?string {
        return FilesCache::getNamespaceByPath($path);
    }

    public function collectForSomeInterface(string $path): ?InterfaceType {
        return FilesCache::getInterfaceByPath($path);
    }

    public function collectForSomeTrait(string $path): ?TraitType {
        return FilesCache::getTraitByPath($path);
    }

    public function collectForSomeMethod(string $path): ?array
    {
        return FilesCache::getMethodsByPath($path);
    }

    public function filterAllowedFor(): array
    {
        return [
            self::ALLOWED_FOR_CLASS,
            self::ALLOWED_FOR_INTERFACE,
            self::ALLOWED_FOR_TRAIT,
            self::ALLOWED_FOR_NAMESPACE,
            self::ALLOWED_FOR_METHOD
        ];
    }
}