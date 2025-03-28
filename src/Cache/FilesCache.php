<?php

namespace Vladitot\ArchChecker\Cache;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\TraitType;
use PhpParser\Builder\Method;
use Vladitot\ArchChecker\Exceptions\ReadingFromFileException;

class FilesCache
{

    protected array $cache = [];

    protected array $pathByFullClassNamesCache = [];

    /**
     * @param string $path
     * @return ClassType|null
     */
    public function getClassByPath(string $path): ?ClassType
    {
        try {
            if (isset($this->cache[$path])) {
                $return =  $this->cache[$path]['class'] ?? null;
                return $return;
            }
            self::readToCache($path);
            $return = $this->cache[$path]['class']??null;
            return $return;
        } catch (ReadingFromFileException $e) {
            echo $e->getMessage().PHP_EOL;
            return null;
        }
    }

    /**
     * Method detects if there stored class, interface, trait in file and returns it
     * @param string $path
     * @return ClassType|InterfaceType|TraitType
     * @throws ReadingFromFileException
     */
    public function detectEntityByPath(string $path)
    {
        if (!isset($this->cache[$path])) {
            self::readToCache($path);
        }
        if (isset($this->cache[$path]['class'])) {
            return $this->cache[$path]['class'];
        } elseif (isset($this->cache[$path]['interface'])) {
            return $this->cache[$path]['interface'];
        } elseif (isset($this->cache[$path]['trait'])) {
            return $this->cache[$path]['trait'];
        } elseif (isset($this->cache[$path]['enum'])) {
            return $this->cache[$path]['enum'];
        }
        throw new \Exception('Could not detect entity in file: '.$path);
    }

    public function getUsesByPath(string $path): array
    {
        try {
            if (isset($this->cache[$path])) {
                return $this->cache[$path]['uses']??[];
            }
            self::readToCache($path);
            return $this->cache[$path]['uses']??[];
        } catch (ReadingFromFileException $e) {
            echo $e->getMessage() . PHP_EOL;
            return [];
        }
    }

    public function getNamespaceByPath(string $path): ?string
    {
        try {
            if (isset($this->cache[$path])) {
                return $this->cache[$path]['namespace']??null;
            }
            self::readToCache($path);
            return $this->cache[$path]['namespace']??null;
        } catch (ReadingFromFileException $e) {
            echo $e->getMessage().PHP_EOL;
            return null;
        }
    }

    public function getInterfaceByPath(string $path): ?InterfaceType
    {
        try {
            if (isset($this->cache[$path])) {
                return $this->cache[$path]['interface']??null;
            }
            self::readToCache($path);
            return $this->cache[$path]['interface']??null;
        } catch (ReadingFromFileException $e) {
            echo $e->getMessage() . PHP_EOL;
            return null;
        }
    }

    public function getTraitByPath(string $path): ?TraitType
    {
        try {
            if (isset($this->cache[$path])) {
                return $this->cache[$path]['trait']??null;
            }
            self::readToCache($path);
            return $this->cache[$path]['trait']??null;
        } catch (ReadingFromFileException $e) {
            echo $e->getMessage() . PHP_EOL;
            return null;
        }
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @return array
     */
    private function getAllUsesOfClassWithRegex(string $content) {
        $uses = [];
        if (preg_match_all('/use\s+(.*);/', $content, $matches)) {
            foreach ($matches[1] as $use) {
                $use = explode(' as ', $use);
                $uses[] = $use[0];
            }
        }
        return $uses;
    }

    public function getPathByFullClassName(string $fullClassName): ?string
    {
        return $this->pathByFullClassNamesCache[trim($fullClassName, '\\')] ?? null;
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @return string
     * @throws ReadingFromFileException
     */
    private function detectIfFileContainsClassInterfaceOrTraitWithRegex(string $content): string
    {
        if (preg_match('/class\s+.*?{/s', $content)) {
            return 'class';
        } elseif (preg_match('/interface\s+.*?{/s', $content)) {
            return 'interface';
        } elseif (preg_match('/trait\s+.*?{/s', $content)) {
            return 'trait';
        } elseif (preg_match('/enum\s+.*?{/s', $content)) {
            return 'enum';
        }
        throw new ReadingFromFileException('Could not detect entity(class,interface,trait) in file: '.$content);
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @param string $path
     * @return void
     * @throws ReadingFromFileException
     */
    private function readToCache(string $path)
    {
        $content = file_get_contents($path);
        if (trim($content) === '') {
            throw new ReadingFromFileException('File is empty: '.$path);
        }
        $type = self::detectIfFileContainsClassInterfaceOrTraitWithRegex($content);
        if ($type==='enum') {
            /** @var EnumType $enum */
            $enum = EnumType::fromCode($content);
            $methodNames = [];
            foreach ($enum->getMethods() as $method) {
                $methodNames[] = $method->getName();
            }
            $this->cache[$path] = [
                'methods'=>$methodNames,
                'enum' => $enum,
                'type' => 'enum',
                'namespace' => self::detectNamespaceFromFileWithRegularExpression($content),
                'uses' => self::getAllUsesOfClassWithRegex($content),
            ];
            return;
        } else if ($type === 'class') {
            /** @var ClassType $class */
            $class = ClassType::fromCode($content);
            $methodNames = [];
            foreach ($class->getMethods() as $method) {
                $methodNames[] = $method->getName();
            }
            $namespace = self::detectNamespaceFromFileWithRegularExpression($content);
            $this->cache[$path] = [
                'class' => $class,
                'type' => 'class',
                'namespace' => $namespace,
                'methods' => $methodNames,
                'uses' => self::getAllUsesOfClassWithRegex($content),
            ];
            $this->pathByFullClassNamesCache[$namespace.'\\'.$class->getName()] = $path;
            return;
        } elseif ($type=='interface') {
            /** @var InterfaceType $interface */
            $interface = InterfaceType::fromCode($content);
            $methods = [];
            foreach ($interface->getMethods() as $method) {
                $methods[] = $method->getName();
            }
            $this->cache[$path] = [
                'interface' => $interface,
                'methods' => $methods,
                'type' => 'interface',
                'namespace' => self::detectNamespaceFromFileWithRegularExpression($content),
                'uses' => self::getAllUsesOfClassWithRegex($content),
            ];
            return;
        } elseif ($type=='trait') {
            /** @var TraitType $trait */
            $trait = TraitType::fromCode($content);
            $methods = [];
            foreach ($trait->getMethods() as $method) {
                $methods[] = $method->getName();
            }
            $this->cache[$path] = [
                'trait' => $trait,
                'methods' => $methods,
                'type' => 'trait',
                'namespace' => self::detectNamespaceFromFileWithRegularExpression($content),
                'uses' => self::getAllUsesOfClassWithRegex($content),
            ];
            return;
        }
        throw new ReadingFromFileException('Could not read info from file: '.$path);
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param string $content
     * @return string|null
     */
    private function detectNamespaceFromFileWithRegularExpression(string $content) {
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
    public function getMethodsByPath(string $path): array
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

    public function getMethodByPathAndName(string $path, string $methodName): ?Method
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