<?php
class Meanbee_Rackspacecloud_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config {
    /**
     * @test
     */
    public function testClassAliases() {
        $this->assertBlockAlias('rackspace/test', 'Meanbee_Rackspacecloud_Block_Test');
        $this->assertModelAlias('rackspace/test', 'Meanbee_Rackspacecloud_Model_Test');
        $this->assertHelperAlias('rackspace/test', 'Meanbee_Rackspacecloud_Helper_Test');
    }
}