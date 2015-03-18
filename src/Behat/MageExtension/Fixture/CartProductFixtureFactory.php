<?php

namespace Behat\MageExtension\Fixture;

class CartProductFixtureFactory extends MageModelFixtureFactory
{

    public function getModelName()
    {
        return 'checkout/cart';
    }

    /**
     * @param array $attributes
     * @return Mage_Customer_Model_Customer
     * @throws Exception
     */
    public function create($product_id = 567481, $qty = 1)
    {
        $cart = parent::create();
        $product = Mage::getModel('catalog/product')->load($product_id);
        $cart->init();
        $cart->addProduct($product, array('product_id' => $product_id,'qty' => $qty));
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->addProduct($product, $qty);

        $quote->collectTotals()->save();

        return $cart;
    }

} 