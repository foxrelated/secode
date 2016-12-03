<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Order.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_Order extends Core_Model_Item_Abstract {

    // Properties
    protected $_parent_is_owner = true;
    protected $_package;
    protected $_statusChanged;
    protected $_product;
    protected $_searchTriggers = false;

    public function getGatewayParams() {
        $params = array();

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        $orderTitle = '#' . $this->order_id;

        // General
        $params['name'] = $siteTitle . ' Order Nos ' . $orderTitle;
        $params['price'] = @round($this->grand_total, 2);
//    $params['tax'] = @round(10, 2);
        $params['description'] = 'Orders ' . $orderTitle . ' on ' . $siteTitle;
        $params['vendor_product_id'] = $this->getGatewayIdentity();
        $params['tangible'] = false;
        // Non-recurring
        $params['recurring'] = false;

        return $params;
    }

    public function onPaymentSuccess() {
        $this->_statusChanged = false;

        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->payment_status != 'active') {
                $this->payment_status = 'active';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentPending() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->payment_status != 'pending') {
                $this->payment_status = 'pending';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentFailure() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->payment_status != 'overdue') {
                $this->payment_status = 'overdue';
                $this->_statusChanged = true;
            }

            $session = new Zend_Session_Namespace('Payment_Siteeventticket');
            $session->unsetAll();
        }
        $this->save();
        return $this;
    }

    public function onRefund() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
            // Change status
            if ($this->payment_status != 'refunded') {
                $this->payment_status = 'refunded';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function getGatewayIdentity() {
        return $this->getProduct()->sku;
    }

    public function getProduct() {
        if (null === $this->_product) {
            $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
            $this->_product = $productsTable->fetchRow($productsTable->select()
                            ->where('extension_type = ?', 'siteeventticket_order')
                            ->where('extension_id = ?', $this->getIdentity())
                            ->limit(1));
            // Create a new product?
            if (!$this->_product) {
                $this->_product = $productsTable->createRow();
                $this->_product->setFromArray($this->getProductParams());
                $this->_product->save();
            }
        }

        return $this->_product;
    }

    public function getProductParams() {

        return array(
            'title' => 'order',
            'description' => 'siteeventticket',
            'price' => @round($this->grand_total, 2),
            'extension_type' => 'siteeventticket_order',
            'extension_id' => $this->order_id,
        );
    }
    
    public function showPrintLink() {

    if (in_array($this->gateway_id, array("3", "4", "5"))) {
      return true;
    } else {
      if ($this->order_status == 3) :
        $payment_status = 'marked as non-payment';
      elseif ($this->payment_status == 'active') :
        $payment_status = 'Yes';
      else:
        $payment_status = 'No';
      endif;
      if ($payment_status == 'Yes' && $this->order_status != 3) {
        return true;
      }
    }
    return false;
  }

}
