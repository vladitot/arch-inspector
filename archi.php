<?php

return [
    \Vladitot\ArchChecker\Rules\RuleForSomeNamespace::filter([
        new \Vladitot\ArchChecker\Filters\Each(),
    ])
        ->should([
            new \Vladitot\ArchChecker\Should\NotExist(),
        ])
        ->setRuleName('My Personal Rule Name'),
];