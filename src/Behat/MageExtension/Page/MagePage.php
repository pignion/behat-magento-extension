<?php

namespace Behat\MageExtension\Page;

use Behat\MageExtension\Context\MageAwareContext;
use Behat\MageExtension\Context\RawMageContext;
use Behat\MinkExtension\Context\MinkAwareContext;

class MagePage
{

    private $_context;
    private $_parameters;

    protected $elements = array();

    public function __construct(RawMageContext $context, array $parameters = array())
    {
        $this->_context = $context;
        $this->setParameters($parameters);
        $def_values = get_class_vars(get_parent_class($this));
        $parent_elements = $def_values['elements'];
        if(isset($parent_elements)){ $this->elements = array_merge($this->elements, $parent_elements); }
    }

    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    public function getDocument()
    {
        return $this->_context->getSession()->getPage();
    }

    /**
     * Gets the initialization parameters for the page
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * @param $element_name string
     * @return array|null
     */
    public function getSelector($element_name) {
        if(array_key_exists($element_name, $this->elements)) {
            $selector = $this->elements[$element_name];
            if(is_array($selector)) return $selector;
            else return array('css', $selector);
        }
        return null;
    }

    /**
     * @param $element_name string
     * @throws Exception
     * @return \Behat\Mink\Element\NodeElement|null
     */
    public function getElementByName($element_name)
    {
        $selector = $this->getSelector($element_name);
        if($selector) {
            return call_user_func_array(array($this->getDocument(), 'find'), $selector);
        }
        else throw new Exception("There is no element defined for $element_name. Add it to the elements array for the " . get_class($this) .  " class");
    }

    /**
     * @param $element_name
     * @throws Exception
     * @return array
     */
    public function getElementsByName($element_name)
    {
        $selector = $this->getSelector($element_name);
        if($selector) {
            return call_user_func_array(array($this->getDocument(), 'findAll'), $selector);
        }
        else throw new Exception("There is no element defined for $element_name. Add it to the elements array for the " . get_class($this) .  " class");
    }

    /**
     * @param $element_name string
     * @return bool
     * @throws Exception
     */
    public function hasElement($element_name)
    {
        return count( $this->getElementsByName($element_name)) > 0;
    }

    /**
     * @param $element_name string
     * @return bool
     * @throws Exception
     */
    public function elementIsVisible($element_name)
    {
        return $this->getElementByName($element_name)->isVisible();
    }

    /**
     * @param $element_name string
     * @param $number_of_elements int
     * @return bool
     * @throws Exception
     */
    public function hasNumberOfElements($element_name, $number_of_elements)
    {
        return count( $this->getElementsByName($element_name)) === $number_of_elements;
    }

    /**
     * Determines whether the page contains the supplied text
     * @param $text
     * @return bool
     */
    public function contains($text)
    {
        return $this->getDocument()->hasContent($text);
    }

    /**
     * Clicks the element with the supplied text
     * @param $text
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function click($text)
    {
        $this->getDocument()->clickLink($text);
    }

    /**
     * @param $element_name string
     * @throws Exception
     * @throws \Behat\Mink\Exception\ElementException
     */
    public function clickTheElement($element_name)
    {
        $el = $this->getElementByName($element_name);
        if($el) $el->click();
        else throw new Exception('Could not find an element with selector '. implode(': ', $this->get_element_selector($element_name)));
    }
}