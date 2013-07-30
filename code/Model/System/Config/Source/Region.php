<?php

class Meanbee_RackspaceCloudFiles_Model_System_Config_Source_Region {

    public function toOptionArray() {
        $options = array(
            array("value" => "DFW", "label" => "Dallas (DFW)"),
            array("value" => "ORD", "label" => "Chicago (ORD)"),
            array("value" => "LON", "label" => "London (LON)"),
            array("value" => "SYD", "label" => "Sydney (SYD)")
        );

        return $options;
    }
}
