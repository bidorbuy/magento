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

/**
 * Class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Logs.
 */
class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Logs
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    /**
     * Get Footer Html
     *
     * @param Varien_Data_Form_Element_Abstract $element element
     *
     * @return String
     *
     * @codingStandardsIgnoreStart
     */
    protected function _getFooterHtml($element) {
        // @codingStandardsIgnoreEnd
        $tooltipsExist = FALSE;
        $html = '</tbody></table>';

        $logsHtml = preg_replace(
            '/<form[^>]+\>/i', 
            '', 
            bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getLogsHtml()
        );
        $logsHtml = str_replace('</form>', '', $logsHtml);

        $html .= $logsHtml;

        $html .= '</fieldset>' . $this->_getExtraJs($element, $tooltipsExist);

        if ($element->getIsNested()) {
            $html .= '</div></td></tr>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }
}