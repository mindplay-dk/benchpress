<?php

namespace mindplay\benchpress;

/**
 * This class implements a basic text report which will be output to the console.
 */
class TextReport implements Report
{
    /**
     * @var bool true, to sort the benchmark results
     */
    public $sort = true;

    /**
     * @inheritdoc
     */
    public function begin(Benchmark $bench)
    {
        echo (
            'OVERHEAD:    ' . number_format($bench->overhead, 3) . " msec\n" .
            'BASELINE:    ' . number_format($bench->factor, 3) . " msec\n" .
            'HOST OS:     ' . PHP_OS . "\n" .
            'PHP VERSION: ' . PHP_VERSION . "\n" .
            'MIN. TIME:   ' . $bench->min_time . " msec\n" .
            'MIN. MARKS:  ' . $bench->min_marks . "\n\n"
        );
    }

    /**
     * @inheritdoc
     */
    public function progress(Process $result)
    {
        $elapsed = number_format($result->elapsed / 1000, 1);

        $iterations = number_format($result->iterations);

        $average = number_format($result->elapsed / $result->iterations, 3);

        echo "* Completed: {$result->description} ({$iterations} iterations in {$elapsed} sec, avg. {$average} msec)\n";
    }

    /**
     * @inheritdoc
     */
    public function finish(Benchmark $bench)
    {
        $results = $bench->results;

        if ($this->sort) {
            usort(
                $results,
                function (Process $a, Process $b) {
                    if ($a->average === $b->average) {
                        return 0;
                    }

                    return $a->average < $b->average ? -1 : 1;
                }
            );
        }

        $max_len = 0;
        $max_time = 0;

        foreach ($results as $result) {
            $max_len = max($max_len, strlen($result->description));
            $max_time = max($max_time, $result->average);
        }

        $min_time = $max_time;

        foreach ($results as $result) {
            $min_time = min($min_time, $result->average);
        }

        $max_len += 1;

        echo "\nResults\n-------\n\n";

        foreach ($results as $result) {
            echo (
                str_pad($result->description . ' ', $max_len, '.') .
                str_pad(' ' . number_format(($result->average), 3) . ' msec ', 20, '.', STR_PAD_LEFT) .
                str_pad(' ' . number_format(($result->points), 3) . ' points ', 20, '.', STR_PAD_LEFT) .
                str_pad(' ' . number_format(($result->average / $max_time * 100), 2) . '% ', 15, '.', STR_PAD_LEFT) .
                str_pad(' ' . number_format(($result->average / $min_time), 2) . 'x', 15, '.', STR_PAD_LEFT) .
                "\n"
            );
        }
    }
}
