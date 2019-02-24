<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->exclude([
        'app',
        'spec',
        'build',
        'bin',
        'web',
        'vendor',
    ])
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true
        ],
        'phpdoc_order' => true,
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals' => true
        ],
        'single_import_per_statement' => false
    ])
    ->setFinder($finder);