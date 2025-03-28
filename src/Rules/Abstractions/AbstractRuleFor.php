<?php

namespace Vladitot\ArchChecker\Rules\Abstractions;

use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

abstract class AbstractRuleFor
{
    /**
     * @var AbstractFilter[]
     */
    public array $filters = [];

    /** @var AbstractShould[] */
    public array $should;
    public string $ruleName;

    /**
     * @param AbstractFilter[] $filters
     */
    public static function filter(array $filters) {
        $object = new static();
        $object->filters = $filters;
        return $object;
    }

    /**
     * @codeCoverageIgnore
     * @param AbstractShould[] $should
     * @return $this
     */
    public function should(array $should) {
        $this->should = $should;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param string $ruleName
     * @return $this
     */
    public function setRuleName(string $ruleName) {
        $this->ruleName = $ruleName;
        return $this;
    }
}