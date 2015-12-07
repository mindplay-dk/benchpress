<?php

/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/vendor/autoload.php';
$autoloader->addPsr4('mindplay\benchpress\\', __DIR__ . '/src');

use mindplay\benchpress\Benchmark;

// EXAMPLE: a benchmark of various model implementation patterns.

class User_Native
{
    public $first_name;
    public $last_name;
    public $email;
}

class User_Methods
{
    private $first_name;
    private $last_name;
    private $email;

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($value) {
        $this->first_name = $value;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($value) {
        $this->last_name = $value;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($value) {
        $this->email = $value;
    }
}

/**
 * @property $first_name
 * @property $last_name
 * @property $email
 */
class User_Accessors
{
    private $_first_name;
    private $_last_name;
    private $_email;

    public function __get($name) {
        return $this->{"get_{$name}"}();
    }

    public function __set($name, $value) {
        $this->{"set_{$name}"}($value);
    }

    protected function get_first_name() {
        return $this->first_name;
    }

    protected function set_first_name($value) {
        $this->first_name = $value;
    }

    protected function get_last_name() {
        return $this->last_name;
    }

    protected function set_last_name($value) {
        $this->last_name = $value;
    }

    protected function get_email() {
        return $this->email;
    }

    protected function set_email($value) {
        $this->email = $value;
    }
}

/**
 * @property $first_name
 * @property $last_name
 * @property $email
 */
class User_Virtual
{
    private $_values = array();

    public function __get($name) {
        return $this->_values[$name];
    }

    public function __set($name, $value) {
        $this->_values[$name] = $value;
    }
}

/**
 * @property $first_name
 * @property $last_name
 * @property $email
 */
class User_TypeSafe
{
    private $_values = array();

    private $_types = array(
        'first_name' => 'is_string',
        'last_name' => 'is_string',
        'email' => 'is_string',
    );

    public function __get($name) {
        return $this->_values[$name];
    }

    public function __set($name, $value) {
        if (! isset($this->_types[$name])) {
            throw new RuntimeException("undefined property \${$name}");
        }

        if (! call_user_func($this->_types[$name], $value)) {
            throw new RuntimeException("invalid property value for \${$name}: {$value}");
        }

        $this->_values[$name] = $value;
    }
}

$bench = new Benchmark();

$bench->add(
    "Native arrays",
    function () {
        $user = array();

        $user['first_name'] = 'Rasmus';
        $user['last_name']= 'Schultz';
        $user['email'] = 'rasmus@mindplay.dk';

        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $email = $user['email'];
    }
);

$bench->add(
    "Native properties",
    function () {
        $user = new User_Native();

        $user->first_name = 'Rasmus';
        $user->last_name = 'Schultz';
        $user->email = 'rasmus@mindplay.dk';

        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
    }
);

$bench->add(
    "Native synchronous methods",
    function () {
        $user = new User_Methods();

        $user->setFirstName('Rasmus');
        $user->setLastName('Schultz');
        $user->setEmail('rasmus@mindplay.dk');

        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $email = $user->getEmail();
    }
);

$bench->add(
    "Protected accessors",
    function () {
        $user = new User_Accessors();

        $user->first_name = 'Rasmus';
        $user->last_name = 'Schultz';
        $user->email = 'rasmus@mindplay.dk';

        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
    }
);

$bench->add(
    "Virtual accessors",
    function () {
        $user = new User_Virtual();

        $user->first_name = 'Rasmus';
        $user->last_name = 'Schultz';
        $user->email = 'rasmus@mindplay.dk';

        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
    }
);

$bench->add(
    "Type-checked accessors",
    function () {
        $user = new User_TypeSafe();

        $user->first_name = 'Rasmus';
        $user->last_name = 'Schultz';
        $user->email = 'rasmus@mindplay.dk';

        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
    }
);

$bench->run();
