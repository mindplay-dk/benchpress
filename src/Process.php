<?php

namespace mindplay\benchpress;

use Closure;

/**
 * This model represents an individual benchmark process and it's result.
 */
class Process
{
    /**@var Closure the function to benchmark */
    public $function;

    /** @var Closure optional function to benchmark for overhead (subtracted from measured time) */
    public $overhead;

    /** @var float average time (msec) per iteration */
    public $average;

    /** @var float average workload per iteration (relative to overall benchmarked run-time performance) */
    public $points;

    /** @var float total time elapsed (msec) */
    public $elapsed;

    /** @var int number of benchmarks completed (number of measurements) */
    public $marks;

    /** @var int total number of iterations completed (number of times the benchmarked function was run) */
    public $iterations;

    /** @var string benchmark description */
    public $description;
}
