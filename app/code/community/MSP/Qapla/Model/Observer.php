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

class MSP_Qapla_Model_Observer
{

    /**
     * @return Mage_Core_Helper_Abstract|MSP_Qapla_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('msp_qapla');
    }

    protected function _getQueue()
    {
        return Mage::getModel('msp_qapla/queue');
    }

    public function salesOrderSaveAfter(Varien_Event_Observer $observer)
    {

        /**
         * @var $order Mage_Sales_Model_Order *
         */
        $order = $observer->getEvent()->getOrder();

        if (!$this->_getHelper()->isEnabled($order->getStoreId()) || $this->_getHelper()->getSendMode($order->getStoreId()) != MSP_Qapla_Model_Source_Mode::MODE_ORDER) {
            return;
        }

        if ($order->getState() == $order->getOrigData('state')) {
            return;
        }
        $this->_getQueue()->add($order);
    }

    public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer)
    {

        /**
         * @var Mage_Sales_Model_Order_Shipment_Track $track
         */
        $track = $observer->getEvent()->getTrack();

        if (!$this->_getHelper()->isEnabled($track->getStoreId()) || $this->_getHelper()->getSendMode($track->getStoreId()) != MSP_Qapla_Model_Source_Mode::MODE_SHIP) {
            return;
        }


        $this->_getQueue()->add($track->getShipment());
    }
}
