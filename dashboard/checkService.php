<?php
    function isServiceDue($currentKilometers, $serviceKilometers, $theshold = 500) {
        $kilometersDifference = $serviceKilometers - $currentKilometers;

        // if($currentKilometers <= $theshold) {
        //     return true;
        // } else {
        //     return false;
        // }

        return $kilometersDifference;
    }
?>