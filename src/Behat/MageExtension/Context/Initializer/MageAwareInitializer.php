<?php

namespace Behat\MageExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Behat\MageExtension\Context\MageAwareContext;
use Behat\MageExtension\Fixture\FixtureFactoryManager;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\MageExtension\Page\PageManager;
use Behat\MageExtension\Session\SessionManager;

class MageAwareInitializer implements ContextInitializer
{
    private $_mage_app = null;
    private $_parameters = null;

    public function __construct(array $parameters)
    {
        $this->_parameters = $parameters;
        $this->_mage_app = \Mage::app();
        //\Mage::register('custom_entry_point', true);
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof MageAwareContext) {
            return;
        }
        $context->setMageApp($this->_mage_app);
        $context->setMageParameters($this->_parameters);
        $context->setPageManager(new PageManager($context, $this->_parameters));
        $context->setFixtureManager(new FixtureFactoryManager($this->_parameters['fixture_factories']));
        $context->setSessionManager(new SessionManager($this->_mage_app));
    }
}