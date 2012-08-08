<?php
class Meanbee_Rackspacecloud_Helper_Cache extends Mage_Core_Helper_Abstract {
    const KEY_AUTH_CONFIG = 'meanbee/rackspacecloud::auth_config';

    public function setAuthConfig($value) {
        $this->_saveCache(self::KEY_AUTH_CONFIG, $value);
    }

    public function getAuthConfig() {
        return $this->_loadCache(self::KEY_AUTH_CONFIG);
    }
}