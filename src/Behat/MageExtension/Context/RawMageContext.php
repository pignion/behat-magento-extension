<?php


namespace Behat\MageExtension\Context;

use Behat\MageExtension\Page\PageManager;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MageExtension\Fixture\FixtureFactoryManager;

class RawMageContext extends RawMinkContext implements MageAwareContext
{
    private $_mage_app = null;
    private $_parameters = null;
    private $_page_manager = null;
    private $_fixture_manager = null;

    /**
     * @return FixtureFactoryManager
     */
    public function getFixtureManager()
    {
        return $this->_fixture_manager;
    }

    public function setFixtureManager($manager)
    {
        $this->_fixture_manager = $manager;
    }

    /**
     * @return \Mage_Core_Model_App
     */
    public function getMageApp()
    {
        return $this->_mage_app;
    }

    public function setMageApp($app)
    {
        $this->_mage_app = $app;
    }

    /**
     * @return array
     */
    public function getMageParameters()
    {
        return $this->_parameters;
    }

    public function setMageParameters($parameters)
    {
        $this->_parameters = $parameters;
    }

    /**
     * @return PageManager
     */
    public function getPageManager()
    {
        return $this->_page_manager;
    }

    public function setPageManager($manager)
    {
        $this->_page_manager = $manager;
    }

    /**
     * @return \Behat\MageExtension\Page\MagePage
     */
    public function getCurrentPage()
    {
        return $this->getPageManager()->getCurrentPage();
    }


    /**
     * @param string $fixture_name
     * @param array $attributes
     * @throws \Behat\MageExtension\Fixture\Exception
     */
    public function createFixture($fixture_name, $attributes = array())
    {
        $this->getFixtureManager()->getFactory($fixture_name)->create($attributes);
    }

    /** @AfterScenario */
    public function after($event)
    {
        $this->getFixtureManager()->cleanFactories();
    }
}