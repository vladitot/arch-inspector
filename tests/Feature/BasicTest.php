<?php

namespace Vladitot\ArchChecker\Tests\Feature;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Filters\WithName;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Tests\Support\Saver;
use Vladitot\ArchChecker\Should\NotToBeInANamespace;

class BasicTest extends TestCase
{

    public function tearDown(): void
    {
        Saver::removeTestEnv();
    }

    /**
     * @return string
     */
    private function prepareForBasicTest()
    {
        $classA = new ClassType('A');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\Allo');
        $namespace->add($classA);
        return Saver::saveNamespaceWithAClass($classA->getName(), $namespace);
    }

    /**
     * @coversNothing
     * @return void
     * @throws \Exception
     */
    public function testBasicTest()
    {
        $this->prepareForBasicTest();

        $enity = RuleForSomeClass::filter([
            new WithName('A'),
        ])->should([
            new NotToBeInANamespace('SomeNamespace\SomeNamespace2\Allo')
        ]
        )->setRuleName('A should not be in SomeNamespace\SomeNamespace2\Allo');

        $exitCode = ArchManager::checkEntity($enity, getcwd().'/testEnv', true);
        $this->assertEquals(1, $exitCode);
    }
}