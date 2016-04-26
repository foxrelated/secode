<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_PaymentController extends Core_Controller_Action_Standard {

  /**
   * @var User_Model_User
   */
  protected $_user;
  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;
  /**
   * @var Payment_Model_Order
   */
  protected $_order;
  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;
  /**
   * @var Sitegroup_Model_Group
   */
  protected $_group;
  /**
   * @var Payment_Model_Package
   */
  protected $_package;
  protected $_success;

  public function init() {

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();

    // If no user, redirect to home?
    if (!$this->_user || !$this->_user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
    // If there are no enabled gateways or packages, disable
    if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
        Engine_Api::_()->getDbtable('packages', 'sitegroup')->getEnabledNonFreePackageCount() <= 0) {
      return $this->_forward('show-error');
    }
    $this->_session = new Zend_Session_Namespace('Payment_Sitegroup');
    $this->_success = new Zend_Session_Namespace('Payment_Sitegroup_Success');
  }

  public function indexAction() {
    return $this->_forward('gateway');
  }

  public function showErrorAction() {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main');
    if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
      $this->view->show = 1;
    } else {
      $this->view->show = 0;
    }
  }

  public function gatewayAction() {
    // Get subscription
    $groupId = $this->_getParam('group_id', $this->_session->group_id);
    if (!$groupId ||
        !($group = Engine_Api::_()->getItem('sitegroup_group', $groupId))) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
    $this->view->group = $group;

    // Check subscription status
    if ($this->_checkGroupStatus($group)) {
      return;
    }

    $existManageAdmin = Engine_Api::_()->sitegroup()->isGroupOwner($group);

    // Get subscription
    if (!$this->_user ||
        !( $groupId = $this->_getParam('group_id', $this->_session->group_id)) ||
        !($group = Engine_Api::_()->getItem('sitegroup_group', $groupId)) ||
        !$existManageAdmin ||
        !($package = Engine_Api::_()->getItem('sitegroup_package', $group->package_id))) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
    $this->view->group = $group;
    $this->view->package = $package;

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    // Gateways
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
            ->where('enabled = ?', 1)
    ;
    $gateways = $gatewayTable->fetchAll($gatewaySelect);

    $gatewayPlugins = array();
    foreach ($gateways as $gateway) {
      // Check billing cycle support
      if (!$package->isOneTime()) {
        $sbc = $gateway->getGateway()->getSupportedBillingCycles();
        if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
          continue;
        }
      }
      $gatewayPlugins[] = array(
              'gateway' => $gateway,
              'plugin' => $gateway->getGateway(),
      );
    }
    $this->view->gateways = $gatewayPlugins;
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main');
  }

  public function processAction() {
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if (!$gatewayId ||
        !($gateway = Engine_Api::_()->getItem('sitegroup_gateway', $gatewayId)) ||
        !($gateway->enabled)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }
    $this->view->gateway = $gateway;

    // Get subscription
    $groupId = $this->_getParam('group_id', $this->_session->group_id);
    if (!$groupId ||
        !($group = Engine_Api::_()->getItem('sitegroup_group', $groupId))) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
    $this->view->group = $group;

    // Get package
    $package = $group->getPackage();
    if (!$package || $package->isFree()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
    $this->view->package = $package;

    // Check subscription?
    if ($this->_checkGroupStatus($group)) {
      return;
    }

    // Process
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
            'user_id' => $this->_user->getIdentity(),
            'gateway_id' => $gateway->gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'sitegroup_group',
            'source_id' => $group->group_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->package_id);
    unset($this->_session->group_id);
    unset($this->_session->gateway_id);


    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();


    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $localeParts = explode('_', $this->_user->language);
    if (count($localeParts) > 1) {
      $params['region'] = $localeParts[1];
    }
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitegroup'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitegroup'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
        . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'sitegroup'), 'default')
        . '?order_id=' . $order_id;
    // Process transaction
    $transaction = $plugin->createGroupTransaction($this->_user, $group, $package, $params);

    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction() {
    // Get order
    if (!$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
        $order->user_id != $this->_user->getIdentity() ||
        $order->source_type != 'sitegroup_group' ||
        !($group = $order->getSource()) ||
        !($package = $group->getPackage()) ||
        !($gateway = Engine_Api::_()->getItem('sitegroup_gateway', $order->gateway_id))) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }

    $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.level.createhost', 0);

    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings', 0);
    $LevelHost = $this->checkLevelHost($levelHost, 'sitegroup');
    $PackagesHost = $this->checkPackageHost($package);
    if (($PackagesHost != $LevelHost)) {
      $this->_finishPayment('active');
    }

    $this->_group = $group;
    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onGroupTransactionReturn($order, $this->_getAllParams());
    } catch (Payment_Model_Exception $e) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }
    $this->_success->succes_id = $group->group_id;
    return $this->_finishPayment($status);
  }

  public function finishAction() {

    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main');
    if (isset($this->_success->succes_id)) {
      $this->view->id = $this->_success->succes_id;
      unset($this->_success->succes_id);
    }
  }

  protected function _checkGroupStatus(
  Zend_Db_Table_Row_Abstract $group = null) {
    if (!$this->_user) {
      return false;
    }

    if (null == $group) {
      $group = Engine_Api::_()->getItem('sitegroup_group', $this->_session->group_id);
    }


    if ($group->getPackage()->isFree()) {
      $this->_finishPayment('free');
      return true;
    }

    return false;
  }

  protected function _finishPayment($state = 'active') {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = $this->_group;

    // No user?
//    if (!$this->_group) {
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }
// @todo: work all here Sitegroup
    // Log the user in, if they aren't already
