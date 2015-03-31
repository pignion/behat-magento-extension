<?php

namespace Behat\MageExtension\Fixture;

class AttributeWithOptionsFixtureFactory extends AttributeFixtureFactory
{
    public $options_count = 2;

    public function __construct()
    {
        parent::__construct();

        $this->setDefaultParameters(array(
            'is_configurable' => '1',
            'type' => 'int',
            'input' => 'select',
            'frontend_input' => 'select',
            'backend' => 'eav/entity_attribute_backend_array',
            'backend_type' => 'int'
        ));
    }

    public function create($parameters = array())
    {
        $attribute = parent::create($parameters);
        for($i = 0 ; $i < $this->options_count; $i++) {
            $this->addOption($attribute, $this->nextValue('options'));
        }

        return $attribute;
    }

    public function addOption($attribute, $option_value)
    {
        $attribute->setData('option', array(
            'value' => array(
                'option' => array($option_value, $option_value)
            )
        ));
        $attribute->save();
    }
}