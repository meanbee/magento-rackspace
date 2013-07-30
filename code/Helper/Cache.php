<?php
class Meanbee_RackspaceCloudFiles_Helper_Cache extends Mage_Core_Helper_Abstract {
    const KEY_CREDENTIALS = 'meanbee/rackspacecloudfiles::credentials';
    const KEY_CDN_CONTAINER_MAP = 'meanbee/rackspacecloudfiles::cdn_container_map';
    const KEY_SHARED_SECRET = 'meanbee/rackspacecloudfiles::shared_secret';

    public function clearAll() {
        $this->_removeCache(self::KEY_CREDENTIALS);
        $this->_removeCache(self::KEY_CDN_CONTAINER_MAP);
        $this->_removeCache(self::KEY_SHARED_SECRET);
    }

    public function setCredentials($credentials) {
        $this->_saveCache($credentials, self::KEY_CREDENTIALS);
    }

    public function getCredentials() {
        return $this->_loadCache(self::KEY_CREDENTIALS);
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