//    if (($state == 'active' || $state == 'free') &&
//        $this->_group &&
//        !$viewer->getIdentity()) {
//      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
//      Engine_Api::_()->user()->setViewer();
//      $viewer = $this->_user;
//    }
    // Handle email verification or pending approval
//    if ($viewer->getIdentity() && (!$viewer->enabled || !$viewer->verified)) {
//      Engine_Api::_()->user()->setViewer(null);
//      Engine_Api::_()->user()->getAuth()->getStorage()->clear();
//
//      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
//      $confirmSession->approved = $viewer->enabled;
//      $confirmSession->verified = $viewer->verified;
//      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
//    }
//
//    // Clear session
//    $errorMessage = $this->_session->errorMessage;
//    $userIdentity = $this->_session->user_id;
//    $this->_session->unsetAll();
//    $this->_session->user_id = $userIdentity;
//    $this->_session->errorMessage = $errorMessage;
    // Redirect

    if ($state == 'free') {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'controller' => 'payment', 'state' => $state), 'sitegroup_extended', true);
    }
  }

  public function checkLevelHost($object, $itemType) {
    $length = 7;
    $encodeorder = 0;
    $obj_length = strlen($object);
    if ($length > $obj_length)
      $length = $obj_length;
    for ($i = 0; $i < $length; $i++) {
      $encodeorder += ord($object[$i]);
    }
    $req_mode = $encodeorder % strlen($itemType);
    $encodeorder +=ord($itemType[$req_mode]);
    $isEnabled = Engine_Api::_()->sitegroup()->isEnabled();
    if (empty($isEnabled)) {
      return 0;
    } else {
      return $encodeorder;
    }
  }

  public function checkPackageHost($strKey) {
    $str = explode("-", $strKey);
    $str = $str[2];
    $char_array = array();
    for ($i = 0; $i < 6; $i++)
      $char_array[] = $str[$i];
    $key = array();
    foreach ($char_array as $value) {
      $v_a = ord($value);
      if ($v_a > 47 && $v_a < 58)
        continue;
      $possition = 0;
      $possition = $v_a % 10;
      if ($possition > 5)
        $possition -=4;
      $key[] = $char_array[$possition];
    }
    $isEnabled = Engine_Api::_()->sitegroup()->isEnabled();
    if (empty($isEnabled)) {
      return 0;
    } else {
      return $getStr = implode($key);
    }
  }

}
?>
