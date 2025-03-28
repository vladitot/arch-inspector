<?php

namespace Vladitot\ArchChecker\Tests\Feature\Filters;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Vladitot\ArchChecker\Factories\ArchManagerFactory;
use Vladitot\ArchChecker\Filters\Each;
use Vladitot\ArchChecker\Tests\TestCase;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Rules\RuleForSomeNamespace;
use Vladitot\ArchChecker\Tests\Support\Saver;

/**
 * @covers \Vladitot\ArchChecker\Filters\Each
 */
class EachTest extends TestCase
{

    private function prepareTestCollectForSomeClass()
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


    public function testCollectForSomeClass()
    {
        $this->prepareTestCollectForSomeClass();

        $rule = RuleForSomeClass::filter([
            new Each(),
        ]);
        $rule->setRuleName('A should not exist under SomeNamespace\SomeNamespace2\ASpace');
        $results = $this->manager->search($rule, getcwd() . '/testEnv');
        $expectedArray = [
            'SomeNamespace/SomeNamespace2/ASpace/A.php',
            'SomeNamespace/SomeNamespace2/ASpace/B.php',
            'SomeNamespace/SomeNamespace2/CSpace/C.php'
        ];
        foreach ($results as $fullClassName => $result) {
            $this->assertInstanceOf(ClassType::class, $result);
            if (in_array($fullClassName, $expectedArray)) {
                $key = array_search($fullClassName, $expectedArray);
                unset($expectedArray[$key]);
            }
        }
        $this->assertEmpty($expectedArray);
    }

    private function prepareTestCollectForSomeNamespace()
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


    public function testCollectForSomeNamespace()
    {
        $this->prepareTestCollectForSomeNamespace();

        $rule = RuleForSomeNamespace::filter([
            new Each(),
        ])->setRuleName('Get all classes under each namespace');
        $results = $this->manager->search($rule, getcwd() . '/testEnv');
        $expectedArray = [
            'SomeNamespace/SomeNamespace2/ASpace/A.php',
            'SomeNamespace/SomeNamespace2/ASpace/B.php',
            'SomeNamespace/SomeNamespace2/CSpace/C.php'
        ];
        foreach ($results as $fullClassName => $result) {
            $this->assertIsString($result);
            if (in_array($fullClassName, $expectedArray)) {
                $key = array_search($fullClassName, $expectedArray);
                unset($expectedArray[$key]);
            }
        }
        $this->assertEmpty($expectedArray);
    }

//    public function testCollectForSomeInterface()
//    {
//
//    }
//
//    public function testCollectForSomeMethod()
//    {
//
//    }
//
//    public function testCollectForSomeTrait()
//    {
//
//    }
//
}
