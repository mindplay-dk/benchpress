<?php

namespace mindplay\benchpress;

/**
 * @see Benchmark::run();
 */
interface Report
{
    public function begin(Benchmark $bench);

    public function progress(Process $result);

    public function finish(Benchmark $bench);
}
