<?php
class Meanbee_Rackspacecloud_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config {
    /**
     * @test
     */
    public function testClassAliases() {
        $this->assertBlockAlias('meanbee_rackspacecloudfiles/test', 'Meanbee_Rackspacecloud_Block_Test');
        $this->assertModelAlias('meanbee_rackspacecloudfiles/test', 'Meanbee_Rackspacecloud_Model_Test');
        $this->assertHelperAlias('meanbee_rackspacecloudfiles/test', 'Meanbee_Rackspacecloud_Helper_Test');
    }
}