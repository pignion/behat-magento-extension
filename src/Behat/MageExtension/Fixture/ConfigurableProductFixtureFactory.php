<?php

namespace Behat\MageExtension\Fixture;

class ConfigurableProductFixtureFactory extends ProductFixtureFactory
{
    use \Behat\MageExtension\Traits\Attribute;

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
     * @param $attributes
     * @return array
     * @throws \Exception
     */
    public function generateSimples($attribute_combos, $parameters = array(), $limit = 5)
    {
        $simples = array();

        foreach(array_slice($attribute_combos, 0, $limit) as $option_combination)
        {
            $product_attributes = $parameters;
            if(!is_callable($parameters['name'])) {
                $product_attributes['name'] = array_reduce($option_combination, function($new_name, $option) {
                    return $new_name . ' ' . $option['label'];
                }, $parameters['name']);
            }

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
        $that = $this;
        $used_product_attribute_ids = array();
        $simple_products = array();


        if(isset($parameters['attribute_set'])) {
            $parameters['attribute_set_id'] = $this->getAttributeSetByName($parameters['attribute_set'])->getId();
        }

        if(isset($parameters['product_attributes'])) {
            $attributes = $parameters['product_attributes'];
            unset($parameters['product_attributes']);
            $option_ids = array();
            foreach($attributes as $code=>$options) {
                $attribute = $this->getAttributeByCode($code);
                $used_product_attribute_ids[] = $attribute->getId();
                $option_ids[$code] = array_map(function($value) use($that, $attribute) {
                    return array( 'value' => $that->getOptionIdByLabel($attribute, $value), 'label' => $value );
                }, $options);
            }
            $option_combos = $this->cartesianOptions($option_ids);
            $simple_products = $this->generateSimples($option_combos, $parameters);
        }

        $data = $this->processParameters($parameters);

        $config_product = $this->getMageModel();
        $config_product->addData($data);
        $config_product->getTypeInstance()->setUsedProductAttributeIds($used_product_attribute_ids);
        $configurableAttributesData = $config_product->getTypeInstance()->getConfigurableAttributesAsArray();
        $config_product->setCanSaveCustomOptions(true);
        $config_product->setCanSaveConfigurableAttributes(true);
        $config_product->setConfigurableAttributesData($configurableAttributesData);

        $config_data = array();

        foreach($simple_products as $product) {
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
            'simple_product_ids' => array_map(function($simple){return $simple->getId();}, $simple_products),
            'configurable_attribute_ids' => $used_product_attribute_ids
        ));
        $this->register($fixture);
        return $fixture;
    }
}