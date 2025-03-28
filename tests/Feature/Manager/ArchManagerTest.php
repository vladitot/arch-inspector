<?php

namespace Vladitot\ArchChecker\Tests\Feature\Manager;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Vladitot\ArchChecker\Tests\TestCase;
use Vladitot\ArchChecker\Factories\ArchManagerFactory;
use Vladitot\ArchChecker\Filters\Each;
use Vladitot\ArchChecker\Manager\ArchManager;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Tests\Support\Saver;

/**
 * @covers \Vladitot\ArchChecker\Manager\ArchManager
 */
class ArchManagerTest extends TestCase
{
    private function prepareTestSearch()
    {
        $classA = new ClassType('A');
        $namespace = new PhpNamespace('SomeNamespace\SomeNamespace2\ASpace');
        $namespace->add($classA);
        Saver::saveNamespaceWithAClass($classA->getName(), $namespace);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSearch() {
        $this->prepareTestSearch();
        $rule = RuleForSomeClass::filter([
            new Each(),
        ]);
        $rule->setRuleName('A should not exist under SomeNamespace\SomeNamespace2\ASpace');
        $results = $this->manager->search($rule, getcwd() . '/testEnv');
        $expectedArray = [
            'SomeNamespace/SomeNamespace2/ASpace/A.php',
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
}