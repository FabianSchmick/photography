<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'method_chaining_indentation' => true,
        'no_useless_else' => true,
        'object_operator_without_whitespace' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'yoda_style' => false
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache') // forward compatibility with 3.x line
;
