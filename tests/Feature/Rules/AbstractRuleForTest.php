<?php

namespace Vladitot\ArchChecker\Tests\Feature\Rules;

use Vladitot\ArchChecker\Tests\TestCase;
use Vladitot\ArchChecker\Filters\Abstractions\AbstractFilter;
use Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor;
use Vladitot\ArchChecker\Should\Abstractions\AbstractShould;

class AbstractRuleForTest extends TestCase
{

    /**
     * @covers \Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor::filter
     * @return void
     */
    public function testFilter() {
        $obj = new class extends AbstractRuleFor {

        };

        $filter = new class extends AbstractFilter {
            public function filterAllowedFor(): array
            {
                return [];
            }
        };

        $obj = $obj::filter([
            $filter
        ]);
        $this->assertEquals([$filter], $obj->filters);
    }

    /**
     * @covers \Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor::setRuleName
     * @return void
     */
    public function testSetRuleName() {
        $obj = new class extends AbstractRuleFor {

        };

        $obj->setRuleName('test');
        $this->assertEquals('test', $obj->ruleName);
    }

    /**
     * @covers \Vladitot\ArchChecker\Rules\Abstractions\AbstractRuleFor::should
     * @return void
     */
    public function testShould() {
        $obj = new class extends AbstractRuleFor {

        };

        $should = new class extends AbstractShould {

            public function checksShouldAllowedFor(): array
            {
                return [];
            }
        };

        $obj->should([$should]);
        $this->assertEquals([$should], $obj->should);
    }
}