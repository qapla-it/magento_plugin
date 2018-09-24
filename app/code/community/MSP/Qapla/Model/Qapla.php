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

class MSP_Qapla_Model_Qapla
{

    /**
     * @return MSP_Qapla_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('msp_qapla');
    }

    /**
     * @return bool
     */
    public function checkApiKey($key)
    {
        $url = $this->_getHelper()->getAuthUrl($key);

        $response = $this->_get($url);

        return $response['checkAPI']['result'] === "OK";
    }

    /**
     * @param $order
     * @return array
     */
    public function getTrackingStatus($order)
    {
        $url = $this->_getHelper()->getTrackUrl($order);

        $response = $this->_get($url);

        return $response;
    }

    /**
     * @param $order
     * @return $string
     */
    public function getTrackingStatusText($order)
    {
        $response = $this->getTrackingStatus($order);
        $message = $this->_getHelper()->getMessage();

        $helper = Mage::helper('msp_qapla/filter');

        if ($response['getTrack']['error']) {
            return $response['getTrack']['message'];
        }

        return $helper->filter($response['getTrack'], $message);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     */
    public function sendOrderInformation(Mage_Sales_Model_Order $order)
    {

        $address = $order->getShippingAddress();

        $parameters = array(
            'id' => $order->getIncrementId(),
            'status' => $order->getState(),
            'createdAt' => $order->getCreatedAt(),
            'updatedAt' => $order->getUpdatedAt(),
            'customerName' => $address->getName(),
            'customerAddress' => $address->getStreetFull(),
            'customerCity' => $address->getCity(),
            'customerState' => $address->getRegion(),
            'customerZip' => $address->getPostcode(),
            'customerCountry' => $address->getCountryModel()->getIso2Code(),
            'customerEmail' => $address->getEmail(),
            'customerTelephone' => $address->getTelephone(),
            'paymentType' => $this->_getHelper()->getRealMethodName($order),
            'amount' => $this->formatAmount($order->getBaseGrandTotal()),
        );

        $rows = array();

        foreach ($order->getAllVisibleItems() as $item) {
            /** @var  $item Mage_Sales_Model_Order_Item */
            $row = [
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'qty' => $item->getQtyOrdered(),
                'price' => $item->getPrice(),
                'total' => $item->getRowTotal()
            ];

            $rows[] = $row;
        }

        $parameters['rows'] = $rows;

        $url = $this->_getHelper()->getSendTrackUrl();
        return $this->_post(
            $url,
            array(
            'apiKey' => $this->_getHelper()->getApiKey($order->getStoreId()),
            'source' => 'magento',
            MSP_Qapla_Helper_Data::API_SEND_ORDER_PATH => array($parameters)
            )
        );
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return mixed
     */
    public function sendTrackingInformation(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $parameters = array();

        $order = $shipment->getOrder();
        $address = $shipment->getShippingAddress();

        $cod = $this->_getHelper()->isMethodCod($order->getPayment()->getMethod());

        foreach ($shipment->getAllTracks() as $track) {
            /**
 * @var $track Mage_Sales_Model_Order_Shipment_Track
*/
            $trackParams = array(
                'courier' => $track->getTitle(),
                'trackingNumber' => $track->getNumber(),
                'shipDate' => $this->_trimDate($shipment->getCreatedAt()),
                'reference' => $order->getIncrementId(),
                'orderDate' => $this->_trimDate($order->getCreatedAt()),
                'name' => $address->getName(),
                'street' => $address->getStreetFull(),
                'city' => $address->getCity(),
                'ZIP' => $address->getPostcode(),
                'state' => $address->getRegion(),
                'country' => $address->getCountryModel()->getIso2Code(),
                'email' => $address->getEmail(),
                'telephone' => $address->getTelephone(),
            );

            if ($cod) {
                $extra = array(
                    'pod' => 1,
                    'amount' => $this->formatAmount($order->getBaseGrandTotal())
                );
            } else {
                $extra = array(
                    'shipping' => $this->formatAmount($order->getBaseShippingAmount())
                );
            }

            $parameters[] = array_merge($trackParams, $extra);
        }

        $url = $this->_getHelper()->getSendTrackUrl();

        return $this->_post(
            $url,
            array(
            'apiKey' => $this->_getHelper()->getApiKey($order->getStoreId()),
            'source' => 'magento',
            MSP_Qapla_Helper_Data::API_SEND_SHIPMENT_PATH => $parameters
            )
        );
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function _get($url)
    {
        $client = new Varien_Http_Client($url);

        return $this->_request($client, 'GET');
    }

    /**
     * @param $url
     * @param array $params
     * @return mixed
     */
    protected function _post($url, $params = array())
    {
        $client = new Zend_Http_Client($url);

        $json = Mage::helper('core')->jsonEncode($params);

        $client->setHeaders('Content-type', 'application/json');

        $client->setRawData($json, null);

        return $this->_request($client, 'POST');
    }

    /**
     * @param Varien_Http_Client $client
     * @param $method
     * @return mixed
     * @throws MSP_Qapla_Exception
     */
    protected function _request(Zend_Http_Client $client, $method)
    {
        $response = $client->request($method);

        if ($response->getStatus() != "200") {
            throw new MSP_Qapla_Exception("Network error");
        }

        $json = Mage::helper('core')->jsonDecode($response->getBody());

        if (!is_array($json)) {
            throw new MSP_Qapla_Exception("Invalid data received");
        }

        return $json;
    }

    protected function _trimDate($datestring)
    {
        return substr($datestring, 0, 10);
    }

    /**
     * @param $amount
     * @return string
     */
    protected function formatAmount($amount)
    {
        return $this->_getHelper()->getCurrencyCode() . " " . $amount;
    }
}
