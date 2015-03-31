<?php

namespace Behat\MageExtension\Session;

class SessionManager
{
    private $_mage_app;

    public function __construct($app)
    {
        $this->_mage_app = $app;
    }

    public function anonymousSession()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            if (PHP_SESSION_ACTIVE === session_status()) {
                session_write_close();
                $_SESSION = null;
            }
        } else {
            if (session_id() !== '') {
                $_SESSION = null;
            }
        }

        $mage_session = \Mage::getSingleton('core/session', array('name' => 'frontend'))
            ->clear()
            ->start();
        session_write_close();
        $_SESSION = null;

        return $mage_session->getSessionId();
    }

    public function customerLogin($email, $password)
    {
        $mage_session = \Mage::getSingleton('core/session', array('name' => 'frontend'))
            //->clear()
            ->start();
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            if (PHP_SESSION_ACTIVE === session_status()) {
                session_write_close();
                $_SESSION = null;
            }
        } else {
            if (session_id() !== '') {
                $_SESSION = null;
            }
        }

        /** @var $session \Mage_Customer_Model_Session */
        $session = \Mage::getSingleton('customer/session')
        //    ->start('frontend')
        ;
        if (! $session->login($email, $password)) {
            throw new \Exception('Invalid Customer Email or Password.');
        }
        $id = $session->getSessionId();
        session_write_close();
        $_SESSION = null;
        $_POST = array();
        return $id;
    }

    public function customerLogout()
    {
        /** @var $session \Mage_Customer_Model_Session */
        $session = \Mage::getSingleton('customer/session');
        $session->logout();
    }

}