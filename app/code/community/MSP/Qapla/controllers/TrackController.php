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

class MSP_Qapla_TrackController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $order = $this->getRequest()->getParam('order');

        $order = Mage::getModel('sales/order')->load($order, 'increment_id');
        $customer = Mage::getModel('customer/session')->getCustomer();

        if ($order->getCustomerId() == $customer->getId()) {
            $qapla = Mage::getModel('msp_qapla/qapla');
            $message = $qapla->getTrackingStatusText($order);

            $this->getResponse()->setBody($message);
        }
    }
}
