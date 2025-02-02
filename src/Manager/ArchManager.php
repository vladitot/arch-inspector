<?php

namespace Vladitot\ArchChecker\Manager;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Rules\RuleForSomeInterface;
use Vladitot\ArchChecker\Rules\RuleForSomeMethod;
use Vladitot\ArchChecker\Rules\RuleForSomeNamespace;
use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;
use Vladitot\ArchChecker\Rules\RuleForSomeTrait;

class ArchManager
{
    public static string $currentRuleName = '';

    /**
     * @param AbstractRuleFor $rule
     * @param string $envDir
     * @return array
     * @throws \Exception
     */
    public static function search(AbstractRuleFor $rule, string $envDir = '') {
        if (empty($rule->ruleName)) {
            throw new \Exception('No rule name provided for '.get_class($rule));
        }
        if (!$envDir) {
            if (file_exists(getcwd().'/app')) {
                $envDir = getcwd().'/app';
            } elseif (file_exists(getcwd().'/src')) {
                $envDir = getcwd().'/src';
            } else {
                throw new \Exception('No env dir provided and no app or src dir found');
            }
        }
        self::$currentRuleName = $rule->ruleName;

        if ($rule instanceof RuleForSomeClass) {
            return self::getByFilters($rule, 'class', 'collectForSomeClass', $envDir);
        } elseif ($rule instanceof RuleForSomeNamespace) {
            return self::getByFilters($rule, 'namespace', 'collectForSomeNamespace', $envDir);
        } elseif ($rule instanceof RuleForSomeInterface) {
            return self::getByFilters($rule, 'interface', 'collectForSomeInterface', $envDir);
        } elseif ($rule instanceof RuleForSomeTrait) {
            return self::getByFilters($rule, 'trait', 'collectForSomeTrait', $envDir);
        } elseif ($rule instanceof RuleForSomeMethod) {
            return self::getByFilters($rule, 'method', 'collectForSomeMethod', $envDir);
        }
        throw new \Exception('Unknown rule type '.get_class($rule));
    }

    public static function checkEntity(AbstractRuleFor $rule, string $envDir = '', bool $disablePrinting = false): int {

        $searched = self::search($rule, $envDir);

        if ($rule instanceof RuleForSomeClass) {
            return self::printErrors(self::checkIfShould($rule, $searched, 'checkIfShouldForClass', $envDir), $disablePrinting);
        } elseif ($rule instanceof RuleForSomeNamespace) {
            return self::printErrors(self::checkIfShould($rule, $searched, 'checkIfShouldForNamespace', $envDir), $disablePrinting);
        } elseif ($rule instanceof RuleForSomeInterface) {
            return self::printErrors(self::checkIfShould($rule, $searched, 'checkIfShouldForInterface', $envDir), $disablePrinting);
        } elseif ($rule instanceof RuleForSomeTrait) {
            return self::printErrors(self::checkIfShould($rule, $searched, 'checkIfShouldForTrait', $envDir), $disablePrinting);
        } elseif ($rule instanceof RuleForSomeMethod) {
            return self::printErrors(self::checkIfShould($rule, $searched, 'checkIfShouldForMethod', $envDir), $disablePrinting);
        }
        throw new \Exception('Unknown rule type '.get_class($rule));
    }

    protected static function checkIfShould(AbstractRuleFor $rule, array $array, string $method, string $envDir) {
        $errors = [];
        foreach ($array as $path=>$item) {
            foreach ($rule->should as $should) {
                $error = $should->$method($item, $envDir.'/'.$path, $rule->ruleName);
                if ($error) {
                    $errors[] = $error;
                }
            }
        }
        return $errors;
    }

    private static function getByFilters(AbstractRuleFor $rule, string $allowedFor, string $methodName, string $envDir) {
        $byFilterFoundFiles = [];
        $filterIndex = 0;
        foreach ($rule->filters as $filter) {
            foreach (self::scanAllDir($envDir) as $filename) {
                if (!in_array($allowedFor, $filter->filterAllowedFor())) {
                    throw new \Exception('Filter '.get_class($filter).' is not allowed for '.self::$currentRuleName);
                }
                $found = $filter->$methodName($envDir.'/'.$filename);
                if ($found) {
                    if ($allowedFor==='method') {
                        foreach ($found as $methodFoundInfo) {
                            /** @var Method $method */
                            $method = $methodFoundInfo['item'];
                            $byFilterFoundFiles[$filterIndex][] = ['item'=>$method, 'path'=>$filename];
                        }
                    } else {
                        $byFilterFoundFiles[$filterIndex][] = ['item' => $found, 'path' => $filename];
                    }
                }
            }
            $filterIndex++;
        }
        return self::extractIntersectedFromAllFilters($byFilterFoundFiles);
    }

    private static function scanAllDir($dir) {
        $result = [];
        foreach(scandir($dir) as $filename) {
            if ($filename[0] === '.') continue;
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach (self::scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }
        return $result;
    }

    private static function printErrors(array $errorsFromShould, bool $disablePrinting=false): int
    {
        if (empty($errorsFromShould)) {
            if (!$disablePrinting) {
                echo 'No errors found for '.self::$currentRuleName.PHP_EOL;
            }
            return 0;
        } else {
            foreach ($errorsFromShould as $error) {
                if (!$disablePrinting) {
                    echo $error.PHP_EOL;
                }
            }
            return 1;
        }
    }

    /**
     * @param array $byFilterFoundFiles
     * @return array
     */
    public static function extractIntersectedFromAllFilters(array $byFilterFoundFiles): array
    {
        $results = [];
        //now lets get intersection of all filters by comparing '$found' as strings
        // object from iteration of $i should be found in every list of $j
        $intersection = [];
        foreach ($byFilterFoundFiles as $i => $foundFiles) {
            foreach ($foundFiles as $foundFile) {
                $found = $foundFile['item'];
                $foundInEvery = true;
                foreach ($byFilterFoundFiles as $j => $foundFiles2) {
                    if ($i === $j) {
                        continue;
                    }
                    $foundInEvery = false;
                    foreach ($foundFiles2 as $foundFile2) {
                        if ($found === $foundFile2['item']) {
                            $foundInEvery = true;
                            break;
                        }
                    }
                    if (!$foundInEvery) {
                        break;
                    }
                }
                if ($foundInEvery) {
                    $intersection[] = $foundFile;
                }
            }
        }
        foreach ($intersection as $value) {
            $results[$value['path']] = $value['item'];
        }
        return $results;
    }
}