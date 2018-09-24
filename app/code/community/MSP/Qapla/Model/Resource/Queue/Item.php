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
class MSP_Qapla_Model_Resource_Queue_Item extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('msp_qapla/msp_qapla_queue', 'msp_qapla_queue_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
            $object->setStatus(MSP_Qapla_Model_Queue_Item::STATUS_PENDING);
        }

        return parent::_beforeSave($object);
    }

}