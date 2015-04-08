<?php

namespace Behat\MageExtension\Traits;

trait Attribute {

    public function getAttributeInfo($attribute_id)
    {
        $attribute = \Mage::getModel('catalog/resource_eav_attribute')->load($attribute_id);
        return array(
            'attribute_id' => $attribute_id,
            'options' =>  \Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute)->getAllOptions(false),
            'code'    => $attribute->getAttributeCode()
        );
    }

    public function getAllProductAttributes()
    {
        Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
    }

    public function getAttributeSetByName($name, $entity_type = 'catalog_product')
    {
        $entityTypeId = \Mage::getModel('eav/entity')
            ->setType($entity_type)
            ->getTypeId();
        return \Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->addFieldToFilter('attribute_set_name', $name)
            ->getFirstItem();
    }

    public function getOptionByLabel($attribute, $label)
    {
        if(is_string($attribute)) $attribute = $this->getAttributeByCode($attribute);
        $options = $this->getAllOptions($attribute);
        $result = null;
        foreach($options as $option) {
            if ($option['label'] == $label) {
                $result = $option;
                break;
            }
        }
        return $result;
    }

    public function getAllOptions($attribute)
    {
        return \Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute)->getAllOptions(false);
    }

    public function getOptionIdByLabel($attribute, $label)
    {
        $option = $this->getOptionByLabel($attribute, $label);
        if($option) return $option['value'];
    }

    public function getAttributesBySetId($attribute_set_id)
    {
        return \Mage::getModel('catalog/product')->getResource()
            ->loadAllAttributes()
            ->getSortedAttributes($attribute_set_id);
        //return \Mage::getModel('catalog/product_attribute_api')->items($attribute_set_id);
    }

    public function getConfigurableAttributesBySetId($attribute_set_id)
    {
        return array_filter($this->getAttributesBySetId($attribute_set_id), function($attribute) {
            return $attribute->getIsConfigurable() && $attribute->isScopeGlobal() &&  $attribute->getFrontendInput() === 'select';
        });
    }

    public function getAttributeByCode($code, $entity_type = 'catalog_product')
    {
        return \Mage::getModel('eav/entity_attribute')->loadByCode($entity_type, $code);
    }
}