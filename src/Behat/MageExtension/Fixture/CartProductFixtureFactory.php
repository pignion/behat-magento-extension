<?php

namespace Behat\MageExtension\Fixture;

class CartProductFixtureFactory extends MageModelFixtureFactory
{
    public function deleteFixture($fixture)
    {
        return null;
    }

    public function getModelName()
    {
        return null;
        //return 'checkout/cart';
    }

    /**
     * @param array $attributes
     * @return Mage_Customer_Model_Customer
     * @throws Exception
     */
    public function create($parameters = array())
    {
        $cart = \Mage::getModel('checkout/cart');
        //$cart = parent::create($parameters);
        $product_id = $parameters['product_id'];
        $qty = $parameters['qty'];
        $product = \Mage::getModel('catalog/product')->load($product_id);
        $cart->init();
        $cart->addProduct($product, array('product_id' => $product_id,'qty' => $qty));
        $cart->save();
        \Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

        $quote = \Mage::getSingleton('checkout/session')->getQuote();
        $quote->addProduct($product, $qty);

        $quote->collectTotals()->save();

        return $cart;
    }

    public function getRequiredParameters()
    {
        return array('product_id', 'qty');
    }
}