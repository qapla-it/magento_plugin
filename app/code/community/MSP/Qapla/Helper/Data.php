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
class MSP_Qapla_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED = 'msp_qapla/general/enabled';
    const XML_PATH_API_KEY = 'msp_qapla/general/api_key';
    const XML_PATH_API_LANGUAGE = 'msp_qapla/general/api_language';
    const XML_PATH_MESSAGE = 'msp_qapla/general/message_template';
    const XML_PATH_SEND_MODE = 'msp_qapla/general/send_mode';
    const XML_PATH_COD_METHODS = 'msp_qapla/general/cashondelivery_methods';

    const API_URL = 'https://api.qapla.it/1.1/';

    const API_CHECK_PATH = "checkAPI";
    const API_TRACK_PATH = "getTrack";

    const API_SEND_ORDER_PATH = 'pushOrder';
    const API_SEND_SHIPMENT_PATH = 'pushTrack';

    /**
     * @param null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return (bool) Mage::getStoreConfig(static::XML_PATH_ENABLED, $store);
    }

    /**
     * @param null $store
     * @return null|string
     */
    public function getApiKey($store = null)
    {
        return Mage::getStoreConfig(static::XML_PATH_API_KEY, $store);
    }

    /**
     * @return string
     */
    public function getApiLanguage()
    {
        return Mage::getStoreConfig(static::XML_PATH_API_LANGUAGE);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return Mage::getStoreConfig(static::XML_PATH_MESSAGE);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getSendMode($store = null)
    {
        return Mage::getStoreConfig(static::XML_PATH_SEND_MODE, $store);
    }

    /**
     * @return string
     */
    public function getAuthParam()
    {
        return "auth=" . urlencode($this->getApiKey());
    }

    /**
     * @return string
     */
    public function getAuthUrl($key = null)
    {
        if(is_null($key)) {
            $key = $this->getApiKey();
        }
        return static::API_URL . static::API_CHECK_PATH . "/?" . "auth=" . urlencode($key);
    }

    /**
     * @return string
     */
    public function getSendTrackUrl()
    {
        return static::API_URL . ($this->getSendMode() == MSP_Qapla_Model_Source_Mode::MODE_ORDER? static::API_SEND_ORDER_PATH: static::API_SEND_SHIPMENT_PATH) . "/";
    }

    /**
     * @param $order
     * @return string
     */
    public function getTrackUrl(Mage_Sales_Model_Order $order)
    {
        if (!is_string($order)) {
            $order = $order->getIncrementId();
        }

        return static::API_URL . static::API_TRACK_PATH . "/?" . $this->getAuthParam()
            . "&reference=" . urlencode($order)
            . "&lang=" . urlencode($this->getApiLanguage());
    }

    /**
     * @return array
     */
    public function getCodMethods()
    {
        return explode(',', Mage::getStoreConfig(static::XML_PATH_COD_METHODS));
    }

    /**
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    public function isMethodCod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $method = $method->getCode();
        }

        $methods = $this->getCodMethods();

        return in_array($method, $methods);
    }

    public function getRealMethodName(Mage_Sales_Model_Order $order)
    {
        $method = $order->getPayment()->getMethod();

        switch ($method) {
            case "m2epropayment":
                $result = $this->getM2eProMethod($order);
                break;
            default:
                $result = $method;
                break;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getBaseCurrencyCode();
    }

    public function getM2eProMethod(Mage_Sales_Model_Order $order)
    {
        $payment = $order->getPayment();

        $info = @unserialize($payment->getMethodInstance()->getInfoInstance()->getAdditionalData());

        return $info["payment_method"] . " via " .$info["component_mode"];
    }
}
