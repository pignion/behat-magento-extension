<?php

namespace Behat\MageExtension\Fixture;

class AttributeFixtureFactory extends MageModelFixtureFactory
{

    public function __construct()
    {
        //get the default attribute set to add it to
        $defaultAttributeSetId = \Mage::getSingleton('eav/config')
            ->getEntityType(\Mage_Catalog_Model_Product::ENTITY)
            ->getDefaultAttributeSetId();
        $set = \Mage::getModel('eav/entity_attribute_set')->load(4);
        $gid = $set->getDefaultGroupId();

        $that = $this;
        $this->setDefaultParameters(array(
            'type'=>            'text',
            'input'=>           'text',
            'label'=>           'Test Attribute',
            'global'=>          \Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'is_required'=>     '0',
            'is_comparable'=>   '0',
            'is_searchable'=>   '0',
            'is_unique'=>       '1',
            'is_configurable'=> '1',
            'user_defined'=>    '1',
            'attribute_code' => function() use($that) { return  $that->nextValue('attribute_code', function($i){return 'attribute_fixture_code_' . $i; });  },
            'frontend_label' => function() use($that) { return  $that->nextValue('frontend_label', function($i){return 'Attribute Fixture ' . $i; });  },
            'attribute_set_id' => $defaultAttributeSetId,
            'attribute_group_id' => $gid
        ));
    }

    public function getModelName()
    {
        return 'catalog/resource_eav_attribute';
    }

    public function create($parameters = array())
    {
        $params = $this->processParameters($parameters);
        $attribute = $this->getMageModel();
        $attribute->addData($params);

        $typeId = \Mage::getModel('catalog/product')
            ->getResource()
            ->getEntityType()
            ->getId();

        $attribute->setEntityTypeId($typeId);
        $attribute->setIsUserDefined(1);
        $attribute->save();
        $fixture = new WrappedMageModel($attribute);
        $this->register($fixture);
        return $attribute;
    }

    public function deleteFixture($fixture)
    {
        $model = $fixture->getWrappedModel();
        if($model->getResource()->isUsedBySuperProducts($model)){
            return false;
        }
        else return parent::deleteFixture($fixture);
    }
}