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
    BASELINE:    0.056 msec
    HOST OS:     WINNT
    PHP VERSION: 5.4.7
    MIN. TIME:   2000 msec
    MIN. MARKS:  30

    * Completed: Native arrays
    * Completed: Native properties
    * Completed: Native synchronous methods
    * Completed: Protected accessors
    * Completed: Virtual accessors
    * Completed: Type-checked accessors

    Results
    -------

    Native arrays ..................... 0.014 msec ....... 0.249 points........ 40.33%......... 2.48x
    Native properties ................. 0.014 msec ....... 0.254 points........ 41.18%......... 2.43x
    Native synchronous methods ........ 0.021 msec ....... 0.381 points........ 61.80%......... 1.62x
    Virtual accessors ................. 0.025 msec ....... 0.446 points........ 72.28%......... 1.38x
    Protected accessors ............... 0.027 msec ....... 0.481 points........ 77.89%......... 1.28x
    Type-checked accessors ............ 0.034 msec ....... 0.617 points....... 100.00%......... 1.00x
