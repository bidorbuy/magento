<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without modification
 * are not permitted without prior written approval by the copyright holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */
class Bidorbuy_StoreIntegrator_Model_System_Config_Source_ExportCurrency {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $arrayToReturn = array();
        $currencies = Mage::getBlockSingleton('directory/currency')->getCurrencies();
        if (!$currencies) {
            $currentCurrencyCode = Mage::getBlockSingleton('directory/currency')->getCurrentCurrencyCode();
            $currencies = array($currentCurrencyCode => Mage::app()->getLocale()->getTranslation($currentCurrencyCode, 'nametocurrency'));
        }
        foreach ($currencies as $code => $locale) {
            $arrayToReturn[] = array('value' => $code, 'label' => $locale . ' (' . $code . ')');
        }

        return $arrayToReturn;
    }
}