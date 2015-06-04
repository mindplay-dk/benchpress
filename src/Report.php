<?php

namespace mindplay\benchpress;

/**
 * @see Benchmark::run();
 */
interface Report
{
    /**
     * Output summary header describing the Benchmark
     *
     * @param Benchmark $bench
     *
     * @return void
     */
    public function begin(Benchmark $bench);

    /**
     * Output the result of a completed benchmarked Process
     *
     * @param Process $result
     *
     * @return void
     */
    public function progress(Process $result);

    /**
     * Output summary information about the completed Benchmark
     *
     * @param Benchmark $bench
     *
     * @return void
     */
    public function finish(Benchmark $bench);
}
