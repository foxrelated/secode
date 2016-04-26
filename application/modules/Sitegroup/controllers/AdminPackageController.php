<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminPackageController extends Core_Controller_Action_Admin {

  public function init() {

    //TAB CREATION
    $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_package');

    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->_viewer_id = $this->_viewer->getIdentity();
  }

  //ACTION FOR MANAGE PACKAGE LISTINGS
  public function indexAction() {

    $this->view->canCreate = $canCreate = 1;
    if (Engine_Api::_()->sitegroup()->enablePaymentPlugin()) {

      //TEST CURL SUPPORT
      if (!function_exists('curl_version') ||
              !($info = curl_version())) {
        $this->view->error = $this->view->translate('The PHP extension cURL' .
                'does not appear to be installed, which is required' .
                'for interaction with payment gateways. Please contact your' .
                'hosting provider.');
      }
      //TEST CURL SSL SUPPORT
      else if (!($info['features'] & CURL_VERSION_SSL) ||
              !in_array('https', $info['protocols'])) {
        $this->view->error = $this->view->translate('The installed version of' .
                'the cURL PHP extension does not support HTTPS, which is required' .
                'for interaction with payment gateways. Please contact your' .
                'hosting provider.');
      }
      //CHECK FOR ENABLE PAYMENT GATEWAYS
      else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
        $this->view->error = $this->view->translate('There are currently no enabled payment gateways. You must %1$senable payment gatways%2$s before creating a paid package.', '<a href="' .
                $this->view->escape($this->view->url(array('module' => 'payment', 'controller' => 'gateway'))) .
                '"  target="_blank" >', '</a>');
      }
    } else {
      $this->view->canCreate = $canCreate = 0;
      $this->view->error = $this->view->translate('You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.');
    }

    //INITILIZE SELECT
    $table = Engine_Api::_()->getDbtable('packages', 'sitegroup');
    $groupName = Engine_Api::_()->getItemtable('sitegroup_group')->info("name");
    $select = $table->select();

    //FILTER FORM
    $this->view->formFilter = $formFilter = new Sitegroup_Form_Admin_Package_Filter();

    //PROCESS FORM
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    }
    if (empty($filterValues['order'])) {
      $select->order("order");
      $filterValues['order'] = 'package_id';
    }
    if (empty($filterValues['direction'])) {

      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order = $filterValues['order'];
    $this->view->direction = $filterValues['direction'];

    //ADD FILTER VALUES
    if (!empty($filterValues['query'])) {
      $select->where('title LIKE ?', '%' . $filterValues['query'] . '%');
    }

    if (isset($filterValues['enabled']) && '' != $filterValues['enabled']) {
      $select->where('enabled = ?', $filterValues['enabled']);
    }

    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'ASC';
      }
      $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }

    //GET DATA
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //GET GROUPS TOTALS FOR EACH PACKAGE
    $memberCounts = array();
    foreach ($paginator as $item) {
      $memberCounts[$item->package_id] = Engine_Api::_()->getDbtable('groups', 'sitegroup')
              ->select()
              ->from('engine4_sitegroup_groups', new Zend_Db_Expr('COUNT(*)'))
              ->where('package_id = ?', $item->package_id)
              ->query()
              ->fetchColumn();
    }
    $this->view->memberCounts = $memberCounts;
  }

  //ACTION FOR PACKAGE CREATE
  public function createAction() {

    if (!Engine_Api::_()->sitegroup()->enablePaymentPlugin()) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitegroup_Form_Admin_Package_Create();

    //GET SUPPORTED BILLING CYCLES
    $gateways = array();
    $supportedBillingCycles = array();
    $partiallySupportedBillingCycles = array();
    $fullySupportedBillingCycles = null;
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach ($gatewaysTable->fetchAll() as $gateway) {
      $gateways[$gateway->gateway_id] = $gateway;
      $supportedBillingCycles[$gateway->gateway_id] = $gateway->getGateway()->getSupportedBillingCycles();
      $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      if (null == $fullySupportedBillingCycles) {
        $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
      } else {
        $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      }
    }
    $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);

    $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);
    $form->getElement('recurrence')
            ->setMultiOptions($multiOptions);
    $form->getElement('recurrence')->options['forever'] = 'One-time';
    //$form->getElement('recurrence')->options['day'] = 'Day';

    $form->getElement('duration')
            ->setMultiOptions($multiOptions);
    $form->getElement('duration')->options/* ['Fully Supported'] */['forever'] = 'Forever';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $form->getElement('ads');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
      $form->getElement('twitter');
    }

    //FORM VALDIATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

     //PROCESS
    $values = $form->getValues();
    
    if(array_key_exists("product_type", $values)) {
      unset($values['product_type']);
    }
    
    if(array_key_exists("max_product", $values)) {
      unset($values['max_product']);
    }
    
    if(array_key_exists("comission_handling", $values)) {
      unset($values['comission_handling']);
    }
    
    if(array_key_exists("comission_fee", $values)) {
      unset($values['comission_fee']);
    }
    
    if(array_key_exists("comission_rate", $values)) {
      unset($values['comission_rate']);
    }
    
    if(array_key_exists("transfer_threshold", $values)) {
      unset($values['transfer_threshold']);
    }
    
    $tmp = $values['recurrence'];
    unset($values['recurrence']);
    if (empty($tmp) || !is_array($tmp)) {
      $tmp = array(null, null);
    }
    $values['recurrence'] = (int) $tmp[0];
    $values['recurrence_type'] = $tmp[1];

    if (!isset($values['ads'])) {
      $values['ads'] = 0;
    }

    if (!isset($values['twitter'])) {
      $values['twitter'] = 0;
    }

    if ($values['price'] > 0) {

      //FOR NOT ENABLE GATEWAYS
      if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
        $form->getDecorator('errors')->setOption('escape', false);

        $error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        return $form->addError($error);
      }
    }

    //for member level seting work
    if (@in_array('0', $values['level_id'])) {
      $values['level_id'] = 0;
    } else {
      $values['level_id'] = implode(',', $values['level_id']);
    }

    $tmp = $values['duration'];
    unset($values['duration']);
    if (empty($tmp) || !is_array($tmp)) {
      $tmp = array(null, null);
    }
    $values['duration'] = (int) $tmp[0];
    $values['duration_type'] = $tmp[1];
    if (isset($values['modules']))
      $values['modules'] = serialize($values['modules']);
    else
      $values['modules'] = serialize(array());

    $profileFields = array();
    if ($values['profile'] == 2) {
      foreach ($_POST as $key => $value) {
        if (@strstr($key, '_profilecheck_') != null && $value) {
          $tc = @explode("_profilecheck_", $key);
          $profileFields[] = "1_" . $tc[0] . "_" . $value;
        }
      }
    }
    $values['profilefields'] = serialize($profileFields);
    $packageTable = Engine_Api::_()->getDbtable('packages', 'sitegroup');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();

    try {

      include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';

      // Create package in gateways?
      if (!$package->isFree()) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();
          // Check billing cycle support
          if (!$package->isOneTime()) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
              continue;
            }
          }
          if (method_exists($gatewayPlugin, 'createProduct')) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
        }

        //START This code use for coupon edit when Create a new package and select all those coupon which have select all option for this package type.
        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon');
        if (!empty($moduleEnabled)) {
          Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->editCouponsAfterCreateNewPackage($package->getType());
        }
        //END COUPON WORK.
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  //ACTION FOR PACKAGE EDIT
  public function editAction() {

    if (!Engine_Api::_()->sitegroup()->enablePaymentPlugin()) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //GET PACKAGES
    if (null == ($packageIdentity = $this->_getParam('package_id')) ||
            !($package = Engine_Api::_()->getDbtable('packages', 'sitegroup')->find($packageIdentity)->current())) {
      throw new Engine_Exception('No package found');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitegroup_Form_Admin_Package_Edit();

    $values = $package->toArray();

    $values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);

    $values['duration'] = array($values['duration'], $values['duration_type']);

    unset($values['recurrence_type']);

    unset($values['duration_type']);
    $values['level_id'] = explode(',', $values['level_id']);

    $otherValues = array(
        'price' => $values['price'],
        'recurrence' => $values['recurrence'],
        'duration' => $values['duration'],
    );
    $oldValuesModules = $values['modules'] = unserialize($values['modules']);

    $form->populate($values);
    $profileFields = array();
    if ($values['profile'] == 2) {
      $profileFields = unserialize($values['profilefields']);
    }
    $session = new Zend_Session_Namespace('profileFields');
    $session->profileFields = $profileFields;

    //CHECK METHOD DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    if (isset($session->profileFields)) {
      unset($session->profileFields);
    }

    //HACK EM UP
    $form->populate($otherValues);

    //PROCESS
    $values = $form->getValues();

    //for member level seting work
    if (@in_array('0', $values['level_id'])) {
      $values['level_id'] = 0;
    } else {
      $values['level_id'] = implode(',', $values['level_id']);
    }

    if(array_key_exists("product_type", $values)) {
      unset($values['product_type']);
    }
    
    if(array_key_exists("max_product", $values)) {
      unset($values['max_product']);
    }
    
    if(array_key_exists("comission_handling", $values)) {
      unset($values['comission_handling']);
    }
    
    if(array_key_exists("comission_fee", $values)) {
      unset($values['comission_fee']);
    }
    
    if(array_key_exists("comission_rate", $values)) {
      unset($values['comission_rate']);
    }
    
    if(array_key_exists("transfer_threshold", $values)) {
      unset($values['transfer_threshold']);
    }

    unset($values['price']);
    unset($values['recurrence']);
    unset($values['recurrence_type']);
    unset($values['duration']);
    unset($values['duration_type']);
    unset($values['trial_duration']);
    unset($values['trial_duration_type']);
    if (isset($values['modules'])) {
      $newValuesModules = $values['modules'];
      $values['modules'] = serialize($values['modules']);
    } else {
			$newValuesModules = $values['modules'];
      $values['modules'] = serialize(array());
		}
    $profileFields = array();
    if ($values['profile'] == 2) {
      $i = 0;
      foreach ($_POST as $key => $value) {
        if (@strstr($key, '_profilecheck_') != null && $value) {
          $tc = @explode("_profilecheck_", $key);
          $profileFields[] = "1_" . $tc[0] . "_" . $value;
        }
      }
    }

    $values['profilefields'] = serialize($profileFields);

    $packageTable = Engine_Api::_()->getDbtable('packages', 'sitegroup');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();

    try {

			if(isset($oldValuesModules) && in_array("sitegroupevent", $oldValuesModules) && isset($newValuesModules) && !in_array("sitegroupevent", $newValuesModules)) {
					$table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
					$rName = $table->info('name');
					$select = $table->select()->from($rName, 'group_id')->where('package_id =?', $this->_getParam('package_id'));
		
					//START GROUP-EVENT CODE
					$sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
					$siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
					foreach ($table->fetchAll($select) as $group) {
							if ($sitegroupeventEnabled) {
									//FETCH Notes CORROSPONDING TO THAT Group ID
									$sitegroupeventtable = Engine_Api::_()->getItemTable('sitegroupevent_event');
									$select = $sitegroupeventtable->select()
													->from($sitegroupeventtable->info('name'), 'event_id')
													->where('group_id = ?', $group->group_id);
									$rows = $sitegroupeventtable->fetchAll($select)->toArray();
									if (!empty($rows)) {
											foreach ($rows as $key => $event_ids) {
													$event_id = $event_ids['event_id'];
													if (!empty($event_id)) {
															$sitegroupeventtable->update(array(
																	'search' => '0'
																			), array(
																	'event_id =?' => $event_id
															));
													}
											}
									}
							}

							if ($siteeventEnabled) {
									//FETCH Notes CORROSPONDING TO THAT Group ID
									$siteeventtable = Engine_Api::_()->getItemTable('siteevent_event');
									$select = $siteeventtable->select()
													->from($siteeventtable->info('name'), 'event_id')
													->where('parent_type = ?', 'sitegroup_group')
													->where('parent_id = ?', $group->group_id);
									$rows = $siteeventtable->fetchAll($select)->toArray();

									if (!empty($rows)) {
											foreach ($rows as $key => $event_ids) {
													$event_id = $event_ids['event_id'];
													if (!empty($event_id)) {
															$siteeventtable->update(array(
																	'search' => '0'
																			), array(
																	'event_id =?' => $event_id
															));
													}
											}
									}
							}
					}
			}

      include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';

      //CREATE PACKAGE IN GATEWAYS
      if (!$package->isFree()) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();

          //CHECK BILLING CYCLE SUPPORT
          if (!$package->isOneTime()) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
              continue;
            }
          }
          if (!method_exists($gatewayPlugin, 'createProduct') ||
                  !method_exists($gatewayPlugin, 'editProduct') ||
                  !method_exists($gatewayPlugin, 'detailVendorProduct')) {
            continue;
          }

          //IF IT THROWS AN EXCEPTION, OR RETURNS EMPTY, ASSUME IT DOESN'T EXIST?
          try {
            $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
          } catch (Exception $e) {
            $info = false;
          }
          //CREATE
          if (!$info) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
          //EDIT
          else {
            $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
          }
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  //ACTION FOR SHOW THE PACKAGE DETAILS
  public function packgeDetailAction() {
    $id = $this->_getParam('id');
    if (empty($id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->package = Engine_Api::_()->getItem('sitegroup_package', $id);
  }

  //ACTION FOR PACKAGE UPDATION
  public function updateAction() {

    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {

          $package = Engine_Api::_()->getItem('sitegroup_package', (int) $value);
          if (!empty($package)) {
            $package->order = $key + 1;
            $package->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $package = Engine_Api::_()->getItem('sitegroup_package', $id);
    if ($package->enabled == 0) {
      try {
        $package->enabled = 1;
        $package->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_redirect('admin/sitegroup/package');
    } else {
      if ($this->getRequest()->isPost()) {
        try {
          $package->enabled = 0;
          $package->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
        ));
      }
    }
  }

}

?>