<?php

namespace Behat\MageExtension\Fixture;

class FixtureFactoryManager
{

    private $_factories = array();


    public function __construct($factories = array())
    {
        foreach ($factories as $factory_name => $factory_class) {
            $this->addFactory($factory_name, $factory_class);
        }
    }

    /**
     * @param string $factory_name
     * @throws Exception
     * @return IFixtureFactory
     */
    public function getFactory($factory_name)
    {
        if(array_key_exists($factory_name, $this->_factories)) return $this->_factories[$factory_name];
        throw new Exception("There is no factory named $factory_name");
    }

    /**
     * @param $factory_name
     * @param string $factory_class
     * @return \IFixtureFactory
     * @throws Exception
     */
    public function addFactory($factory_name, $factory_class = null)
    {
        if(!$factory_class) {
            if(class_exists($factory_name))
                $factory_class = $factory_name;
            else if(class_exists($factory_name . 'FixtureFactory'))
                $factory_class = $factory_name . 'FixtureFactory';
            else
                throw new Exception("You must either provide a class name, or create a class for $factory_name");
        }
        if(!array_key_exists($factory_name, $this->_factories)) {
            $factory = new $factory_class();
            $this->_factories[$factory_name] = $factory;
            return $factory;
        }
        throw new Exception("The factory '$factory_name' has already been added'" );
    }

    /**
     * @param string $factory_name
     */
    public function cleanFactory($factory_name)
    {
        $factory = $this->getFactory($factory_name);
        $factory->clean();
    }

    /**
     *
     */
    public function cleanFactories()
    {
        foreach(array_keys($this->_factories) as $factory_name) {
            $this->cleanFactory($factory_name);
        }
    }

    /**
     * @param $factory_name
     * @return array
     * @throws Exception
     */
    public function getFixtures($factory_name)
    {
        return $this->getFactory($factory_name)->getFixtures();
    }

} 