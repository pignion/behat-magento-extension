<?php

namespace Behat\MageExtension\Fixture;

interface IFixtureFactory
{
    /**
     * @return boolean
     */
    public function clean();

    /**
     * @param array $attributes
     */
    public function create($attributes = array());

    /**
     * @param FixtureFactoryManager $manager
     */
    public function setManager(FixtureFactoryManager $manager);

    /**
     * @return boolean
     */
    public function isClean();

    /**
     * @return array
     */
    public function getFixtures();
} 