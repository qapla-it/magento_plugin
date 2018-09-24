<?php
/**
* MageSpecialist
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@magespecialist.it so we can send you a copy immediately.
*
* @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/ 
class MSP_Qapla_Model_Queue_Item extends Mage_Core_Model_Abstract
{

    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_COMPLETE = 'complete';

    protected function _construct()
    {
        $this->_init('msp_qapla/queue_item');
    }

    /**
     * @return Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment
     * @throws MSP_Qapla_Exception
     */
    public function getReferencedEntity()
    {
        $entity = false;

        switch ($this->getEntityType()) {
            case Mage_Sales_Model_Order::ENTITY:
                $entity = Mage::getModel('sales/order')->load($this->getEntityId());
                break;
            case Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME:
                $entity = Mage::getModel('sales/order_shipment')->load($this->getEntityId());
                break;
            default:
                throw new MSP_Qapla_Exception("Invalid entity type");
                break;
        }

        return $entity;

    }

}