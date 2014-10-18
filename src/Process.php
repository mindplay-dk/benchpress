<?php

namespace mindplay\benchpress;

use Closure;

/**
 * This model represents an individual benchmark process and it's result.
 */
class Process
{
    /** @var Closure the function to benchmark */
    public $function;

    /** @var Closure option function to benchmark for overhead */
    public $overhead;

    /** @var float average time per iteration */
    public $average;

    /** @var float average workload per iteration */
    public $points;

    /** @var float total time elapsed */
    public $elapsed;

    /** @var int number of benchmarks completed */
    public $marks;

    /** @var string benchmark description */
    public $description;
}
