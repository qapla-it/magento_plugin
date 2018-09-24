<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2017 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MSP_Qapla_Block_Adminhtml_System_Config_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('msp_qapla/system/config/button.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        unset($element);
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    protected function _getCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/qapla_checkapi');
    }

    /**
     * @return string
     */
    protected function _getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'id'        => 'atwixtweaks_button',
                'label'     => $this->helper('adminhtml')->__('Check API KEY'),
                'onclick'   => 'javascript:checkApi();'
                )
            );

        return $button->toHtml();
    }
}
