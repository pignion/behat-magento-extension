<?php

namespace Behat\MageExtension\Fixture;

interface IFixtureFactory
{
    public function clean();
    public function create($attributes = array());

    /**
     * @return array
     */
    public function getFixtures();
} 