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

class MSP_Qapla_Model_Queue
{

    public function process()
    {
        $collection = Mage::getModel('msp_qapla/queue_item')->getCollection();
        $collection->addFieldToFilter('status', ['neq' => MSP_Qapla_Model_Queue_Item::STATUS_COMPLETE]);

        foreach ($collection as $item) {
            try {
                $entity = $item->getReferencedEntity();

                switch ($item->getEntityType()) {
                    case Mage_Sales_Model_Order::ENTITY:
                        Mage::getModel('msp_qapla/qapla')->sendOrderInformation($entity);
                        break;
                    case Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME:
                        Mage::getModel('msp_qapla/qapla')->sendTrackingInformation($entity);
                        break;
                }

                $item->setStatus(MSP_Qapla_Model_Queue_Item::STATUS_COMPLETE);
            }
            catch (\Exception $e) {
                $item->setStatus(MSP_Qapla_Model_Queue_Item::STATUS_FAILED);
                $item->setMessage($e->getMessage());
            }

            $item->setProcessedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
            $item->save();
        }
    }


    /**
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment $entity
     * @return $this
     * @throws MSP_Qapla_Exception
     */
    public function add($entity)
    {
        $item = Mage::getModel('msp_qapla/queue_item');

        if ($entity instanceof Mage_Sales_Model_Order) {
            $item->setEntityType(Mage_Sales_Model_Order::ENTITY);
        }
        elseif ($entity instanceof Mage_Sales_Model_Order_Shipment) {
            $item->setEntityType(Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
        }
        else {
            throw new MSP_Qapla_Exception('invalid entity');
        }

        $item->setEntityId($entity->getId());

        $item->save();

        return $this;
    }
}