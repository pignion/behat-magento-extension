<?php

namespace Behat\MageExtension\Page;
use Behat\MinkExtension\Context\RawMinkContext;
use  Behat\MageExtension\Context\RawMageContext;
use Behat\MageExtension\Page\MagePage;

class PageManager
{

    /**
     * @var RawMageContext
     */
    private $_context;

    private $_page_instances = array();
    private $_category_page_class;
    private $_product_page_class;

    /**
     * @var Array
     */
    public $pages;

    public function __construct(RawMageContext $context, array $parameters)
    {
        $this->_context = $context;
        $this->pages = $parameters['custom_page_classes'] ?: array();
        $this->_category_page_class = $parameters['category_page_class'];
        $this->_product_page_class = $parameters['product_page_class'];
    }

    protected function getPageInstance($class_name, $attributes = array())
    {
        $instance;
        if(array_key_exists($class_name, $this->_page_instances)){
            $instance = $this->_page_instances[$class_name];
            $instance->setParameters($attributes);
        } else {
            $instance = new $class_name($this->_context, $attributes);
            $this->_page_instances[$class_name] = $instance;
        }
        return $instance;
    }

    public function getCategoryPage($category_id)
    {
        return $this->getPageInstance($this->_category_page_class, array('category_id' => $category_id));
    }

    public function getProductPage($product_id)
    {
        return $this->getPageInstance($this->_product_page_class, array('product_id' => $product_id));

    }

    public function getCurrentPage()
    {
        $product_id = null;
        $category_id = null;

        $current_url = $this->_context->getMink()->getSession()->getCurrentUrl();
        $path = substr($current_url, strlen( $this->_context->getMinkParameter('base_url') ) );

        $urls = \Mage::getModel('core/url_rewrite');
        $urls->setStoreId(1);
        $urls->loadByRequestPath($path);


        if($urls->getData('url_rewrite_id')) {
            if($urls->getData('product_id')) {
                return $this->getProductPage($urls->getData('product_id'));

            }
            if($urls->getData('category_id')) {
                return $this->getCategoryPage($urls->getData('category_id'));
            }
        }
        else {
            $url_segments = explode('/', ltrim($path, '/'));
            $length = count($url_segments);
            if($length > 6 && $url_segments[1] == 'category') {
                return $this->getCategoryPage( $url_segments[6]);
            }
        }



        $class_name = $this->getPageClass($path);
        if(class_exists($class_name)){
            return $this->getPageInstance($class_name);
        }
        $default_class = $this->_context->getMageParameters()['default_page_class'];
        return $this->getPageInstance($default_class);
    }

    /***
     * Guesses a Page class name from a path
     * @param $url string
     * @return string
     */
    public function getPageClass($path)
    {
        if(array_key_exists($path, $this->pages))
           return $this->pages[$path];
//        $page_name = array_search($page_url, $this->pages);
//        if($page_name) {
//            return  preg_replace('/\s+/', '', $page_name);
//        }
    }

}