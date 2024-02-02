<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->exclude('vendor')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->append($finder)
    ->exclude('bootstrap/cache')
    ->exclude('storage')
    ->exclude('public')
    ->exclude('node_modules')
    ->exclude('database/seeds')
    ->exclude('database/factories')
    ->exclude('resources/lang')
    ->exclude('tests/Feature')
    ->exclude('tests/Unit')
    ->exclude('tests/TestCase.php')
    ->exclude('database/migrations')
    ->exclude('database/factories')
    ->exclude('public/js')
    ->exclude('public/css')
    ->exclude('public/mix-manifest.json')
    ->exclude('public/storage')
    ->exclude('public/vendor')
    ->exclude('storage/framework')
    ->exclude('storage/app/public')
    ->exclude('storage/logs')
    ->exclude('storage/debugbar')
    ->exclude('bootstrap/cache')
    ->exclude('node_modules')
    ->exclude('public/js')
    ->exclude('public/css')
    ->exclude('public/mix-manifest.json')
    ->exclude('public/storage')
    ->exclude('public/vendor')
    ->exclude('storage/framework')
    ->exclude('storage/app/public')
    ->exclude('storage/logs')
    ->exclude('storage/debugbar')
    ->exclude('bootstrap/cache');

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['operators' => ['=>' => 'align_single_space_minimal']],
        'cast_spaces' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'function_typehint_space' => true,
        'linebreak_after_opening_tag' => true,
        'lowercase_cast' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_multiline_whitespace_before_semicolons' => true,
        'no_short_echo_tag' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'ordered_imports' => true,
        'phpdoc_align' => true,
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'return_type_declaration' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'whitespace_after_comma_in_array' => true,
    ]);
