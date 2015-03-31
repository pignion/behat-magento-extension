<?php

namespace Behat\MageExtension\Fixture;

class AttributeSetFixtureFactory extends MageModelFixtureFactory
{

    public function __construct()
    {
        $that = $this;
        $this->setDefaultParameter('attribute_set_name', function() use($that) {
            return $that->nextValue('attribute_set_name', function($index) {
               return 'attribute_set_fixture_' . $index;
            });
        });
    }

    public function getModelName()
    {
        return 'eav/entity_attribute_set';
    }

    public function getRequiredParameters()
    {
        return array('attribute_set_name');
    }

    public function create($parameters = array())
    {
        $params = $this->processParameters($parameters);
        $attribute_set = $this->getMageModel();

        $entityTypeId = \Mage::getModel('catalog/product')
            ->getResource()
            ->getEntityType()
            ->getId();

        $attribute_set
            ->setEntityTypeId($entityTypeId)
            ->setAttributeSetName($params['attribute_set_name']);
        $attribute_set->validate();
        $attribute_set->save();

        $attribute_set->initFromSkeleton(4);

//        $group = \Mage::getModel('eav/entity_attribute_group');
//        $group->setAttributeGroupName($this->nextValue('group_name', function($i){return 'GROUP_'.$i;}));
//        $group->setAttributeSetId($attribute_set->getId());

//        $attribute_set->setGroups(array($group));

        $attribute_set->save();
        $fixture = new WrappedMageModel($attribute_set);
        $this->register($fixture);
        return $fixture;
    }
}