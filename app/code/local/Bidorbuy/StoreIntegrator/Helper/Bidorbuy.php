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

require_once(Mage::getBaseDir('lib') . DS . 'Bidorbuy' . DS . 'vendor' . DS . 'autoload.php');

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

if (empty(bobsi\Settings::$storeName)) {
    bobsi\Settings::$logsPath = Mage::getBaseDir('var') . DS . bobsi\Version::$id . DS . 'logs';
    bobsi\StaticHolder::getBidorbuyStoreIntegrator()->init(
        Mage::app()->getStore()->getName(),
        Mage::getStoreConfig('trans_email/ident_general/email'),
        'Magento ' . Mage::getVersion(),
        Mage::getStoreConfig('bidorbuystoreintegrator/exportConfiguration/encodedConfiguration')
    );
}

if (!class_exists('Bidorbuy_StoreIntegrator_Helper_Bidorbuy')) {
    class Bidorbuy_StoreIntegrator_Helper_Bidorbuy {
    }
}
