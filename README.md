mindplay/benchpress
-------------------

A simple benchmark suite for PHP 5.3 and up.

Basic usage:

```PHP
    use mindplay\benchpress\Benchmark;

    $bench = new Benchmark();

    // Benchmark a function and get the result immediately:

    $time = $bench->mark(
        function () {
            // do the work...
        }
    );
```

With reporting:

```PHP
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
```

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

    Native arrays ..................... 0.011 msec ...... 0.653 points ....... 43.55% ......... 1.00x
    Native properties ................. 0.011 msec ...... 0.656 points ....... 43.81% ......... 1.01x
    Native synchronous methods ........ 0.017 msec ...... 1.018 points ....... 67.92% ......... 1.56x
    Virtual accessors ................. 0.019 msec ...... 1.137 points ....... 75.88% ......... 1.74x
    Protected accessors ............... 0.021 msec ...... 1.238 points ....... 82.62% ......... 1.90x
    Type-checked accessors ............ 0.025 msec ...... 1.499 points ...... 100.00% ......... 2.30x
