<?php

namespace Behat\MageExtension\Page;

class Category extends MagePage
{
    public $categoryId;

    public function setParameters(array $parameters)
    {
        if(array_key_exists('category_id', $parameters)) $this->categoryId = $parameters['category_id'];
        else {$this->categoryId = null; }
        parent::setParameters($parameters);
    }
}