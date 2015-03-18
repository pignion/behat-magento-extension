<?php

namespace Behat\MageExtension\Fixture;

abstract class MageModelFixtureFactory implements IFixtureFactory
{
    public static function sequence($funcOrString, $firstNum = 1) {
        $n = $firstNum - 1;
        if (is_callable($funcOrString)) {
            return function() use (&$n, $funcOrString) {
                $n++;
                return call_user_func($funcOrString, $n);
            };
        } elseif (strpos($funcOrString, '%d') !== false) {
            return function() use (&$n, $funcOrString) {
                $n++;
                return str_replace('%d', $n, $funcOrString);
            };
        } else {
            return function() use (&$n, $funcOrString) {
                $n++;
                return $funcOrString . $n;
            };
        }
    }

    private $_fixtures = array();
    private $_defaultParameters = array();

    public abstract function getModelName();

    protected function getMageModel()
    {
        $model = \Mage::getModel($this->getModelName());
        if(!$model) throw new Exception("There was an issue creating the model");
        return $model;
    }

    public function getDefaultParameters()
    {
        return $this->_defaultParameters;
    }

    /**
     * Sets the parameters that will be used for model fixture created
     * @param array $parameters the parameter kay/value pairs used in model creation. If items are callable they'll be exectued at runtime
     */
    public function setDefaultParameters(array $parameters)
    {
        $this->_defaultParameters = $parameters;
    }

    /**
     * @param array $parameters
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function create($parameters = array())
    {
        $data = array_merge($this->getDefaultParameters(), $parameters);
        array_walk($data, function(&$item){
            if(is_callable($item)) $item = $item();
        });

        $model = $this->getMageModel();
        $model->addData($data);
        $model->save();
        $this->_fixtures[] = $model;
        return $model;
    }

    /**
     * @param $fixture Mage_Core_Model_Abstract
     */
    public function deleteFixture($fixture)
    {
        \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        $fixture->delete();
        \Mage::app()->setCurrentStore(\Mage_Core_Model_App::DISTRO_STORE_ID);
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return $this->_fixtures;
    }

    public function clean()
    {
        foreach ($this->_fixtures as $fixture) {
            $this->deleteFixture($fixture);
        }
        $this->_fixtures = array();
    }
} 