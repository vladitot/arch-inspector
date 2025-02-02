<?php

namespace Vladitot\ArchChecker\Tests\Feature\Filters;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;
use Vladitot\ArchChecker\Filters\UnderNamespace;
use Vladitot\ArchChecker\Filters\WithName;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Should\NotExist;
use Vladitot\ArchChecker\Tests\Support\Saver;

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

        $exitCode = ArchManager::checkEntity($rule, getcwd().'/testEnv', true);
        $this->assertEquals(1, $exitCode);

        $rule = RuleForSomeClass::filter([
            new UnderNamespace('SomeNamespace\SomeNamespace2\ASpace'),
            new WithName('C'),
        ])->should([
            new NotExist()
        ])->setRuleName('C should not exist under SomeNamespace\SomeNamespace2\ASpace');

        $exitCode = ArchManager::checkEntity($rule, getcwd().'/testEnv', true);
        $this->assertEquals(0, $exitCode);
    }
}