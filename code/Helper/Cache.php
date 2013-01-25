<?php
class Meanbee_RackspaceCloudFiles_Helper_Cache extends Mage_Core_Helper_Abstract {
    const KEY_AUTH_CONFIG = 'meanbee/rackspacecloudfiles::auth_config';
    const KEY_CDN_CONTAINER_MAP = 'meanbee/rackspacecloudfiles::cdn_container_map';
    const KEY_SHARED_SECRET = 'meanbee/rackspacecloudfiles::shared_secret';

    public function setAuthConfig($value) {
        $this->_saveCache($value, self::KEY_AUTH_CONFIG);
    }

    public function getAuthConfig() {
        return $this->_loadCache(self::KEY_AUTH_CONFIG);
    }

    public function setCdnContainerMap($value) {
        $this->_saveCache($value, self::KEY_CDN_CONTAINER_MAP);
    }

    public function getCdnContainerMap() {
        return $this->_loadCache(self::KEY_CDN_CONTAINER_MAP);
    }

    public function setSharedSecret($value) {
        $this->_saveCache($value, self::KEY_SHARED_SECRET);
    }

    public function getSharedSecret() {
        return $this->_loadCache(self::KEY_SHARED_SECRET);
    }

    protected function _saveCache($data, $key, $tags = array(), $lifeTime = false) {
        $data = serialize($data);
        parent::_saveCache($data, $key, $tags, $lifeTime);
    }

    protected function _loadCache($key) {
        $value = parent::_loadCache($key);

        if ($value !== false) {
            $value = unserialize($value);
        }

        return $value;
    }
}