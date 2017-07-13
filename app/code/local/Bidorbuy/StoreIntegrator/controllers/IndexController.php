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

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

class Bidorbuy_StoreIntegrator_IndexController extends Mage_Core_Controller_Front_Action {
    /**
     * @var array of strings
     */
    public $shippingMethodsTitles = array();

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        $this->shippingMethodsTitles = $this->getShippingMethodsTitles();
//        $this->onOffCache(0);

        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * @param bool $onOff - true - enable caching, false - disable caching.
     */
    private function onOffCache($onOff = true) {
        $onOff = (bool)$onOff;

        if (!$onOff) {
            Mage::app()->getCacheInstance()->flush();
            Mage::app()->cleanCache();
        }

        $model = Mage::getModel('core/cache');
        $options = $model->canUse();
        foreach ($options as $option => $value) {
            $options[$option] = $onOff;
        }
        $model->saveOptions($options);
    }

    public function downloadAction() {
        $this->download($this->getRequest()->getParam(bobsi\Settings::paramToken));
    }

    public function downloadlAction() {
        $this->downloadl($this->getRequest()->getParam(bobsi\Settings::paramToken));
    }

    public function versionAction() {
        $this->version(
            $this->getRequest()->getParam(bobsi\Settings::paramToken),
            'y' == $this->getRequest()->getParam('phpinfo', 'n'));
    }

    public function download($token) {
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->download($token);
    }

    public function downloadl($token) {
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->downloadl($token);
    }

    public function version($token, $phpinfo = false) {
        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->showVersion($token, $phpinfo);
    }

    public function exportAction() {
        $this->export($this->getRequest()->getParam(bobsi\Settings::paramToken), $this->getRequest()->getParam(bobsi\Settings::paramIds));
    }

    private function export($token, $productsIds = false) {
        $exportConfiguration = array(
            bobsi\Settings::paramIds => $productsIds,
            bobsi\Tradefeed::settingsNameExcludedAttributes => array('Width', 'Height', 'Length', bobsi\Tradefeed::nameProductAttrShippingWeight),
            bobsi\Settings::paramCallbackGetProducts => array($this, 'getAllProducts'),
            bobsi\Settings::paramCallbackGetBreadcrumb => array($this, 'getBreadcrumb'),
            bobsi\Settings::paramCallbackExportProducts => array($this, 'exportProducts'),
            bobsi\Settings::paramCategories => Mage::helper('storeintegrator/data')->getExportCategoriesIds(bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getExcludeCategories()),
            bobsi\Settings::paramExtensions => array(),
        );

        //Add attributes excluded from Product Title
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
        foreach ($attributes as $attribute) {
            if (!$this->isAttributeUsedInProductTitle($attribute)) {
                $exportConfiguration[bobsi\Tradefeed::settingsNameExcludedAttributes][] = $attribute->getFrontendLabel();
            }
        }

        $modules = Mage::getConfig()->getNode('modules')->children();
        $extensions = &$exportConfiguration[bobsi\Settings::paramExtensions];
        foreach ($modules as $moduleName => $moduleInfo) {
            $extensions[$moduleName] = $moduleName . '/' . $moduleInfo->active . ' ' . $moduleInfo->version;
        }

        bobsi\StaticHolder::getBidorbuyStoreIntegrator()->export($token, $exportConfiguration);
    }

    public function getBreadcrumb($categoryId) {
        return Mage::helper('storeintegrator/data')->getBreadcrumb($categoryId);
    }

    public function &getProducts(&$exportConfiguration = array()) {
        /* @var $productsArray Mage_Catalog_Model_Resource_Product_Collection */

        $categoryId = $exportConfiguration[bobsi\Settings::paramCategoryId];

        $itemsPerIteration = intval($exportConfiguration[bobsi\Settings::paramItemsPerIteration]);
        $iteration = intval($exportConfiguration[bobsi\Settings::paramIteration]);
        if ($categoryId > 0) {
            $productsArray = Mage::getResourceModel('catalog/product_collection')
                ->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId))
//              This filter adds two huge strings to SQL query. We will filter this later.
//              ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToSort('entity_id', 'ASC')
                ->addAttributeToFilter('type_id', array('in' => array('simple', 'downloadable', 'virtual')));

