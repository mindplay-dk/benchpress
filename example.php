<?php

/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/vendor/autoload.php';
$autoloader->addPsr4('mindplay\benchpress\\', __DIR__ . '/src');

use mindplay\benchpress\Benchmark;

/**
 * @param int $amount
 * @param int $percent_set
 * @param bool $hash_keys
 *
 * @return array
 */
function fixture($amount, $percent_set, $hash_keys) {
    $array = array();

    $max = round($percent_set * $amount / 100);

    for ($i=0; $i<$amount; $i++) {
        $array[$hash_keys ? md5($i) : $i] = $i < $max ? 'TEST' : null;
    }

    return $array;
}

$sample = fixture(100, 50, false);
$isset = 0;
$key_exists = 0;

foreach ($sample as $index => $value) {
    $isset += isset($sample[$index]) ? 1 : 0;
    $key_exists += array_key_exists($index, $sample) ? 1 : 0;
}

if ($isset !== 50 || $key_exists !== 100) {
    die('bad fixture');
}

$bench = new Benchmark(500, 20);

foreach (array(0,50,100) as $percent_set) {
    foreach (array(false, true) as $hash_keys) {
        $amount = 1000;

        $fixture = fixture($amount, $percent_set, $hash_keys);

        $keys = array_keys($fixture);

        $key_type = $hash_keys ? 'string' : 'numeric';

        $overhead = function () use ($fixture, $keys) {
            foreach ($keys as $key) {
                if (true) {
                    // do nothing
                }
            }
        };

        $bench->add(
            "isset() on {$amount} elements with {$percent_set}% set using {$key_type} keys",
            function () use ($fixture, $keys) {
                foreach ($keys as $key) {
                    if (isset($fixture[$key])) {
                        // do nothing
                    }
                }
            },
            $overhead
        );

        $bench->add(
            "array_key_exists() on {$amount} elements with {$percent_set}% set using {$key_type} keys",
            function () use ($fixture, $keys) {
                foreach ($keys as $key) {
                    if (array_key_exists($key, $fixture)) {
                        // do nothing
                    }
                }
            },
            $overhead
        );

    }
}

$bench->run();
