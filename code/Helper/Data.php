<?php
class Meanbee_Rackspacecloud_Helper_Data extends Mage_Core_Helper_Abstract {
    public function log($message, $level = Zend_Log::DEBUG) {
        /** @var $config Meanbee_Rackspacecloud_Helper_Config */
        $config = Mage::helper('meanbee_rackspacecloudfiles/config');

        Mage::log("[meanbee_rackspacecloudfiles] $message", $level, $config->getLogLocation(), $config->isLogEnabled());
    }

    public function getRandomSecretKey() {
        return str_shuffle(md5(microtime()));
    }

    public function isRelevantUrl($url) {
        preg_match_all("/^https?:\/\/.*\.rackcdn\.com\/(.*)$/", $url, $matches);

        return $matches && count($matches) > 0 && count($matches[0]) > 0;
    }
}