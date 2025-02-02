<?php

namespace Vladitot\ArchChecker\Filters\Abstractions;

use Nette\NotImplementedException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitType;

abstract class AbstractFilter
{
    public const ALLOWED_FOR_CLASS = 'class';

    public const ALLOWED_FOR_INTERFACE = 'interface';

    public const ALLOWED_FOR_TRAIT = 'trait';

    public const ALLOWED_FOR_METHOD = 'method';

    public const ALLOWED_FOR_NAMESPACE = 'namespace';

    abstract public function filterAllowedFor(): array;

    public function collectForSomeClass(string $path): ?ClassType {
        throw new NotImplementedException();
    }

    public function collectForSomeNamespace(string $path): ?string {
        throw new NotImplementedException();
    }

    public function collectForSomeInterface(string $path): ?InterfaceType {
        throw new NotImplementedException();
    }

    public function collectForSomeTrait(string $path): ?TraitType {
        throw new NotImplementedException();
    }

    /**
     * @param string $path
     * @return array|null|Method[]
     */
    public function collectForSomeMethod(string $path): ?array
    {
        throw new NotImplementedException();
    }
}