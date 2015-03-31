<?php

namespace Behat\MageExtension\Fixture;

class WrappedMageModel
{
    private $_model;
    protected $_fixture_data;

    public function __construct($model, $fixture_data = array())
    {
        $this->_model = $model;
        $this->_fixture_data = $fixture_data;
    }

    public function getWrappedModel()
    {
        return $this->_model;
    }

    public function __call($name, $args){
        return call_user_func_array([$this->_model,$name], $args);
    }

    function __get($name)
    {
        return $this->_model->$name;
    }

    function __set($name, $value)
    {
        $this->_model->$name = $value;
    }

    public function getFixtureData($key)
    {
        if(array_key_exists($key, $this->_fixture_data)) return $this->_fixture_data[$key];
        return null;
    }
}