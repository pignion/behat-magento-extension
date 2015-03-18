<?php

namespace Behat\MageExtension\Page;

class Product extends MagePage
{
    public $productId;

    public function setParameters(array $parameters)
    {
        if(array_key_exists('product_id', $parameters)) $this->productId = $parameters['product_id'];
        else { $this->productId = null; }
        parent::setParameters($parameters);
    }
}