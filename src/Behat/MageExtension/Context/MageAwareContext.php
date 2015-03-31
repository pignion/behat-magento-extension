<?php
/**
 * Created by IntelliJ IDEA.
 * User: john
 * Date: 3/12/15
 * Time: 9:44 AM
 */

namespace Behat\MageExtension\Context;

use Behat\Behat\Context\Context;


interface MageAwareContext
{
    public function setMageApp($app);
    public function setPageManager($manager);
    public function setMageParameters($parameters);
    public function setFixtureManager($manager);
    public function setSessionManager($manager);
}