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

class MSP_Qapla_Helper_Filter extends Mage_Core_Helper_Abstract
{

    protected $tags = array(
        'REFERENCE' => 'reference',
        'STATUS' => 'status',
        'COURIER' => 'courierInfo',
        'STATUS_DATE' => 'statusDate',
        'STATUS_PLACE' => 'courierPlace',
        'TRACKING_URL' => 'trackingUrl',
        'COURIER_LOGO' => 'courierLogo',
    );

    public function filter($response, $message)
    {

        foreach ($this->tags as $tag => $map) {
            $message = str_replace("[*" . $tag . "*]", $response[$map], $message);
        }

        return $message;
    }
}
