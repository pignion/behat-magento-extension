<?php


namespace Behat\MageExtension\Context;

use Behat\MageExtension\Page\PageManager;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MageExtension\Fixture\FixtureFactoryManager;
use Behat\MageExtension\Session\SessionManager;

class RawMageContext extends RawMinkContext implements MageAwareContext
{
    private $_mage_app = null;
    private $_parameters = null;
    private $_page_manager = null;
    private $_fixture_manager = null;
    private $_session_manager = null;

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
     * @return SessionManager
     */
    public function getSessionManager()
    {
        return $this->_session_manager;
    }

    public function setSessionManager($manager)
    {
        $this->_session_manager = $manager;
    }

    /**
     * @return \Behat\MageExtension\Page\MagePage
     */
    public function getCurrentPage()
    {
        return $this->getPageManager()->getCurrentPage();
    }

    public function getProductUrlByName($product_name)
    {
        $prod = \Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('name', $product_name);
        $id = $prod->getFirstItem()->getEntityId();
        return $this->getProductUrlById($id);

    }

    public function getProductUrlById($id)
    {
        /**
         * @var $model \Mage_Catalog_Model_Product
         */
        $model =  \Mage::getModel('catalog/product')->load($id);
        return $model->getUrlInStore();
//        return $model->getUrlInStore(array('_ignore_category' => true));
        $url = \Mage::getModel('core/url_rewrite')->loadByRequestPath(
            $model->getUrlPath()
        );
        return $url->getTargetPath();
//        $urlModel = \Mage::getSingleton('catalog/factory')->getProductUrlInstance();
//        return $urlModel->getUrl($model, array('_type'=>\Mage_Core_Model_Store::URL_TYPE_WEB));
    }

    public function addToCart($product_id, $qty = 1, $options = null)
    {
        $product = \Mage::getModel('catalog/product')->load($product_id);
        $customer = \Mage::getSingleton('customer/session')->getCustomer();
        if($customer) {
            \Mage::getSingleton('checkout/session')->getQuote()->setCustomerId($customer->getId());
        }
//        $cart = \Mage::getModel('checkout/cart');
//        $cart->init();
//        $cart->addProduct($product, array('product_id' => $product_id,'qty' => $qty));
//        $cart->save();

        \Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $quote = \Mage::getSingleton('checkout/session')->getQuote();
        if($options) {
            $quote->addProduct($product, new \Varien_Object( array('product_id'=> $product_id, 'super_attribute' => $options, 'qty' => $qty)));
        }
        else $quote->addProduct($product, $qty);
        $quote->collectTotals()->save();
    }

    /**
     * @param string $fixture_name
     * @param array $attributes
     * @throws \Behat\MageExtension\Fixture\Exception
     */
    public function createFixture($fixture_name, $attributes = array())
    {
        return $this->getFixtureManager()->getFactory($fixture_name)->create($attributes);
    }

    /** @AfterScenario */
    public function after($event)
    {
        $this->getFixtureManager()->cleanFactories();
    }


}