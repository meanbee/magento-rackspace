<?php
class Meanbee_RackspaceCloudFiles_Model_Observer {

    public function observeConfigChange(Varien_Event_Observer $observer) {
        /** @var $config Meanbee_RackspaceCloudFiles_Helper_Config */
        $config = Mage::helper('meanbee_rackspacecloudfiles/config');

        if (!$config->isConfigured() && $config->isEnabled()) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('meanbee_rackspacecloudfiles')->__("The Meanbee Rackspace Cloud Files Downloads module is enabled, but without the username and API key, the module will not work!")
            );
        }

        if ($config->isConfigured()) {
            /** @var $connection Meanbee_RackspaceCloudFiles_Model_Connection */
            $connection = Mage::getModel('meanbee_rackspacecloudfiles/connection');

            /*
             * Double check the entered credentials against the Rackspace API.  Will throw an exception if there are credentials, so we take the exception message
             * and make it a little more user friendly.
             */
            try {
                /** @var $auth_instance CF_Authentication */
                $auth_instance = $connection->getAuthInstance(true);

                Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('meanbee_rackspacecloudfiles')->__("We double checked your credentials against the Rackspace API and they appear to be correct")
                );
            } catch (Exception $e) {
                Mage::throwException("It appears that your Rackspace Credentials are incorrect, we checked them and the following error was returned: " . $e->getMessage());
            }
        }

        if ($config->isLogEnabled()) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('meanbee_rackspacecloudfiles')->__("Logging is now enabled.")
            );

            $this->_log("Logging is enabled.");
        } else {
            $this->_log("Logging is disabled.  A log message to tell you that logging is disabled.. ingenious, right?");
        }
    }

    protected function _log($message, $level = Zend_Log::DEBUG) {
        Mage::helper('meanbee_rackspacecloudfiles')->log($message, $level);
    }
}
