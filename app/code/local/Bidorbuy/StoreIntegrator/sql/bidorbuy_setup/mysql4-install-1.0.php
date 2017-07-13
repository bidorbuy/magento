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

//It loads the file with the class and constants which were defined there.
if (!defined(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME)) {
    new Bidorbuy_StoreIntegrator_Model_Observer();
}

/* @var $this Mage_Eav_Model_Entity_Setup */
 
// Add an extra columns to the catalog_eav_attribute-table:
$this->getConnection()->addColumn(
        $this->getTable('catalog/eav_attribute'), BIDORBUY_ATTR_IS_USED_IN_FEED_NAME, array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'unsigned'  => true,
            'default' => '0',
            'comment' => BIDORBUY_ATTR_IS_USED_IN_FEED_LABEL
    )
);
$this->getConnection()->addColumn(
        $this->getTable('catalog/eav_attribute'), BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME, array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'unsigned'  => true,
            'default' => '0',
            'comment' => BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_LABEL
    )
);