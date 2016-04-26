<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_PackageController extends Core_Controller_Action_Standard {

  //ACTION FOR SHOW PACKAGES
  public function indexAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GROUP CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'create')->isValid())
      return;
      
    $this->view->group_id  = $this->_getParam('group_id');
    
    if (!Engine_Api::_()->sitegroup()->hasPackageEnable()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'create'), 'sitegroup_general', true);    
    }

    //Start Intergration extenaion work
    $this->view->business_id = $this->_getParam('business_id', null);
    $this->view->store_id = $this->_getParam('store_id', null);
    $this->view->page_id = $this->_getParam('page_id', null);
    //End Intergration extenaion work

    $this->view->parent_id = $this->_getParam('parent_id', null);
    $is_package_view = Zend_Registry::isRegistered('sitegroup_package_view') ? Zend_Registry::get('sitegroup_package_view') : null;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    //Start Coupon plugin work.
		$this->view->couponmodules_enabled =  $couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.

    $packages_select = Engine_Api::_()->getItemtable('sitegroup_package')->getPackagesSql(null);
    
    $paginator = Zend_Paginator::factory($packages_select);
    if (empty($is_package_view)) {
      $this->view->paginator = array();
    } else {
      $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('group'));
    }

//     $packagesCountSelect = Engine_Api::_()->getItemtable('sitegroup_package')->getPackagesSql(null);
//     $paginatorCountSelect = Zend_Paginator::factory($packagesCountSelect);
//     $totalItemCountSelect = $paginatorCountSelect->getTotalItemCount();
//     
//     if($this->view->paginator->getTotalItemCount() != $totalItemCountSelect) {
//       $this->view->showAllPackageMsg = 1;
//     }
    
  }

	//ACTION FOR PACKAGE DETAIL
  public function detailAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GROUP CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'create')->isValid())
      return;

    $id = $this->_getParam('id');
    if (empty($id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->package = Engine_Api::_()->getItem('sitegroup_package', $id);
  }

	//ACTION FOR PACKAGE UPDATION
  public function updatePackageAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $this->view->sitegroups_view_menu = 15;

		//GET GROUP ID GROUP OBJECT AND THEN CHECK VALIDATIONS
    $this->view->group_id = $group_id = $this->_getParam('group_id');
    if (empty($group_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->package = Engine_Api::_()->getItem('sitegroup_package', $sitegroup->package_id);
    $table = Engine_Api::_()->getItemtable('sitegroup_package');
    $packages_select = $table->getPackagesSql($sitegroup->getOwner())
            ->where("update_list = ?", 1)
            ->where("enabled = ?", 1)
            ->where("package_id <> ?", $sitegroup->package_id);
    $paginator = Zend_Paginator::factory($packages_select);

    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('group'));

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');
    $this->view->is_ajax = $this->_getParam('is_ajax', '');
    
    //Start Coupon plugin work.
		$couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.
  }

	//ACTION FOR PACKAGE UPGRADE CONFIRMATION
  public function updateConfirmationAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//GET GROUP ID GROUP OBJECT AND THEN CHECK VALIDATIONS
    $this->view->group_id = $group_id = $this->_getParam('group_id');
    if (empty($group_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->package_id = $this->_getParam('package_id');
    $package_chnage = Engine_Api::_()->getItem('sitegroup_package', $this->view->package_id);
    if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($sitegroup->getOwner()->level_id , explode(",", $package_chnage->level_id)))) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $getPackageAuth = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');

    if ($this->getRequest()->getPost()) {

      if (!empty($_POST['package_id'])) {
        $table = $sitegroup->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
          $is_upgrade_package = true;
          
          //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
          if($sitegroup->status == 'active')
            $sitegroup->cancel($is_upgrade_package);
          
          $sitegroup->package_id = $_POST['package_id'];
          $package = Engine_Api::_()->getItem('sitegroup_package', $sitegroup->package_id);

          $sitegroup->featured = $package->featured;
          $sitegroup->sponsored = $package->sponsored;
          $sitegroup->pending = 1;
          $sitegroup->expiration_date = new Zend_Db_Expr('NULL');
          $sitegroup->status = 'initial';
          if (($package->isFree()) && !empty($getPackageAuth)) {
            $sitegroup->approved = $package->approved;
          } else {
            $sitegroup->approved = 0;
          }

          if (!empty($sitegroup->approved)) {

            $sitegroup->pending = 0;
            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitegroup->expiration_date = '2250-01-01 00:00:00';

            if (empty($sitegroup->aprrove_date)) {
              $sitegroup->aprrove_date = date('Y-m-d H:i:s');
              if (!empty($sitegroup) && !empty($sitegroup->draft) && empty($sitegroup->pending)) {
                Engine_Api::_()->sitegroup()->attachGroupActivity($sitegroup);
              }
            }
          }
          $sitegroup->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'format' => 'smoothbox',
              'parentRedirect' => $this->view->url(array('action' => 'update-package', 'group_id' => $sitegroup->group_id), 'sitegroup_packages', true),
              'parentRedirectTime' => 15,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Group has been successfully changed.'))
      ));
    }
  }

	//ACTION FOR PACKAGE PAYMENT
  public function paymentAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET GROUP ID GROUP OBJECT AND THEN CHECK VALIDATIONS
    $group_id = $_POST['group_id_session'];
    if (empty($group_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $getPackageAuth = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');
    $package = Engine_Api::_()->getItem('sitegroup_package', $sitegroup->package_id);

    if ((!$package->isFree()) && !empty($getPackageAuth)) {
      $session = new Zend_Session_Namespace('Payment_Sitegroup');
      $session->group_id = $group_id;

      return $this->_helper->redirector->gotoRoute(array(), 'sitegroup_payment', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
    }
  }
  
    //ACTION FOR PACKAGE CANCEL
  public function cancelAction(){
    if( !($package_id = $this->_getParam('package_id')) ||
        !($package = Engine_Api::_()->getItem('sitegroup_package', $package_id)) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'package_id' => null));
    }

    $this->view->package_id = $package_id;
    $group_id = $this->_getParam('group_id');

    $this->view->form = $form = new Sitegroup_Form_Packagecancel();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Try to cancel
    $this->view->form = null;
    try {
      Engine_Api::_()->getItem('sitegroup_group', $group_id)->cancel();
      $this->view->status = true;
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->error = $e->getMessage();
    }
  }
  
}
?>