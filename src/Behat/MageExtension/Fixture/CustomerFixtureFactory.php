<?php

namespace Behat\MageExtension\Fixture;

use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerFixtureFactory extends MageModelFixtureFactory
{

    public function __construct()
    {
        $this->setDefaultParameters(array(
            'email' => $this->nextValue('email', function($i){ return "johnj+$i@copiousinc.com";}),
            'password' => $this->nextValue('password', function($i){ return "password_$i";}),

        ));
    }

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
