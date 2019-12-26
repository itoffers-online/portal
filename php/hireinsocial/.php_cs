<?php

$fileHeaderComment = <<<COMMENT
This file is part of the Hire in Social project.

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
        'declare_strict_types' => true
    ])
    ->setFinder($finder);