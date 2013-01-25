<?php
require_once "Mage/Downloadable/controllers/DownloadController.php";
class Meanbee_RackspaceCloudFiles_DownloadController extends Mage_Downloadable_DownloadController {

    /**
     * @param $url
     * @param $resourceType
     */
    protected function _processDownload($url, $resourceType) {
        /** @var $config Meanbee_RackspaceCloudFiles_Helper_Config*/
        $config = Mage::helper('meanbee_rackspacecloudfiles/config');

        /** @var $helper Meanbee_RackspaceCloudFiles_Helper_Data */
        $helper = Mage::helper('meanbee_rackspacecloudfiles');

        $helper->log("Hit _processDownload.", Zend_Log::INFO);

        if ($config->isEnabled() && $config->isConfigured()) {
            $helper->log("Module is correctly configured and enabled.", Zend_Log::INFO);
            if ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $helper->log("Resource is a URL.", Zend_Log::INFO);

                /** @var $connection Meanbee_RackspaceCloudFiles_Model_Connection */
                $connection = Mage::getSingleton('meanbee_rackspacecloudfiles/connection');

                if ($helper->isRelevantUrl($url)) {
                    $helper->log("URL ($url) is relevant.", Zend_Log::INFO);
                    $protected_url = $connection->getTempUrl($url);
                    $helper->log("Temporary URL generated ($protected_url).", Zend_Log::INFO);

                    $this->getResponse()
                        // Temporary redirect to avoid caching
                        ->setHttpResponseCode(307)
                        ->setHeader("Location", $protected_url);

                    $this->getResponse()->clearBody();
                    $this->getResponse()->sendHeaders();

                    return;
                } else {
                    $helper->log("Not relevant URL: " . $url, Zend_Log::INFO);
                }
            }
        }

        return parent::_processDownload($url, $resourceType);
    }
}
