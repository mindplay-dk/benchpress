mindplay/benchpress
----------------

A simple benchmark suite for PHP 5.3 and up.



Basic usage:

    use mindplay\benchpress\Benchmark;

    $bench = new Benchmark();

    // Benchmark a function and get the result immediately:

    $time = $bench->mark(
        function () {
            // do the work...
        }
    );

With reporting:

    use mindplay\benchpress\Benchmark;

    $bench = new Benchmark();

    // Queue up functions to be benchmarked:

    $bench->add(
        "Description of work",
        function () {
            // do the work...
        }
    );

    $bench->add(...);
    $bench->add(...);
    $bench->add(...);

    // Run the queued functions and generate a report:

    $bench->run();

Report output from the included "example.php" benchmark looks like this:

    OVERHEAD:    0.001 msec
    BASELINE:    0.043 msec
    HOST OS:     WINNT
    PHP VERSION: 5.4.7
    MIN. TIME:   500 msec
    MIN. MARKS:  20

    * Completed: isset() on 1000 elements with 0% set using numeric keys
    * Completed: array_key_exists() on 1000 elements with 0% set using numeric keys
    * Completed: isset() on 1000 elements with 0% set using string keys
    * Completed: array_key_exists() on 1000 elements with 0% set using string keys
    * Completed: isset() on 1000 elements with 50% set using numeric keys
    * Completed: array_key_exists() on 1000 elements with 50% set using numeric keys
    * Completed: isset() on 1000 elements with 50% set using string keys
    * Completed: array_key_exists() on 1000 elements with 50% set using string keys
    * Completed: isset() on 1000 elements with 100% set using numeric keys
    * Completed: array_key_exists() on 1000 elements with 100% set using numeric keys
    * Completed: isset() on 1000 elements with 100% set using string keys
    * Completed: array_key_exists() on 1000 elements with 100% set using string keys

    Results
    -------

    isset() on 1000 elements with 0% set using numeric keys ..................... 0.015 msec ....... 0.361 points.............. 1.96%
    isset() on 1000 elements with 50% set using numeric keys .................... 0.017 msec ....... 0.409 points.............. 2.21%
    isset() on 1000 elements with 100% set using numeric keys ................... 0.022 msec ....... 0.519 points.............. 2.81%
    isset() on 1000 elements with 0% set using string keys ...................... 0.103 msec ....... 2.411 points............. 13.07%
    isset() on 1000 elements with 50% set using string keys ..................... 0.104 msec ....... 2.442 points............. 13.23%
    isset() on 1000 elements with 100% set using string keys .................... 0.108 msec ....... 2.541 points............. 13.77%
    array_key_exists() on 1000 elements with 0% set using numeric keys .......... 0.688 msec ...... 16.175 points............. 87.66%
    array_key_exists() on 1000 elements with 50% set using numeric keys ......... 0.692 msec ...... 16.271 points............. 88.18%
    array_key_exists() on 1000 elements with 100% set using numeric keys ........ 0.692 msec ...... 16.275 points............. 88.20%
    array_key_exists() on 1000 elements with 0% set using string keys ........... 0.771 msec ...... 18.127 points............. 98.24%
    array_key_exists() on 1000 elements with 50% set using string keys .......... 0.784 msec ...... 18.441 points............. 99.94%
    array_key_exists() on 1000 elements with 100% set using string keys ......... 0.785 msec ...... 18.452 points............ 100.00%
