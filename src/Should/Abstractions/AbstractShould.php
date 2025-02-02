<?php

namespace Vladitot\ArchChecker\Should\Abstractions;
use Nette\NotImplementedException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitType;

abstract class AbstractShould
{

    public const ALLOWED_FOR_CLASS = 'class';

    public const ALLOWED_FOR_INTERFACE = 'interface';

    public const ALLOWED_FOR_TRAIT = 'trait';

    public const ALLOWED_FOR_METHOD = 'method';

    public const ALLOWED_FOR_NAMESPACE = 'namespace';

    public function checkIfShouldForClass(ClassType $class, string $path, string $ruleName): ?string {
        throw new NotImplementedException();
    }

    public function checkIfShouldForNamespace(string $namespace, string $path, string $ruleName): ?string {
        throw new NotImplementedException();
    }

    public function checkIfShouldForInterface(InterfaceType $interface, string $path, string $ruleName): ?string {
        throw new NotImplementedException();
    }

    public function checkIfShouldForTrait(TraitType $trait, string $path, string $ruleName): ?string {
        throw new NotImplementedException();
    }

    public function checkIfShouldForMethod(Method $method, string $path, string $ruleName): ?string {
        throw new NotImplementedException();
    }

    abstract public function checksShouldAllowedFor(): array;
}