            $productsArray->getSelect()->limit($itemsPerIteration, $itemsPerIteration * ($iteration));
        } else {
            $productsArray = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('status')
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->joinField('category_product', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');

            $productsArray->getSelect()->where('at_category_product.category_id IS NULL');
        }

        // Unfortunately, Magento doesn't return an empty page if it is not exist,
        // it always returns the last existing page instead. We have to catch this moment manually.
        if (($iteration + 1) > ceil($productsArray->getSize() / $itemsPerIteration)) return array();

        return array_keys($productsArray->getItems()); //or $productsArray->getAllIds()
    }

    public function getAllProducts() {
        return array_keys(Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('status')
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->getItems());
    }

    public function &exportProducts(array &$productsIds, &$exportConfiguration = array()) {
        $exportQuantityMoreThan = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getExportQuantityMoreThan();
        $defaultStockQuantity = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getDefaultStockQuantity();

        $exportProducts = array();
        foreach ($productsIds as $product) {
            $product = Mage::getModel('catalog/product')->load($product);

            $pc = $product->getCategoryIds();
            $pc = empty($pc) ? array('0') : $pc;

            $categoriesMatching = array_intersect($exportConfiguration[bobsi\Settings::paramCategories], $pc);

            if (empty($categoriesMatching)) {
                continue;
            }

            /* @var $product Mage_Catalog_Model_Product */
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->logInfo('Processing product id: ' . $product->getId());

            if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                bobsi\StaticHolder::getBidorbuyStoreIntegrator()->logInfo('Product status: disabled. Product id: ' . $product->getId());
                continue;
            }

            if ($this->calcProductQuantity($product, $defaultStockQuantity) > $exportQuantityMoreThan
                && intval(round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()))) != 0
            ) {
                $p = $this->buildExportProduct($product, $categoriesMatching);
                if (intval($p[bobsi\Tradefeed::nameProductPrice]) > 0) {
                    $exportProducts[] = $p;
                } else {
                    bobsi\StaticHolder::getBidorbuyStoreIntegrator()->logInfo('Product price <= 0, skipping, product id: ' . $product->getId());
                }
            } else {
                bobsi\StaticHolder::getBidorbuyStoreIntegrator()->logInfo('Price = 0 or QTY is not enough to export product id: ' . $product->getId());
            }
        }
        return $exportProducts;
    }

    private function &buildExportProduct(Mage_Catalog_Model_Product &$product, $categoriesMatching) {
        $exportedProduct = array();
        $hasParent = false;
        $parent = false;

        //Do we have parent?
        if ($product->getTypeId() == 'simple') {
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            if (!$parentIds) $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            if (isset($parentIds[0])) {
                $hasParent = true;
                /* @var $parent Mage_Catalog_Model_Product */
                $parent = Mage::getModel('catalog/product')->load($parentIds[0]);
            }
        }


        $exportedProduct[bobsi\Tradefeed::nameProductId] = $product->getId();
        $exportedProduct[bobsi\Tradefeed::nameProductName] = $product->getName();

        $exportedProduct[bobsi\Tradefeed::nameProductCode] = $product->getId();
        if ($hasParent) {
            $exportedProduct[bobsi\Tradefeed::nameProductCode] = $parent->getId() . '-' . $exportedProduct[bobsi\Tradefeed::nameProductCode];
        }

        if (strlen($product->getSku())) {
            $exportedProduct[bobsi\Tradefeed::nameProductCode] .= '-' . $product->getSku();
        } elseif ($hasParent && strlen($parent->getSku())) {
            $exportedProduct[bobsi\Tradefeed::nameProductCode] .= '-' . $parent->getSku();
        }

        $categoriesBreadcrumbs = array();
        foreach ($categoriesMatching as $catId) {
            $categoriesBreadcrumbs[] = $this->getBreadcrumb($catId);
        }
        $exportedProduct[bobsi\Tradefeed::nameProductCategory] = implode(bobsi\Tradefeed::categoryNameDelimiter, $categoriesBreadcrumbs);

        $priceWithoutReduct = $product->getPrice(); //Regular price
        $priceFinal = Mage::helper('tax')->getPrice($product, $product->getFinalPrice()); //Regular price + Taxes + Discounts

        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $exportCurrency = Mage::getStoreConfig('bidorbuystoreintegrator/exportConfiguration/exportCurrency');
        if ($exportCurrency) {
            $priceWithoutReduct = Mage::helper('directory/data')->currencyConvert($product->getPrice(), $currentCurrencyCode, $exportCurrency); //Regular price
            $priceFinal = Mage::helper('directory/data')->currencyConvert(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()), $currentCurrencyCode, $exportCurrency); //Regular price + Taxes + Discounts
        }

        if ($priceFinal < $priceWithoutReduct) {
            $exportedProduct[bobsi\Tradefeed::nameProductPrice] = $priceFinal;
            $exportedProduct[bobsi\Tradefeed::nameProductMarketPrice] = $priceWithoutReduct;
        } else {
            $exportedProduct[bobsi\Tradefeed::nameProductPrice] = $priceFinal;
            $exportedProduct[bobsi\Tradefeed::nameProductMarketPrice] = '';
        }

        $exportedProduct[bobsi\Tradefeed::nameProductCondition] = bobsi\Tradefeed::conditionNew;

        if (!empty($this->shippingMethodsTitles)) {
            $exportedProduct[bobsi\Tradefeed::nameProductShippingClass] = implode(', ', $this->shippingMethodsTitles);
        }

        $exportedProduct[bobsi\Tradefeed::nameProductSummary] = Mage::helper('catalog/output')->productAttribute($product, $product->getShortDescription(), 'shortDescription');
        $exportedProduct[bobsi\Tradefeed::nameProductDescription] = Mage::helper('catalog/output')->productAttribute($product, $product->getDescription(), 'description');

        if (in_array('manufacturer', array_keys($product->getAttributes()))) {
            $exportedProduct[bobsi\Tradefeed::nameProductAttributes] = array(
                'Brand' => $product->getAttributeText('manufacturer')
            );
        }

        if (in_array('weight', array_keys($product->getAttributes()))) {
            $weight = $product->getWeight();
            if (is_numeric($weight) && $weight > 0) {
                $exportedProduct[bobsi\Tradefeed::nameProductAttributes][bobsi\Tradefeed::nameProductAttrShippingWeight] = number_format($weight, 2, '.', '');
                //$exportedProduct[bobsi\Tradefeed::nameProductAttributes][bobsi\Tradefeed::nameProductAttrWeight] = intval($weight);
            }
        }

        foreach ($product->getAttributes() as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($this->isAttributeUsedInFeed($attribute)) {
                $attributeValue = $product->getResource()->getAttribute($attribute->getAttributeCode())->getFrontend()->getValue($product);
                //manufacturer was added as `Brand` before
                if ($attributeValue && !empty($attributeValue) && $attribute->getAttributeCode() != 'manufacturer')
                    $exportedProduct[bobsi\Tradefeed::nameProductAttributes][$attribute->getFrontendLabel()] = $attributeValue;
            }
        }

        $exportedProduct[bobsi\Tradefeed::nameProductAvailableQty] = $this->calcProductQuantity($product, bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getDefaultStockQuantity());

        $imagePath = $product->getImage();
        $imageUrl = (!$imagePath || $imagePath == 'no_selection') ? '' : Mage::getModel('catalog/product_media_config')->getMediaUrl($imagePath);

        if ($hasParent && !$imageUrl) {
            $imagePath = $parent->getImage();
            $imageUrl = (!$imagePath || $imagePath == 'no_selection') ? '' : Mage::getModel('catalog/product_media_config')->getMediaUrl($imagePath);
        }

        $images = array();

        if ($imageUrl) {
            $images[] = $imageUrl;
        }

        foreach ($product->getMediaGalleryImages() as $image) {
            $images[] = $image->getUrl();
        }

        $exportedProduct[bobsi\Tradefeed::nameProductImageURL] = $imageUrl;
        $exportedProduct[bobsi\Tradefeed::nameProductImages] = $images;

        return $exportedProduct;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $default
     * @return int
     */
    private function calcProductQuantity($product, $default = 0) {
        $productStockModel = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        /* @var $productStockModel Mage_CatalogInventory_Model_Stock_Item */
        return ($productStockModel->getManageStock()) ? (($productStockModel->getIsInStock()) ? $productStockModel->getQty() : 0) : $default;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return boolean
     */
    public function isAttributeUsedInFeed($attribute) {
        if (!defined('BIDORBUY_ATTR_IS_USED_IN_FEED_NAME')) {
            new Bidorbuy_StoreIntegrator_Model_Observer();
        }

        return $attribute->getIsUserDefined() && is_numeric($attribute->getData(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME)) && intval($attribute->getData(BIDORBUY_ATTR_IS_USED_IN_FEED_NAME)) > 0;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return boolean
     */
    public function isAttributeUsedInProductTitle($attribute) {
        if (!defined('BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME')) {
            new Bidorbuy_StoreIntegrator_Model_Observer();
        }

        return $attribute->getIsUserDefined() && is_numeric($attribute->getData(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME)) && intval($attribute->getData(BIDORBUY_ATTR_IS_USED_IN_PRODUCT_TITLE_NAME)) > 0;
    }

    /**
     * @return array of strings
     */
    public function getShippingMethodsTitles() {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = array();

        foreach ($methods as $_code => $_method) {
            $options[] = ($_title = Mage::getStoreConfig("carriers/$_code/title")) ? $_title : $_code;
        }

        return $options;
    }

    function devAction() {
    }
}