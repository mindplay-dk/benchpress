<?php

namespace mindplay\benchpress;

use Closure;

class Benchmark
{
    /** @var int benchmark for at least this long (msec) */
    public $min_time = 1000;

    /** @var int benchmark at least this many times */
    public $min_marks = 30;

    /** @var int iterate for at least this long (msec) between marks */
    public $min_elapsed = 100;

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
            $n = 12345.12345;
            $s = str_repeat('0123456789', 10);

            $s1 = substr($s, 0, 20);
            $s2 = substr($s, 10, 40) . $s1;
            $s3 = substr($s, 20, 20) . $s2;
            $s4 = substr($s3, 20, 40);

            $n1 = floor($n);
            $n2 = round($n);
            $n3 = sin($n);
            $n4 = cos($n);

            $a = array('foo'=>100, 'bar'=>200, 'baz'=>300);
            $e = '{"foo":100,"bar":200,"baz":300}';

            $j = json_encode($a);
            $d = json_decode($e, true);

            if ($a !== $d) die('internal error (1)');
            if ($e !== $j) die('internal error (2)');

            $a1 = $a['foo'];
            $a2 = $a['bar'];
            $a3 = $a['baz'];

            $x1 = parse_url('scheme://domain:port/path?query_string#fragment_id');
            $x2 = preg_match('/(?P<name>\w+): (?P<digit>\d+)/', 'foobar: 2008', $matches);
            $x3 = date('Y-m-d H:i:s', strtotime('2014-02-13 09:36:27'));
            $x4 = sha1('abcdefghijklmnopqrstuvwxyz', $x3);
        });

        $this->min_time = $min_time;
        $this->min_marks = $min_marks;
        $this->min_elapsed = $min_elapsed;
    }

    /**
     * Benchmark a given function immediately, without generating an entry in the Report.
     *
     * @param Closure $func     the function to benchmark
     * @param float   &$elapsed returns elapsed time in milliseconds
     * @param int     &$marks   returns the number of benchmarks performed
     *
     * @return float average
     */
    public function mark(Closure $func, &$elapsed = null, &$marks = null) {
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

        foreach ($this->results as $result) {
            $overhead = $result->overhead
                ? $this->mark($result->overhead)
                : 0;

            $result->average = $this->mark($result->function, $result->elapsed, $result->marks) - $overhead;
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
