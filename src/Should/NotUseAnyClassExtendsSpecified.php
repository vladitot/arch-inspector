<?php

namespace Vladitot\ArchChecker\Should;

use Nette\PhpGenerator\ClassType;
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
        $uses = $this->filesCache->getUsesByPath($path);
        foreach ($uses as $use) {
            $usePath = $this->filesCache->getPathByFullClassName($use);
            if (!$usePath) {
                continue;
            }
            $classFromUse = $this->filesCache->getClassByPath($usePath);
            if ($classFromUse->getExtends()==null) {
                continue;
            }
            if (trim($classFromUse->getExtends(), '\\') === trim($this->className, '\\')) {
                $namespace = $this->filesCache->getNamespaceByPath($path);
                return "Class ".$namespace."\\{$class->getName()} should not use any childrens({$use}) of class {$this->className} by rule: \"{$ruleName}\"";
            }
        }

        return null;
    }
}