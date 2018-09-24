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

class MSP_Qapla_Qapla_CheckapiController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('msp_qapla');
    }

    public function indexAction()
    {
        $key = $this->getRequest()->getParam('apikey');
        $qapla = Mage::getModel('msp_qapla/qapla');
        $error = false;

        try {
            $res = $qapla->checkApiKey($key);
        } catch (MSP_Qapla_Exception $e) {
            $error = true;
            $res = $e->getMessage();
        }

        $json = Mage::helper('core')->jsonEncode(
            array(
            'error' => $error,
            'message' => $res,
            )
        );

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($json);
        return null;
    }
}
