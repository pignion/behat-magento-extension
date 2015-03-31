<?php

namespace Behat\MageExtension\Fixture;

class ProductFixtureFactory extends MageModelFixtureFactory
{
    function __construct()
    {
        $that = $this;
        $this->setDefaultParameters (
            array(
                'attribute_set_id' => $this->getMageModel()->getResource()
                    ->getEntityType()
                    ->getDefaultAttributeSetId(),
                'name' => function() use($that) {
                    return $that->nextValue('name');
                },
                'weight' => 2,
                'visibility' => \Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                'status' => \Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                'price' => 100,
                'description' => 'Product description',
                'short_description' => 'Product short description',
                'tax_class_id' => 0,
                'type_id' => \Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'stock_data' => array( 'is_in_stock' => 1, 'qty' => 99999 ),
                'website_ids' => array(1),
                'sku' => function() use($that) {
                    return $that->getManager()->nextValue('sku');
                }
            )
        );
    }

    public function getModelName()
    {
        return 'catalog/product';
    }
}