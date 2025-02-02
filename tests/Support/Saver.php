<?php

namespace Vladitot\ArchChecker\Tests\Support;

use Nette\PhpGenerator\PhpNamespace;

class Saver
{
    public static function saveNamespaceWithAClass(string $className, PhpNamespace $namespace, ?string $baseDir = null): string
    {
        if ($baseDir === null) {
            $baseDir = getcwd().'/testEnv';
        }
        $pathFromNamespace = str_replace('\\', '/', $namespace->getName());
        @mkdir($baseDir.'/'.$pathFromNamespace, recursive: true);
        $path = $baseDir. '/'.$pathFromNamespace . '/' . $className . '.php';
        file_put_contents($path, "<?php\n\n" . $namespace);
        return $baseDir;
    }

    public static function removeTestEnv(?string $envPath = null): void
    {
        if ($envPath === null) {
            $envPath = getcwd().'/testEnv';
        }
        shell_exec('rm -rf '.$envPath);
    }

}