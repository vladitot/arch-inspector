<?php

namespace Vladitot\ArchChecker\Tests\Feature\Filters;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Vladitot\ArchChecker\Tests\TestCase;
use Vladitot\ArchChecker\Filters\UnderNamespace;
use Vladitot\ArchChecker\Filters\WithName;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Printer\Printer;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Should\NotExist;
use Vladitot\ArchChecker\Tests\Support\Saver;

/**
 * @covers \Vladitot\ArchChecker\Filters\UnderNamespace
 */
class UnderNamespaceFilterTest extends TestCase
{
    private function prepareForTest()
    {
        $classA = new ClassType('A');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\ASpace');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);

        $classA = new ClassType('B');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\ASpace');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);

        $classA = new ClassType('C');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\CSpace');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);

        $classA = new ClassType('D');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\DSpace\SuperDSpace');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);

    }

    /**
     * @coversNothing
     * @return void
     * @throws \Exception
     */
    public function testUnderNamespaceAndClassName() {

        $this->prepareForTest();

        $rule = RuleForSomeClass::filter([
            new UnderNamespace('SomeNamespace\SomeNamespace2\ASpace'),
            new WithName('A'),
        ])->should([
            new NotExist()
        ])->setRuleName('A should not exist under SomeNamespace\SomeNamespace2\ASpace');

        $exitCode = $this->manager->checkEntity($rule, getcwd().'/testEnv');
        $this->assertEquals(1, $exitCode);

        $rule = RuleForSomeClass::filter([
            new UnderNamespace('SomeNamespace\SomeNamespace2\ASpace'),
            new WithName('C'),
        ])->should([
            new NotExist()
        ])->setRuleName('C should not exist under SomeNamespace\SomeNamespace2\ASpace');

        $exitCode = $this->manager->checkEntity($rule, getcwd().'/testEnv');
        $this->assertEquals(0, $exitCode);
    }

    /**
     * @coversNothing
     * @return void
     * @throws \Exception
     */
    public function testUnderNamespaceDeeper() {

        $this->prepareForTest();

        $rule = RuleForSomeClass::filter([
            new UnderNamespace('SomeNamespace\SomeNamespace2'),
            new WithName('D'),
        ])->should([
            new NotExist()
        ])->setRuleName('D should not exist under SomeNamespace\SomeNamespace2');

        $exitCode = $this->manager->checkEntity($rule, getcwd().'/testEnv');
        $this->assertEquals(1, $exitCode);
    }
}