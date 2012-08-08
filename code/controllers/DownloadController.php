<?php
require_once "Mage/Downloadable/controllers/DownloadController.php";
class Meanbee_Rackspacecloud_DownloadController extends Mage_Downloadable_DownloadController {

    /**
     * @TODO Check that the module is enabled and properly configured before attempting to generate a temporary URL
     *
     * @param $url
     * @param $resourceType
     */
    protected function _processDownload($url, $resourceType) {
        if ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {

            /** @var $connection  Mage_Rackspacecloud_Model_Connection */
            $connection = Mage::getSingleton('rackspace/connection');

            /** @var $helper Meanbee_Rackspacecloud_Helper_Data */
            $helper = Mage::helper('rackspace');

            if ($helper->isRelevantUrl($url)) {
                $protected_url = $connection->getTempUrl($url);

                $this->getResponse()
                    // Temporary redirect to avoid caching
                    ->setHttpResponseCode(307)
                    ->setHeader("Location", $protected_url);

                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                return;
            } else {
                $helper->log("Not relevant URL: " . $url);
            }
        }

        return parent::_processDownload($url, $resourceType);
    }
}
