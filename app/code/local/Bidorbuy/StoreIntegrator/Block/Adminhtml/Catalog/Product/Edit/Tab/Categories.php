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

require_once(Mage::getModuleDir('', 'Bidorbuy_StoreIntegrator') . DS . 'Helper' . DS . 'Bidorbuy.php');

class Bidorbuy_StoreIntegrator_Block_Adminhtml_Catalog_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories {
    /**
     * Return array with category IDs which the product is assigned to
     *
     * @return array
     */
    function getCategoryIds() {
        // Please don't be confused, excludeCategories contains checked/marked included categories.
        // Defect #3595: Saved categories are not updated correctly and there is a difference between included categories before and after saving.
        // (we don't need to reverse categories here, if we use instance of integrator to fetch excluded categories
        // and reverse them it doesn't work on servers with additionall chaching mechanisms or magento optimization)
        //
        $excludeCategories = Mage::getStoreConfig('bidorbuystoreintegrator/exportCriteria/excludeCategories');
        $categories = explode(',', $excludeCategories);

        if (($key = array_search('1', $categories)) !== false) {
            unset($categories[$key]); //remove category 1
        }

        return $categories;
    }

    function isReadonly() {
        return false;
    }

    public function getLoadTreeUrl($expanded = null) {
        $params['id'] = 0;
        return Mage::helper('adminhtml')->getUrl('/catalog_product/bobCategoriesJson', $params);
    }
}