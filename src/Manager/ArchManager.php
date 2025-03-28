<?php

namespace Vladitot\ArchChecker\Manager;

use Nette\PhpGenerator\Method;
use Vladitot\ArchChecker\Cache\FilesCache;
use Vladitot\ArchChecker\Printer\Printer;
use Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor;
use Vladitot\ArchChecker\Rules\RuleForSomeClass;
use Vladitot\ArchChecker\Rules\RuleForSomeInterface;
use Vladitot\ArchChecker\Rules\RuleForSomeMethod;
use Vladitot\ArchChecker\Rules\RuleForSomeNamespace;
use Vladitot\ArchChecker\Rules\RuleForSomeTrait;

class ArchManager
{
    public string $currentRuleName = '';
    private Printer $printer;
    private FilesCache $filesCache;


    /**
     * @param AbstractRuleFor $rule
     * @param string $envDir
     * @return array
     * @throws \Exception
     */
    public function search(AbstractRuleFor $rule, string $envDir = '') {
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
        $this->currentRuleName = $rule->ruleName;
        if ($rule instanceof RuleForSomeClass) {
            return $this->getByFilters($rule, 'class', 'collectForSomeClass', $envDir);
        } elseif ($rule instanceof RuleForSomeNamespace) {
            return $this->getByFilters($rule, 'namespace', 'collectForSomeNamespace', $envDir);
        } elseif ($rule instanceof RuleForSomeInterface) {
            return $this->getByFilters($rule, 'interface', 'collectForSomeInterface', $envDir);
        } elseif ($rule instanceof RuleForSomeTrait) {
            return $this->getByFilters($rule, 'trait', 'collectForSomeTrait', $envDir);
        } elseif ($rule instanceof RuleForSomeMethod) {
            return $this->getByFilters($rule, 'method', 'collectForSomeMethod', $envDir);
        }
        throw new \Exception('Unknown rule type '.get_class($rule));
    }


    /**
     * @codeCoverageIgnore
     * @param Printer $printer
     * @param FilesCache $filesCache
     */
    public function __construct(Printer $printer, FilesCache $filesCache)
    {
        $this->printer = $printer;
        $this->filesCache = $filesCache;
    }

    public function checkArrayOfRules(array $rules, string $envDir = ''): int {
        $errorCode = 0;
        foreach ($rules as $rule) {
            $newCode = $this->checkEntity($rule, $envDir);
            if ($newCode>0) {
                $errorCode = $newCode;
            }
        }
        return $errorCode;
    }

    public function checkEntity(AbstractRuleFor $rule, string $envDir = ''): int {

        if (!$envDir) {
            if (file_exists(getcwd().'/app')) {
                $envDir = getcwd().'/app';
            } elseif (file_exists(getcwd().'/src')) {
                $envDir = getcwd().'/src';
            } else {
                throw new \Exception('No env dir provided and no app or src dir found');
            }
        }
        $searched = $this->search($rule, $envDir);

        if ($rule instanceof RuleForSomeClass) {
            return $this->printErrors($this->checkIfShould($rule, $searched, 'checkIfShouldForClass', $envDir));
        } elseif ($rule instanceof RuleForSomeNamespace) {
            return $this->printErrors($this->checkIfShould($rule, $searched, 'checkIfShouldForNamespace', $envDir));
        } elseif ($rule instanceof RuleForSomeInterface) {
            return $this->printErrors($this->checkIfShould($rule, $searched, 'checkIfShouldForInterface', $envDir));
        } elseif ($rule instanceof RuleForSomeTrait) {
            return $this->printErrors($this->checkIfShould($rule, $searched, 'checkIfShouldForTrait', $envDir));
        } elseif ($rule instanceof RuleForSomeMethod) {
            return $this->printErrors($this->checkIfShould($rule, $searched, 'checkIfShouldForMethod', $envDir));
        }
        throw new \Exception('Unknown rule type '.get_class($rule));
    }

    protected function checkIfShould(AbstractRuleFor $rule, array $array, string $method, string $envDir) {
        $errors = [];
        foreach ($array as $path=>$item) {
            foreach ($rule->should as $should) {
                $should->setFilesCache($this->filesCache);
                $error = $should->$method($item, $envDir.'/'.$path, $rule->ruleName);
                if ($error) {
                    $errors[] = $error;
                }
            }
        }
        return $errors;
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param AbstractRuleFor $rule
     * @param string $allowedFor
     * @param string $methodName
     * @param string $envDir
     * @return array
     * @throws \Exception
     */
    private function getByFilters(AbstractRuleFor $rule, string $allowedFor, string $methodName, string $envDir) {
        $byFilterFoundFiles = [];
        $filterIndex = 0;
        foreach ($rule->filters as $filter) {
            foreach ($this->scanAllDir($envDir) as $filename) {
                if (!in_array($allowedFor, $filter->filterAllowedFor())) {
                    throw new \Exception('Filter '.get_class($filter).' is not allowed for '.$this->currentRuleName);
                }
                $filter->setFilesCache($this->filesCache);
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
        return $this->extractIntersectedFromAllFilters($byFilterFoundFiles);
    }

    /**
     * @codeCoverageIgnore because its just a wrapper for PHP function and it is private
     * @param $dir
     * @return array
     */
    private function scanAllDir($dir) {
        $result = [];
        foreach(scandir($dir) as $filename) {
            if ($filename[0] === '.') continue;
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach ($this->scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                if (pathinfo($filename, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }
                $result[] = $filename;
            }
        }
        return $result;
    }

    private function printErrors(array $errorsFromShould): int
    {
        if (empty($errorsFromShould)) {
//                echo 'No errors found for '.$this->$currentRuleName.PHP_EOL;
            return 0;
        } else {
            $formedExitCode = 0;
            foreach ($errorsFromShould as $error) {
                $printed = $this->printer->printError($error.PHP_EOL);
                if ($printed) {
                    $formedExitCode = 1;
                }
            }
            return $formedExitCode;
        }
    }

    /**
     * @codeCoverageIgnore because it is private
     * @param array $byFilterFoundFiles
     * @return array
     */
    private function extractIntersectedFromAllFilters(array $byFilterFoundFiles): array
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