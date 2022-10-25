<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => array('syntax' => 'short'),
        'no_unreachable_default_argument_value' => false,
        'braces' => array('allow_single_line_closure' => true),
        'heredoc_to_nowdoc' => false,
        'phpdoc_annotation_without_dot' => false,
        'php_unit_test_annotation' => ['style' => 'annotation'],
        'php_unit_method_casing' => false,
        'psr_autoloading' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->notPath('conf/')
            ->notPath('tmp/')
            ->notPath('node_modules/')    
            ->notPath('var/cache')
            ->notPath('vendor/')
            ->notPath('www')
    )
;
