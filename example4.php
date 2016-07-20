<?php

require __DIR__ . '/vendor/autoload.php';

use mindplay\benchpress\Benchmark;

# EXAMPLE: a micro-benchmark for fastest way to validate a UTF-8 encoded string

$bench = new Benchmark();

$samples = array(
    "valid" => array(
        "short" => "abcd",
        "long"  => str_repeat('0123456789abcdef', 128),
    ),
    "invalid" => array(
        "short" => "\xc3\x28",
        "long"  => str_repeat('0123456789abcdef', 128) . "\xc3\x28",
    ),
);

foreach ($samples as $validity => $cases) {
    foreach ($cases as $case => $string) {
        $expected = $validity === "valid" ? true : false;

        $bench->add(
            "{$case}, {$validity} with preg_match()",
            function () use ($string, $expected) {
                assert($expected === (preg_match('//u', $string) === 1));
            }
        );

        $bench->add(
            "{$case}, {$validity} with mb_check_encoding()",
            function () use ($string, $expected) {
                assert($expected === mb_check_encoding($string, "UTF-8"));
            }
        );

        $bench->add(
            "{$case}, {$validity} with mb_detect_encoding()",
            function () use ($string, $expected) {
                assert($expected === (false !== mb_detect_encoding($string, 'UTF-8', true)));
            }
        );

        $bench->add(
            "{$case}, {$validity} with iconv()",
            function () use ($string, $expected) {
                assert($expected === (strlen($string) === strlen(@iconv('UTF-8', 'UTF-8//IGNORE', $string))));
            }
        );
    }
}

$bench->run();
