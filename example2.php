<?php

require __DIR__ . '/vendor/autoload.php';

use mindplay\benchpress\Benchmark;

# EXAMPLE: a micro-benchmark to assess the cost of using parameter objects

class Context
{
    public $one;
    public $two;
    public $three;

    public function __construct($one, $two, $three)
    {
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }
}

class Foo
{
    public function withParamObject(Context $context)
    {
        return $context->one + $context->two + $context->three;
    }

    public function withArgs($one, $two, $three)
    {
        return $one + $two + $three;
    }
}

$foo = new Foo();

if ($foo->withParamObject(new Context(1, 2, 3)) !== 6) {
    die("error in withParamObject()");
}

if ($foo->withArgs(1, 2, 3) !== 6) {
    die("error in withArgs()");
}

$bench = new Benchmark();

$bench->add(
    "with parameter object",
    function () use ($foo) {
        $foo->withParamObject(new Context(1, 2, 3));
    }
);

$bench->add(
    "with arguments",
    function () use ($foo) {
        $foo->withArgs(1, 2, 3);
    }
);

$bench->run();
