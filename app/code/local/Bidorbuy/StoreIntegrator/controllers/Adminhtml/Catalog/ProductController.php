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

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'ProductController.php');

class Bidorbuy_StoreIntegrator_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController {
    public function bobCategoriesJsonAction() {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('storeintegrator/adminhtml_catalog_product_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}