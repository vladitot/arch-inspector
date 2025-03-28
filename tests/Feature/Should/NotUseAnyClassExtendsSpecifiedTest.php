<?php

namespace Vladitot\ArchChecker\Tests\Feature\Should;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Vladitot\ArchChecker\Tests\TestCase;
use Vladitot\ArchChecker\Factories\ArchManagerFactory;
use Vladitot\ArchChecker\Filters\WithName;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Printer\Printer;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Should\NotUseAnyClassExtendsSpecified;
use Vladitot\ArchChecker\Tests\Support\Saver;

class NotUseAnyClassExtendsSpecifiedTest extends TestCase
{

    public function tearDown(): void
    {
        Saver::removeTestEnv();
    }

    private function prepareForBasicTest()
    {
        $classA = new ClassType('A');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\A');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);

        $classB = new ClassType('B');
        $classB->setExtends('SomeNamespace\SomeNamespace2\A\A');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\B');
        $namespace->add($classB);
        Saver::saveNamespaceWithAClass($classB->getName(), $namespace);

        $classC = new ClassType('C');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\C');
        $namespace->add($classC);
        $namespace->addUse('SomeNamespace\SomeNamespace2\B\B');
        Saver::saveNamespaceWithAClass($classC->getName(), $namespace);
    }

    /**
     * @coversNothing
     * @return void
     * @throws \Exception
     */
    public function testNotUseAnyClassExtendsSpecifiedTest()
    {
        $this->prepareForBasicTest();

        $enity = RuleForSomeClass::filter([
            new WithName('C'),
        ])->should([
            new NotUseAnyClassExtendsSpecified('SomeNamespace\SomeNamespace2\A\A')
        ]
        )->setRuleName('C should not use anything extends class A');

        $exitCode = $this->manager->checkEntity($enity, getcwd().'/testEnv');
        $this->assertEquals(1, $exitCode);
    }
}