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
class Bidorbuy_StoreIntegrator_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getExportCategoriesIds($ids = array()) {
        // try to explode if $ids is string
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (!is_array($ids)) {
            $ids = array();
        }

        $categoriesIds = array_merge(Mage::getModel('catalog/category')
            ->getTreeModel()
            ->load()
            ->getCollection()
            ->getAllIds(),
            array('0')
        );
        return array_values(array_diff($categoriesIds, $ids));
    }

    public function getBreadcrumb($categoryId) {
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();

        $category = Mage::getModel('catalog/category')->load($categoryId);
        $parentsIds = $category->getParentIds();

        $categories = array();

        $parents = $category->getCollection()
            ->addIdFilter($parentsIds)
            ->addAttributeToSelect('name')
            ->setOrder('level', Varien_Data_Collection::SORT_ORDER_ASC);

        foreach ($parents as $parentCat) {
            if ($parentCat->getId() != $rootCategoryId AND $parentCat->getId() != 1)
                array_push($categories, $parentCat->getName());
        }
        array_push($categories, $category->getName());

        return implode(' > ', $categories);
    }
}