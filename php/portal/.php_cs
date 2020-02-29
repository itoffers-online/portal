#!/usr/bin/env php
<?php

$fileHeaderComment = <<<COMMENT
This file is part of the itoffers.online project.

(c) Norbert Orzechowicz <norbert@orzechowicz.pl>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/bin',
        __DIR__ . '/db'
    ]);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__.'/var/cache/.php_cs.cache')
    ->setRules([
        '@PSR2' => true,
        'psr4' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
        'mb_str_functions' => true,
        'ordered_imports' => true,
        'blank_line_before_statement' => true,
        'trailing_comma_in_multiline_array' => true,
        'strict_comparison' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        'return_type_declaration' => ['space_before' => 'one'],
        'class_attributes_separation' => ['elements' => ['const', 'property', 'method']],
        'declare_strict_types' => true,
        'blank_line_after_opening_tag' => true,
        'no_unused_imports' => true,
        'no_unset_on_property' => true,
        'no_null_property_initialization' => true
    ])
    ->setFinder($finder);