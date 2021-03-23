<?php

namespace common;

class SessionStorage
{

    private $namespace;

    private function __construct($namespace)
    { // нельзя создавать извне
        $this->namespace = $namespace;
        if (!isset($_SESSION[$namespace])) {
            $_SESSION[$namespace] = array();
        }
    }

    /**
     *
     * @return \static
     */
    public static function get($namespace)
    {
        return new static($namespace);
    }

    public function register($name, $default = '')
    {
        if (!isset($_SESSION[$this->namespace][$name])) {
            $_SESSION[$this->namespace][$name] = $default;
        }
    }

    public function save($name, $value)
    {
        $_SESSION[$this->namespace][$name] = $value;
    }

    public function load($name)
    {
        return array_key_exists($name, $_SESSION[$this->namespace]) ? $_SESSION[$this->namespace][$name] : false;
    }

}
