<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('var')
    ->exclude('config')
    ->notPath('tests/bootstrap.php')
    ->notPath('public/index.php')
    ->name('*.php')
    ->ignoreDotFiles(true)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        '@PHP8x2Migration' => true,
        '@PHP8x3Migration' => true,
        '@PHP8x5Migration' => true,
        'declare_strict_types' => true,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
        'php_unit_method_casing' => ['case' => 'camel_case'],
    ])
    ->setFinder($finder)
;
