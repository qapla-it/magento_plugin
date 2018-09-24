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

class MSP_Qapla_Block_Track extends Mage_Core_Block_Template
{

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->getUrl('qapla/track', array('order' => $this->_getOrder()->getIncrementId()));
    }

    public function _toHtml()
    {
        if (!Mage::helper('msp_qapla')->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
