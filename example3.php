<?php

require __DIR__ . '/vendor/autoload.php';

use mindplay\benchpress\Benchmark;

# EXAMPLE: a micro-benchmark for different approaches to string output concatenation

$bench = new Benchmark();

$bench->add(
    "echo with string concatenation",
    function () {
        $foo = "foo";
        $bar = "bar";
        $baz = "baz";

        echo $foo . $bar . $baz;
    }
);

$bench->add(
    "echo with string template",
    function () {
        $foo = "foo";
        $bar = "bar";
        $baz = "baz";

        echo "{$foo}{$bar}{$baz}";
    }
);

$bench->add(
    "echo with comma-separated arguments",
    function () {
        $foo = "foo";
        $bar = "bar";
        $baz = "baz";

        echo $foo, $bar, $baz;
    }
);

$bench->run();
