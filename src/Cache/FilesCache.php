<?php

namespace Vladitot\ArchChecker\Cache;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\TraitType;
use PhpParser\Builder\Method;
use Vladitot\ArchChecker\Exceptions\ReadingFromFileException;

class FilesCache
{

    protected static array $cache = [];

    protected static array $pathByFullClassNamesCache = [];

//    public static function getRealEntityTypeByPath(string $path): string
//    {
//        if (isset(self::$cache[$path])) {
//            return self::$cache[$path]['type'];
//        }
//        $content = file_get_contents($path);
//        self::readToCache($content, $path);
//        return self::$cache[$path]['type'];
//    }

    /**
     * @param string $path
     * @return ClassType
     * @throws ReadingFromFileException
     */
    public static function getClassByPath(string $path): ClassType
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['class'];
        }
        $content = file_get_contents($path);
        self::readToCache($content, $path);
        return self::$cache[$path]['class'];
    }

    /**
     * Method detects if there stored class, interface, trait in file and returns it
     * @param string $path
     * @return ClassType|InterfaceType|TraitType
     */
    public static function detectEntityByPath(string $path)
    {
        if (!isset(self::$cache[$path])) {
            $content = file_get_contents($path);
            self::readToCache($content, $path);
        }
        if (isset(self::$cache[$path]['class'])) {
            return self::$cache[$path]['class'];
        } elseif (isset(self::$cache[$path]['interface'])) {
            return self::$cache[$path]['interface'];
        } elseif (isset(self::$cache[$path]['trait'])) {
            return self::$cache[$path]['trait'];
        }
        throw new \Exception('Could not detect entity in file: '.$path);
    }

    public static function getUsesByPath(string $path): array
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['uses'];
        }
        $content = file_get_contents($path);
        self::readToCache($content, $path);
        return self::$cache[$path]['uses'];
    }

    public static function getNamespaceByPath(string $path): string
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['namespace'];
        }
        $content = file_get_contents($path);
        self::readToCache($content, $path);
        return self::$cache[$path]['namespace'];
    }

    public static function getInterfaceByPath(string $path): InterfaceType
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['class'];
        }
        $content = file_get_contents($path);
        self::readToCache($content, $path);
        return self::$cache[$path]['class'];
    }

    public static function getTraitByPath(string $path): TraitType
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['class'];
        }
        $content = file_get_contents($path);
        self::readToCache($content, $path);
        return self::$cache[$path]['class'];
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @return array
     */
    private static function getAllUsesOfClassWithRegex(string $content) {
        $uses = [];
        if (preg_match_all('/use\s+(.*);/', $content, $matches)) {
            foreach ($matches[1] as $use) {
                $use = explode(' as ', $use);
                $uses[] = $use[0];
            }
        }
        return $uses;
    }

    public static function getPathByFullClassName(string $fullClassName): ?string
    {
        return self::$pathByFullClassNamesCache[trim($fullClassName, '\\')] ?? null;
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @param string $path
     * @return void
     * @throws ReadingFromFileException
     */
    private static function readToCache(string $content, string $path)
    {
        try {
            /** @var ClassType $class */
            $class = ClassType::fromCode($content);
            $methodNames = [];
            foreach ($class->getMethods() as $method) {
                $methodNames[] = $method->getName();
            }
            $namespace = self::detectNamespaceFromFileWithRegularExpression($content);
            self::$cache[$path] = [
                'class' => $class,
                'type' => 'class',
                'namespace' => $namespace,
                'methods' => $methodNames,
                'uses' => self::getAllUsesOfClassWithRegex($content),
            ];
            self::$pathByFullClassNamesCache[$namespace.'\\'.$class->getName()] = $path;
            return;
        } catch (\Exception $e) {
        }
        try {
            $interface = InterfaceType::fromCode($content);
            self::$cache[$path] = [
                'class' => $interface,
                'type' => 'interface',
                'namespace' => self::detectNamespaceFromFileWithRegularExpression($content)
            ];
            return;
        } catch (\Exception $e) {
        }

        try {
            $trait = TraitType::fromCode($content);
            self::$cache[$path] = [
                'class' => $trait,
                'type' => 'trait',
                'namespace' => self::detectNamespaceFromFileWithRegularExpression($content)
            ];
            return;
        } catch (\Exception $e) {
        }

        throw new ReadingFromFileException('Could not read info from file: '.$path);
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @return string|null
     */
    private static function detectNamespaceFromFileWithRegularExpression(string $content) {
        $namespace = null;
        if (preg_match('/namespace\s+(.*);/', $content, $matches)) {
            $namespace = $matches[1];
        }
        return $namespace;
    }

    /**
     * @param string $path
     * @return array|Method[]
     */
    public static function getMethodsByPath(string $path): array
    {
        $entity = self::detectEntityByPath($path);
        if ($entity instanceof ClassType) {
            return $entity->getMethods();
        } elseif ($entity instanceof InterfaceType) {
            return $entity->getMethods();
        } elseif ($entity instanceof TraitType) {
            return $entity->getMethods();
        }
        throw new \Exception('Could not get methods from entity in a file: '.$path);
    }

    public static function getMethodByPathAndName(string $path, string $methodName): ?Method
    {
        $methods = self::getMethodsByPath($path);
        foreach ($methods as $method) {
            if ($method->getName() === $methodName) {
                return $method;
            }
        }
        return null;
    }
}