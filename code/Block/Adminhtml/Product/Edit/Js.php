<?php
class Meanbee_RackspaceCloudFiles_Block_Adminhtml_Product_Edit_Js extends Mage_Core_Block_Template {
    /**
     * @return Meanbee_RackspaceCloudFiles_Helper_Config
     */
    protected function _getConfig() {
        return Mage::helper('rackspace/config');
    }
}