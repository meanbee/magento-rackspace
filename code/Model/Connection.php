<?php
include "php-cloudfiles/cloudfiles.php";

class Meanbee_Rackspacecloud_Model_Connection extends Mage_Core_Model_Abstract {
    /* This function will get the correct values for the instance variables in this class.
    TODO Cache the results */
    protected function _construct() {
        parent::_construct();


        $this->generateMap();

        $this->_data['shared_secret'] = $this->updateSharedSecret("Hello");
    }

    protected function updateSharedSecret($sharedSecret)
    {
        $curlCh = curl_init();

        curl_setopt($curlCh, CURLOPT_SSL_VERIFYPEER, True);
        curl_setopt($curlCh, CURLOPT_CAINFO, Mage::getBaseDir('lib') . "/php-cloudfiles/share/cacert.pem");
        curl_setopt($curlCh, CURLOPT_POST, TRUE);
        curl_setopt($curlCh, CURLOPT_POSTFIELDS, "");
        curl_setopt($curlCh, CURLOPT_VERBOSE, 1);
        curl_setopt($curlCh, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlCh, CURLOPT_MAXREDIRS, 4);
        curl_setopt($curlCh, CURLOPT_HEADER, 0);
        curl_setopt($curlCh, CURLOPT_HTTPHEADER, array("X-Auth-Token: " . $this->getAuthToken(), "X-Account-Meta-Temp-Url-Key: $sharedSecret"));
        curl_setopt($curlCh, CURLOPT_USERAGENT, USER_AGENT);
        curl_setopt($curlCh, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlCh, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlCh, CURLOPT_URL, $this->getStorageUrl());
        curl_exec($curlCh);
        curl_close($curlCh);

        return $sharedSecret;
    }

    protected function generateMap()
    {
        $this->_data['map'] = array();
        foreach ($this->getConnection()->get_containers() as $container) {
            $container->make_public();
            $this->_data['map'][$container->cdn_uri] = $container->name;
            $this->_data['map'][$container->cdn_ssl_uri] = $container->name;
        }
    }

    public function getTempUrl($url) {
        // TODO Error checking, container doesn't exist?
        $containerInfo = $this->_getContainerInfo($url);
        $container = $this->getConnection()->get_container($containerInfo['name']);
        $objectName = $this->_getObjectName($url, $containerInfo['url']);
        $object = $container->get_object($objectName);

        return $object->get_temp_url(
            $this->_data['shared_secret'],
            $this->getConfig()->getRackspaceRequestTimeout(),
            'GET'
        );
    }

    protected function _getObjectName($url, $containerUrl) {
        $length = strlen($containerUrl);
        $strippedUrl = substr($url, $length, strlen($url));
        return ltrim($strippedUrl, '/');

    }

    protected function _getContainerInfo($url) {
        foreach ($this->getMap() as $key => $value) {
            $keyLength = strlen($key);
            $containerCdn = substr($url, 0, $keyLength);
            if ($containerCdn == $key) {
                return array (
                    "name" => $value,
                    "url" => $key
                );
            }
        }
    }

    /**
     * @return Meanbee_Rackspacecloud_Helper_Config
     */
    public function getConfig() {
        return Mage::helper('rackspace/config');
    }

    /**
     * @return Meanbee_Rackspacecloud_Helper_Cache
     */
    public function getCache() {
        return Mage::helper('rackspace/cache');
    }

    public function getAuthInstance() {
        $auth_config = $this->getCache()->getAuthConfig();

        if ($auth_config === false) {
            $username = $this->getConfig()->getRackspaceUsername();
            $api_key = $this->getConfig()->getRackspaceApiKey();

            $auth = new CF_Authentication($username, $api_key);
            $auth->authenticate();

            $this->getCache()->setAuthConfig($auth->export_credentials());
        } else {
            $auth = new CF_Authentication();
            $auth->load_cached_credentials($auth_config['auth_token'], $auth_config['storage_url'], $auth_config['cdnm_url']);
        }

        return $auth;
    }

    public function getConnection() {
        return new CF_Connection($this->getAuthInstance());
    }

    public function getAuthToken() {
        return $this->getAuthInstance()->auth_token;
    }

    public function getStorageUrl() {
        return $this->getAuthInstance()->storage_url;
    }
}
