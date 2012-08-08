<?php
require_once "Mage/Downloadable/controllers/DownloadController.php";
class Meanbee_Rackspacecloud_DownloadController extends Mage_Downloadable_DownloadController {

    protected function _processDownload($url, $resourceType) {
        /** @var $connection  Mage_Rackspacecloud_Helper_Connection */
        $connection = Mage::getSingleton('rackspace/connection');

        $connection->getTempUrl($url);

        return parent::_processDownload($url, $resourceType);
    }
}
