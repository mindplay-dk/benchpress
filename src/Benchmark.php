<?php

namespace mindplay\benchpress;

use Closure;

class Benchmark
{
    /** @var int benchmark for at least this long (msec) */
    public $min_time = 300;

    /** @var int benchmark at least this many times */
    public $min_marks = 30;

    /** @var int iterate for at least this long (msec) between marks */
    public $min_elapsed = 10;

    /** @var float function call overhead in milliseconds */
    public $overhead = 0;

    /** @var float baseline system performance factor */
    public $factor;

    /** @var Process[] benchmark results */
    public $results = array();

    /**
     * @param int $min_time    benchmark for at least this long (msec)
     * @param int $min_marks   benchmark at least this many times
     * @param int $min_elapsed iterate for at least this long (msec) between marks
     */
    public function __construct($min_time = 2000, $min_marks = 30, $min_elapsed = 100)
    {
        // benchmark an empty function to establish function call overhead:

        $this->overhead = $this->mark(function() {});

        // benchmark a function using various PHP features to establish
        // a performance baseline for the PHP version / platform / etc.:

        $this->factor = $this->mark(function() {
            $v = 0;
            $s = '';
            $a = array();

            $fn = function($x) {
                return (string) $x; // casting
            };

            for ($i=0; $i<10; $i++) {
                $v += $i; // integer

                $b = $v > 50; // boolean

                // branching:

                if ($b) {
                    $n = true;
                } else {
                    $n = false;
                }

                $s .= $fn($i); // strings, function calls

                $a[$i] = $n; // arrays
            }
        });

        $this->min_time = $min_time;
        $this->min_marks = $min_marks;
        $this->min_elapsed = $min_elapsed;
    }

    /**
     * Benchmark a given function immediately, without generating an entry in the Report.
     *
     * @param Closure $func        the function to benchmark
     * @param float   &$elapsed    returns elapsed time in milliseconds
     * @param int     &$marks      returns the number of benchmarks performed
     * @param int     &$iterations returns the total number of iterations completed
     *
     * @return float average
     */
    public function mark(Closure $func, &$elapsed = null, &$marks = null, &$iterations = null) {
        $time = array();
        $elapsed = 0;
        $marks = -1; // ignore the first iteration
        $num_it = $this->min_iterations($func);

        while ($elapsed < $this->min_time || $marks < $this->min_marks) {
            $start = microtime(true) * 1000;

            for ($it=0; $it < $num_it; $it++) {
                $func();
            }

            $end = microtime(true) * 1000;

            $marks += 1;

            if ($marks > 0) {
                $time[] = ($end - $start) / $num_it;
                $elapsed += $end - $start;
            }
        }

        $iterations = $num_it * $marks;

        $weighted = $this->weighted_average($time);

        return max(0, $weighted - $this->overhead);
    }

    /**
     * Queue a given function for benchmarking - the result will appear in the Report.
     *
     * @param string       $description a short description of the benchmark
     * @param Closure      $function    the function to benchmark
     * @param Closure|null $overhead    an optional function to benchmark for overhead
     *
     * @see run()
     */
    public function add($description, Closure $function, Closure $overhead = null) {
        $result = new Process();

        $result->description = $description;
        $result->function = $function;
        $result->overhead = $overhead;

        $this->results[] = $result;
    }

    /**
     * Run all functions queued for benchmarking and generate a report.
     *
     * @param Report $report the report to generate (optional, defaults to TextReport)
     *
     * @see add()
     */
    public function run(Report $report = null)
    {
        $report = $report ?: new TextReport();

        $report->begin($this);

        // trigger any errors as early as possible:

        foreach ($this->results as $result) {
            $function = $result->function;
            $function();
        }

        // run the benchmarks:

        foreach ($this->results as $result) {
            $overhead = $result->overhead
                ? $this->mark($result->overhead)
                : 0;

            $result->average = $this->mark($result->function, $result->elapsed, $result->marks, $result->iterations) - $overhead;
            $result->points = $result->average / $this->factor;

            $report->progress($result);
        }

        $report->finish($this);
    }

    /**
     * @param Closure $func the function to benchmark for required minimum number of iterations
     *
     * @return int the minimum number of iterations required to run
     *             for at least {@link $min_elapsed} msec between marks.
     */
    private function min_iterations($func) {
        $it = 0;
        $elapsed = 0;

        $start = microtime(true) * 1000;

        while ($elapsed < $this->min_elapsed) {
            $func();

            $it += 1;

            $elapsed = microtime(true) * 1000 - $start;
        }

        return $it;
    }

    /**
     * @param float[] $values values for which to compute a weighted average
     *
     * @return float weighted average
     */
    private function weighted_average($values) {
        $sum = 0;
        foreach ($values as $i => $value) {
            $sum += $value;
        }

        $avg = $sum / count($values);

        $error = array();

        $max_error = 0;

        foreach ($values as $i => $value) {
            $error[$i] = abs($avg - $value);
            $max_error = max($error[$i], $max_error);
        }

        $total_weight = 0;
        $weighted_sum = 0;

        foreach ($values as $i => $value) {
            $weight = 1 - ($error[$i] / $max_error);
            $weight = $weight * $weight; // square
            $total_weight += $weight;
            $weighted_sum += $weight * $value;
        }

        $weighted_average = $weighted_sum / $total_weight;

        return $weighted_average;
    }
}
