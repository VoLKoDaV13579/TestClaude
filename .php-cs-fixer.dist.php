<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/modules',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PHP84Migration' => true,

        // Strict rules
        'strict_param' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,

        // Import rules
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['const', 'class', 'function'],
        ],

        // Array rules
        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'no_trailing_comma_in_singleline' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments', 'parameters', 'match'],
        ],

        // Spacing and formatting
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'switch', 'foreach', 'for', 'while'],
        ],
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_whitespace_before_comma_in_array' => true,
        'single_quote' => true,
        'whitespace_after_comma_in_array' => true,

        // Class and method rules
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
                'const' => 'one',
            ],
        ],
        'class_definition' => [
            'single_line' => true,
        ],
        'no_blank_lines_after_class_opening' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public',
                'method_protected',
                'method_private',
            ],
        ],
        'self_accessor' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'visibility_required' => [
            'elements' => ['property', 'method', 'const'],
        ],

        // PHPDoc rules
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => false,
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'phpdoc_var_annotation_correct_order' => true,

        // Miscellaneous
        'fully_qualified_strict_types' => true,
        'is_null' => true,
        'mb_str_functions' => true,
        'modernize_types_casting' => true,
        'no_alias_functions' => true,
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'not_operator_with_successor_space' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'void_return' => true,
    ])
    ->setFinder($finder);
