<?php
class Meanbee_Rackspacecloud_Helper_Cache extends Mage_Core_Helper_Abstract {
    const KEY_AUTH_CONFIG = 'meanbee/rackspacecloud::auth_config';
    const KEY_CDN_CONTAINER_MAP = 'meanbee/rackspacecloud::cdn_container_map';
    const KEY_SHARED_SECRET = 'meanbee/rackspacecloud::shared_secret';

    public function setAuthConfig($value) {
        $this->_saveCache(self::KEY_AUTH_CONFIG, $value);
    }

    public function getAuthConfig() {
        return $this->_loadCache(self::KEY_AUTH_CONFIG);
    }

    public function setCdnContainerMap($value) {
        $this->_saveCache(self::KEY_CDN_CONTAINER_MAP, $value);
    }

    public function getCdnContainerMap() {
        return $this->_loadCache(self::KEY_CDN_CONTAINER_MAP);
    }

    public function setSharedSecret($value) {
        $this->_saveCache(self::KEY_SHARED_SECRET, $value);
    }

    public function getSharedSecret() {
        return $this->_loadCache(self::KEY_SHARED_SECRET);
    }
}