<?php
class Meanbee_Rackspacecloud_Helper_Data extends Mage_Core_Helper_Abstract {
    public function log($message, $level = Zend_Log::DEBUG) {
        /** @var $config Meanbee_Rackspacecloud_Helper_Config */
        $config = Mage::helper('rackspace/config');

        $module_log_active = $config->isLogEnabled();
        Mage::log("[meanbee_rackspacecloud] $message", $level, $config->getLogLocation(), $module_log_active);
    }

    public function getRandomSecretKey() {
        return str_shuffle(md5(microtime()));
    }
}