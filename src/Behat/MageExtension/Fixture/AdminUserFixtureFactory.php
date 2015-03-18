<?php

namespace Behat\MageExtension\Fixture;

class AdminUserFixtureFactory extends MageModelFixtureFactory
{

    public function getModelName()
    {
        return 'admin/user';
    }

    /**
     * @param array $properties
     * @return Mage_Admin_Model_User
     * @throws Exception
     */
    public function create($properties = array())
    {
        $model = parent::create($properties);
        $role = \Mage::getModel("admin/role");
        $role->setParentId(1);
        $role->setTreeLevel(1);
        $role->setRoleType('U');
        $role->setUserId($model->getId());
        $role->save();
        return $model;
    }
} 