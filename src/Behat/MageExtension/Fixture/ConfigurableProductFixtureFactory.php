<?php

namespace Behat\MageExtension\Fixture;

class ConfigurableProductFixtureFactory extends ProductFixtureFactory
{

    function __construct()
    {
        parent::__construct();
        $this->setDefaultParameter('type_id', \Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
    }

    /**
     * Creates new attribues for the configurable product
     * @param int $count the number of attributes to create
     * @return array the IDs of the newly created attributes
     * @throws \Exception
     */
    public function generateAttributes($count = 1)
    {
        $attribute_ids = array();
        for($i = 1; $i <= $count; $i++) {
            $attribute_ids[] = $this->getManager()->getFactory('attribute_with_options')->create()->getId();
        }
        return $attribute_ids;
    }

    /**
     * Generates a simple product fixture for every combination of options for the supplied attributes
     * @param $attribute_ids
     * @return array
     * @throws \Exception
     */
    public function generateSimples($attribute_ids)
    {
        $simples = array();
        $all_options = array();
        foreach ($attribute_ids as $attribute_id) {
            $attribute = \Mage::getModel('catalog/resource_eav_attribute')->load($attribute_id);
            $options = \Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute)->getAllOptions(false);
            $all_options[$attribute->getAttributeCode()] = $options;
        }

        foreach($this->cartesianOptions($all_options) as $option_combination)
        {
            $product_attributes = array();
            foreach($option_combination as $attribute_code => $option) {
               $product_attributes[$attribute_code] = $option['value'];
            }
            $simples[] = $this->getManager()->getFactory('product')->create($product_attributes);
        }

        return $simples;
    }

    /**
     * Generates each possible combination of attribute options
     * @param $input
     * @return array
     */
    function cartesianOptions($input) {
        // filter out empty values
        $input = array_filter($input);

        $result = array(array());

        foreach ($input as $key => $values) {
            $append = array();

            foreach($result as $product) {
                foreach($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    public function create($parameters = array())
    {
        $attribute_ids = array();
        $simpleProducts = array();

        if(isset($parameters['configurable_attributes'])) {
            $attribute_ids = $parameters['configurable_attributes'];
            unset($parameters['configurable_attributes']);
        }
        else {
            $attribute_ids = $this->generateAttributes();
        }

        if(isset($parameters['simple_products'])) {
            $simpleProducts = $parameters['simple_products'];
            unset($parameters['simple_products']);
        }
        else {
            $simpleProducts = $this->generateSimples($attribute_ids);
        }

        $data = $this->processParameters($parameters);

        $config_product = $this->getMageModel();
        $config_product->addData($data);
        $config_product->getTypeInstance()->setUsedProductAttributeIds($attribute_ids);
        $configurableAttributesData = $config_product->getTypeInstance()->getConfigurableAttributesAsArray();
        $config_product->setCanSaveCustomOptions(true);
        $config_product->setCanSaveConfigurableAttributes(true);
        $config_product->setConfigurableAttributesData($configurableAttributesData);

        $config_data = array();

        foreach($simpleProducts as $product) {
            $pid = $product->getId();
            $config_data[$pid] = array();
            foreach($configurableAttributesData as $attribute) {
                $code = $attribute['attribute_code'];
                $config_data[$pid][] = array(
                    'attribute_id' => $attribute['attribute_id'],
                    'label' => '',
                    'value_index' => $product->getData($code),
                    'is_percent' => '',
                    'pricing_value' => ''
                );
            }

        }

        $config_product->setConfigurableProductsData($config_data);

        $config_product->save();
        $fixture = new WrappedMageModel($config_product, array(
            'simple_product_ids' => array_map(function($simple){return $simple->getId();}, $simpleProducts),
            'configurable_attribute_ids' => $attribute_ids
        ));
        $this->register($fixture);
        return $fixture;

    }
}