<?php


$finder = PhpCsFixer\Finder::create()
    ->in(array(__DIR__.'/Translator', __DIR__.'/Tests',__DIR__.'/DependencyInjection'))
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'array_syntax' => array('syntax' => 'short'),
    ))
    ->setFinder($finder)
;
