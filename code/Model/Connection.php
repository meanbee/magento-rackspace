<?php
require_once "php-opencloud/lib/php-opencloud.php";

use OpenCloud\Common\Exceptions\HttpError;
use OpenCloud\Rackspace;

class Meanbee_RackspaceCloudFiles_Model_Connection extends Mage_Core_Model_Abstract {

    /** @var Rackspace $_connection */
    protected $_connection;

    /**
     * Generate the temporary URL for a file.
     *
     * @param $url
     * @return mixed
     */
    public function getTempUrl($url) {
        $containerInfo = $this->_getContainerInfo($url);
        if (empty($containerInfo)) {
            Mage::throwException("Could not find the Rackspace Container for URL ($url). Please check the module configuration.");
        }
        /** @var OpenCloud\ObjectStore\Container $container */
        $container = $this->getObjectStore()->Container($containerInfo['name']);

        $objectName = $this->_getObjectName($url, $containerInfo['url']);
        $object = $container->DataObject($objectName);

        return $object->TempUrl(
            $this->getSharedSecret(),
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
        $this->getHelper()->log("Acquiring container info based on URL ($url)", Zend_Log::INFO);
        $container_map = $this->getCdnContainerMap();
        foreach ($container_map as $key => $value) {
            $keyLength = strlen($key);
            $containerCdn = substr($url, 0, $keyLength);
            if ($containerCdn == $key) {
                $this->getHelper()->log("Container info found: name ($value), url ($key)", Zend_Log::INFO);

                return array (
                    "name" => $value,
                    "url" => $key
                );
            }
        }
        $this->getHelper()->log("Container could not be found.", Zend_Log::INFO);
    }

    /**
     * @return Meanbee_RackspaceCloudFiles_Helper_Config
     */
    public function getConfig() {
        return Mage::helper('meanbee_rackspacecloudfiles/config');
    }

    /**
     * @return Meanbee_RackspaceCloudFiles_Helper_Cache
     */
    public function getCache() {
        return Mage::helper('meanbee_rackspacecloudfiles/cache');
    }

    /**
     * @return Meanbee_RackspaceCloudFiles_Helper_Data
     */
    public function getHelper() {
        return Mage::helper('meanbee_rackspacecloudfiles');
    }

    /**
     * @param bool $force_auth If set to true, will force the connection to re-authenticate and replace the
     *                         cached authentication credentials with the new ones.
     * @return OpenCloud\Rackspace
     */
    public function getConnection($force_auth = false) {
        $connection = $this->_connection;

        if (!($connection instanceof Rackspace) || $force_auth) {
            $this->getHelper()->log("Creating new Rackspace connection.", Zend_Log::INFO);

            $username = $this->getConfig()->getRackspaceUsername();
            $api_key = $this->getConfig()->getRackspaceApiKey();
            $region = $this->getConfig()->getRackspaceRegion();

            // The API endpoint depends on the Region used for the services
            $endpoint = RACKSPACE_US;
            if ($region == "LON") {
                $endpoint = RACKSPACE_UK;
            }

            $connection = new Rackspace($endpoint, array(
                'username' => $username,
                'apiKey'   => $api_key
            ));

            $credentials = $this->getCache()->getCredentials();

            if ($credentials === false || $force_auth) {
                $this->getHelper()->log("Authenticating with Rackspace.", Zend_Log::INFO);
                $connection->Authenticate();
                $this->getCache()->setCredentials($connection->ExportCredentials());
                $this->getHelper()->log("Authenticated successfully and cached authentication credentials.", Zend_Log::INFO);
            } else {
                $this->getHelper()->log("Cached authentication credentials found.", Zend_Log::INFO);
                $connection->ImportCredentials($credentials);
                $this->getHelper()->log("Cached authentication credentials loaded.", Zend_Log::INFO);
            }

            $this->_connection = $connection;
        }

        return $connection;
    }

    /**
     * @return OpenCloud\ObjectStore
     */
    public function getObjectStore() {
        $connection = $this->getConnection();

        $region = $this->getConfig()->getRackspaceRegion();

        return $connection->ObjectStore('cloudFiles', $region);
    }

    /**
     * Generate a map of CDN urls to containers.  Uses cache if available.

     * @param bool $force_new If set to true, will force a new value to be generated and save that as the new cached
     *                        value.
     * @return array
     */
    public function getCdnContainerMap($force_new = false) {
        $this->getHelper()->log("Generating CDN container map.", Zend_Log::INFO);
        $cdn_map = $this->getCache()->getCdnContainerMap();

        if ($cdn_map === false || $force_new) {
            $this->getHelper()->log(".", Zend_Log::INFO);
            $cdn_map = array();

            $container_list = $this->getObjectStore()->ContainerList();

            /** @var OpenCloud\ObjectStore\Container $container */
            while ($container = $container_list->Next()) {
                $container->EnableCDN();

                $cdn_map[$container->CDNURI()] = $container->Name();
                $cdn_map[$container->SSLURI()] = $container->Name();
            }

            $this->getCache()->setCdnContainerMap($cdn_map);
        } else {
            $this->getHelper()->log("Map cached map data found.", Zend_Log::INFO);
        }

        $this->getHelper()->log("CDN container map created.", Zend_Log::INFO);

        return $cdn_map;
    }

    /**
     * Get the shared secret that's used when generating the temporary url for a file.  If we don't know of one, then
     * set a new one in the account and cache.
     *
     * There are possible concurrency issues here. The request to update a remote shared secret takes several seconds.
     * In this time, we could see several requests occur.
     *
     * @param bool $force_new If set to true, will force a new value to be generated and save that as the new cached
     *                        value.
     * @return string
     */
    public function getSharedSecret($force_new = false) {
        $this->getHelper()->log("Getting shared secret.", Zend_Log::INFO);
        $secret_key = $this->getCache()->getSharedSecret();

        if ($secret_key === false || $force_new) {
            $secret_key = $this->getHelper()->getRandomSecretKey();
            $this->getHelper()->log("No cached shared secret found, setting to $secret_key.", Zend_Log::INFO);

            /*
             * Attempt to update the remote shared secret, if this fails then reauth
             * and then try again. If this fails once more, we can't accurately diagnose the
             * problem so the best course of action is to except.
             */
            if ($this->_updateRemoteSharedSecret($secret_key) == false) {
                $this->getHelper()->log("Request failed, requiring authorisation credentials.", Zend_Log::INFO);
                $this->getConnection(true);
                if ($this->_updateRemoteSharedSecret($secret_key) == false) {
                    $this->getHelper()->log("Request failed, again. Generating exception.", Zend_Log::INFO);
                    Mage::throwException("Problem with Rackspace API or module configuration.");
                }
            }

            $this->getCache()->setSharedSecret($secret_key);
        }

        $this->getHelper()->log("Got secret key ($secret_key)", Zend_Log::INFO);

        return $secret_key;
    }

    /**
     * Attempt to set the remove secret. Return success or failure.
     *
     * @param string $secret_key The string to set the remote secret key to.
     *
     * @return bool success or failure
     */
    protected function _updateRemoteSharedSecret($secret_key) {
        $object_store = $this->getObjectStore();

        try {
            $object_store->SetTempUrlSecret($secret_key);
        } catch (HttpError $error) {
            return false;
        }

        return true;
    }
}
