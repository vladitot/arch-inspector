<?php

namespace Vladitot\ArchChecker\Should;

use Nette\PhpGenerator\Method;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

class NotBeHigherNLines extends AbstractShould
{
    private int $linesLimit;

    public function __construct(int $linesLimit)
    {
        $this->linesLimit = $linesLimit;
    }

    public function checkIfShouldForMethod(Method $method, string $path, string $ruleName): ?string
    {
        $lines = explode("\n", $method->getBody());
        if (count($lines) > $this->linesLimit) {
            return 'Method ' . $method->getName() . ' should not be higher than '.$this->linesLimit.' lines by rule "' . $ruleName . '"';
        }
        return null;
    }

    public function checksShouldAllowedFor(): array
    {
        return [
            AbstractShould::ALLOWED_FOR_METHOD
        ];
    }
}