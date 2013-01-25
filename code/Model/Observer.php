<?php
class Meanbee_Rackspacecloud_Model_Observer {

    public function observeConfigChange(Varien_Event_Observer $observer) {
        /** @var $config Meanbee_Rackspacecloud_Helper_Config */
        $config = Mage::helper('meanbee_rackspacecloudfiles/config');

        if (!$config->isConfigured() && $config->isEnabled()) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('meanbee_rackspacecloudfiles')->__("The Meanbee Rackspace Cloud Files Downloads module is enabled, but without the username and API key, the module will not work!")
            );
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
