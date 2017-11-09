<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without
 * modification are not permitted without prior written approval by the copyright
 * holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

require_once(Mage::getModuleDir('', 'Bidorbuy_StoreIntegrator') . DS . 'Helper' . DS . 'Bidorbuy.php');

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

define('BIDORBUY_ATTR_IS_USED_IN_FEED_NAME', 'bidorbuy_feed');
define('BIDORBUY_ATTR_IS_USED_IN_FEED_LABEL', 'Used for bidorbuy Feed');
define('BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME', 'bidorbuy_in_title');
define('BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_LABEL', 'Used in title of a Product');

class Bidorbuy_StoreIntegrator_Model_Observer {
    // The configuration is already saved in magento database, this is the copy of saved configuration.
    // We use the copy to generate one line configuration and save it into database,
    // so to be used to load into integrator as one string on creating the integrator instance.
    public function handleAdminSystemConfigChangedSection() {
        $groups = Mage::app()->getRequest()->getParam('groups');

        $settings = array();
        if ($groups && count($groups)) {
            foreach ($groups as $group) {
                foreach ($group['fields'] as $key => $value) {
                    $settings[$key] = $value['value'];
                }
            }
        }

        if (!isset($settings[bobsi\Settings::nameExcludeCategories])
            || empty($settings[bobsi\Settings::nameExcludeCategories])
        ) {
            $settings[bobsi\Settings::nameExcludeCategories] = array();
        }

        $settings[bobsi\Settings::nameExcludeCategories] = Mage::helper('storeintegrator/data')
            ->getExportCategoriesIds($settings[bobsi\Settings::nameExcludeCategories]);
        $userName = Mage::getStoreConfig('bidorbuystoreintegrator/debug/username');
        $password = Mage::getStoreConfig('bidorbuystoreintegrator/debug/password');


        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->unserialize(serialize($settings));

        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->setUsername($userName);
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->setPassword($password);

        $value = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->serialize(TRUE);
        Mage::getModel('core/config')
            ->saveConfig('bidorbuystoreintegrator/exportConfiguration/encodedConfiguration', $value);

        // reset tokens
        if (Mage::app()->getRequest()->getParam(bobsi\Settings::nameActionReset)) {
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->processAction(bobsi\Settings::nameActionReset);

            $value = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->serialize(TRUE);
            Mage::getModel('core/config')
                ->saveConfig('bidorbuystoreintegrator/exportConfiguration/encodedConfiguration', $value);

            Mage::getSingleton('core/session')->addSuccess('Tokens are successfully reset.');
        }

        // download/remove logs
        if (Mage::app()->getRequest()->getParam(bobsi\Settings::nameLoggingFormAction)) {
            $data = array(
                bobsi\Settings::nameLoggingFormFilename =>
                    (Mage::app()->getRequest()->getParam(bobsi\Settings::nameLoggingFormFilename))
                        ? Mage::app()->getRequest()->getParam(bobsi\Settings::nameLoggingFormFilename)
                        : '');

            $result = bobsi\StaticHolder::getBidorbuyStoreIntegrator()
                ->processAction(Mage::app()->getRequest()->getParam(bobsi\Settings::nameLoggingFormAction), $data);

            // clear previous messages if they exist.
            Mage::getSingleton('core/session')->getMessages(TRUE);

            foreach ($result as $warn) {
                Mage::getSingleton('core/session')->addSuccess($warn);
            }
        }
    }


    /**
     * Add an extra field to new fieldset in accordance with Request #3633
     *
     * @param Varien_Event_Observer $observer observer
     *
     * @return void
     */
    public function addFieldsToAttributeEditForm(Varien_Event_Observer $observer) {
        $attrType = $observer->getEvent()->getAttribute()->getFrontendInput();
        $model = $observer->getEvent()->getAttribute()->getSourceModel() ?
            $observer->getEvent()->getAttribute()->getSourceModel() :
            $observer->getEvent()->getAttribute()->getBackendModel();

        //'eav/entity_attribute_source_boolean' - for yesno
        // attribute; 'eav/entity_attribute_backend_array' for multy select
        $disabledUsedInTitleField = isset($model)
            && (strpos($model, 'backend_array')
            || strpos($model, 'source_boolean')) ? TRUE : FALSE;

        if (isset($attrType) && is_string($attrType) && strpos($attrType, 'select') === FALSE) {
            return;
        }

        $fieldset = $observer->getForm()->addField('bidorbuy', 'fieldset', array(
            'legend' => Mage::helper('adminhtml')->__('bidorbuy Feed')
        ));

        $fieldset->addField(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME, 'select', array(
            'name' => BIDORBUY_ATTR_IS_USED_IN_FEED_NAME,
            'label' => Mage::helper('adminhtml')->__(BIDORBUY_ATTR_IS_USED_IN_FEED_LABEL),
            'values' => array('1' => 'Yes', '0' => 'No')
        ));
        $fieldset->addField(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME, 'select', array(
            'name' => BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
            'label' => Mage::helper('adminhtml')->__('Used in title of a Product'),
            'values' => array('1' => 'Yes', '0' => 'No'),
            'disabled' => $disabledUsedInTitleField ? 'disabled' : ''
        ));
    }

    public function addColumnsToGrid(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();

        switch ($block->getType()) {
            case 'adminhtml/catalog_product_attribute_grid':
                /* @var $block Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid */
                $block->addColumn(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME, array(
                    'header' => Mage::helper('adminhtml')->__(BIDORBUY_ATTR_IS_USED_IN_FEED_LABEL),
                    'index' => BIDORBUY_ATTR_IS_USED_IN_FEED_NAME,
                    'filter' => FALSE,
                    'type' => 'options',
                    'options' => array(
                        '1' => Mage::helper('catalog')->__('Yes'),
                        '0' => Mage::helper('catalog')->__('No'),
                    ),
                    'align' => 'center',
                    //It have to invoke $this->filterBidorbuyFields(), after attempt to filter this column but don't
                    //'filter_condition_callback' => array($this, 'filterBidorbuyFields'), 
                ));
                $block->addColumn(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME, array(
                    'header' => Mage::helper('adminhtml')->__(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_LABEL),
                    'index' => BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
                    'filter' => FALSE,
                    'type' => 'options',
                    'options' => array(
                        '1' => Mage::helper('catalog')->__('Yes'),
                        '0' => Mage::helper('catalog')->__('No'),
                    ),
                    'align' => 'center',
                ));
                break;
        }
    }

    public function loadBidorbuyFields(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();

        if (isset($collection) && is_a($collection, 'Mage_Index_Model_Resource_Process_Collection')) {
            /* @var $collection Mage_Index_Model_Resource_Process_Collection */
            // Manipulate $collection here to add columns
            $collection->addExpressionFieldToSelect(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME, '', array());
            $collection->addExpressionFieldToSelect(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME, '', array());
        }
    }

}