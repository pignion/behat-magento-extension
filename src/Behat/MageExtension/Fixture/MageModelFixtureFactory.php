<?php

namespace Behat\MageExtension\Fixture;

use Behat\MageExtension\Traits\Sequencer;
use Symfony\Component\Config\Definition\Exception\Exception;

abstract class MageModelFixtureFactory implements IFixtureFactory
{
    use Sequencer;

    private $_fixtures = array();
    private $_deferred = array();
//    private $_sequences = array();
    private $_defaultParameters = array();
    private $_manager;

    /**
     * @return FixtureFactoryManager
     */
    public function getManager()
    {
        return $this->_manager;
    }

    public function setManager(FixtureFactoryManager $manager)
    {
        $this->_manager = $manager;
    }

    public function getRequiredParameters()
    {
        return array();
    }

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

    public function setDefaultParameter($name, $value)
    {
        $this->_defaultParameters[$name] = $value;
    }

    /**
     * Merges in the parameters that will be used for model fixture created
     * @param array $parameters the parameter kay/value pairs used in model creation. If items are callable they'll be exectued at runtime
     */
    public function setDefaultParameters(array $parameters)
    {
        $this->_defaultParameters = array_merge($this->getDefaultParameters(), $parameters);
    }

    /**
     * Combines and procoesses the default parameters with the supplied parameters
     * @param array $parameters
     * @return array
     */
    public function processParameters($parameters)
    {
        $result = array_merge($this->getDefaultParameters(), $parameters);
        array_walk($result, function(&$item){
            if(is_callable($item)) $item = $item();
        });
        foreach($this->getRequiredParameters() as $param) {
            if(!isset($result[$param])) {
                throw new \Exception ("$param is a required parameter for this fixture");
            }
        }
        return $result;
    }

    /**
     * @param array $parameters
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function create($parameters = array())
    {
        $data = $this->processParameters($parameters);
        $model = $this->getMageModel();
        $model->addData($data);
        $model->save();
        $fixture = new WrappedMageModel($model);
        $this->register($fixture);
        return $fixture;
    }

    /**
     * Adds a fixture to the factory's known fixture list
     * @param $fixture
     */
    public function register($fixture)
    {
        $this->_fixtures[] = $fixture;
    }

    /**
     * Attempts to delete a fixture
     * @param $fixture
     * @return bool Whether the action was successful
     * @throws \Exception
     */
    public function deleteFixture($fixture)
    {
        $model = $fixture->getWrappedModel();
        try {
            \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
            $result = $model->delete();
            \Mage::app()->setCurrentStore(\Mage_Core_Model_App::DISTRO_STORE_ID);
            return true;
        } catch(\Exception $ex) {
            throw new \Exception("There was an error deleting the fixture: ". $ex->getMessage());
        }
    }

    /**
     * @return array an array of fixtures
     */
    public function getFixtures()
    {
        return $this->_fixtures;
    }

    /**
     * Attempts to clean all fixtures
     * @return bool
     * @throws \Exception
     */
    public function clean()
    {
        $deleted = array();
        foreach ($this->_fixtures as $i => $fixture) {
            if($this->deleteFixture($fixture)) {
                $deleted[] = $i;
            }
        }
        foreach($deleted as $i) {
            unset($this->_fixtures[$i]);
        }
        return count( $this->_fixtures ) === 0;
    }

    /**
     * Whether the factory is clean of fixtures
     * @return boolean
     */
    public function isClean()
    {
        return count( $this->_fixtures ) === 0;
    }

}