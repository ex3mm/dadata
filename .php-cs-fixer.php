<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                       => true,
        'strict_param'                 => true,
        'declare_strict_types'         => true,
        'ordered_imports'              => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'            => true,
        'phpdoc_align'                 => ['align' => 'left'],
        'phpdoc_separation'            => true,
        'phpdoc_trim'                  => true,
        'array_syntax'                 => ['syntax' => 'short'],
        'binary_operator_spaces'       => ['default' => 'align_single_space_minimal'],
        'blank_line_after_namespace'   => true,
        'blank_line_after_opening_tag' => true,
        'concat_space'                 => ['spacing' => 'one'],
        'no_extra_blank_lines'         => true,
        'no_trailing_whitespace'       => true,
        'single_quote'                 => true,
        'trailing_comma_in_multiline'  => ['elements' => ['arrays']],
        'final_internal_class'         => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
