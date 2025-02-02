<?php

namespace Vladitot\ArchChecker\Tests\Feature\Cache;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;
use Vladitot\ArchChecker\Tests\Support\Saver;

/**
 * @covers \Vladitot\ArchChecker\Cache\FilesCache
 */
class FilesCacheTest extends TestCase
{
    private function prepareTestGetClassByPath()
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

    public function testGetClassByPath()
    {
        $this->prepareTestGetClassByPath();

        $class = \Vladitot\ArchChecker\Cache\FilesCache::getClassByPath(getcwd() . '/testEnv/SomeNamespace/SomeNamespace2/ASpace/A.php');
        $this->assertInstanceOf(ClassType::class, $class);
        $this->assertEquals('A', $class->getName());

        $class = \Vladitot\ArchChecker\Cache\FilesCache::getClassByPath(getcwd() . '/testEnv/SomeNamespace/SomeNamespace2/ASpace/B.php');
        $this->assertInstanceOf(ClassType::class, $class);
        $this->assertEquals('B', $class->getName());

    }

    private function prepareTestGetNamespaceByPath() {
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

    public function testGetNamespaceByPath() {
        $this->prepareTestGetNamespaceByPath();

        $namespace = \Vladitot\ArchChecker\Cache\FilesCache::getNamespaceByPath(getcwd() . '/testEnv/SomeNamespace/SomeNamespace2/ASpace/A.php');
        $this->assertIsString($namespace);
        $this->assertEquals('SomeNamespace\SomeNamespace2\ASpace', $namespace);

        $namespace = \Vladitot\ArchChecker\Cache\FilesCache::getNamespaceByPath(getcwd() . '/testEnv/SomeNamespace/SomeNamespace2/CSpace/C.php');
        $this->assertIsString($namespace);
    }
}