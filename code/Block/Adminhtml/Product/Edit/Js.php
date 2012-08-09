<?php
class Meanbee_Rackspacecloud_Block_Adminhtml_Product_Edit_Js extends Mage_Core_Block_Template {
    /**
     * @return Meanbee_Rackspacecloud_Helper_Config
     */
    protected function _getConfig() {
        return Mage::helper('rackspace/config');
    }
}