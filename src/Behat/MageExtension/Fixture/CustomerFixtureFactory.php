<?php

namespace Behat\MageExtension\Fixture;

class CustomerFixtureFactory extends MageModelFixtureFactory
{

    public function getModelName()
    {
        return 'customer/customer';
    }

    /**
     * @param array $attributes
     * @return Mage_Customer_Model_Customer
     * @throws Exception
     */
    public function create($attributes = array())
    {
        $model = parent::create($attributes);
        $model->setConfirmation(null);
        $model->save();
        return $model;
    }
}
