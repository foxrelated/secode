<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR MAKING THE LIST FEATURED/UNFEATURED
  public function featuredAction() {

    $listing_id = $this->_getParam('listing_id');
    if (!empty($listing_id)) {
      $list = Engine_Api::_()->getItem('list_listing', $listing_id);
			$list->featured = !$list->featured;
			$list->save();
		}
    $this->_redirect('admin/list/viewlist');
  }

  //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
  public function sponsoredAction() {

    $listing_id = $this->_getParam('listing_id');
    if (!empty($listing_id)) {
      $list = Engine_Api::_()->getItem('list_listing', $listing_id);
			$list->sponsored = !$list->sponsored;
			$list->save();
		}
    $this->_redirect('admin/list/viewlist');
  }

  //ACTION FOR MAKING THE LIST APPROVE/DIS-APPROVE
  public function approvedAction() {

    $listing_id = $this->_getParam('listing_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $email = array();
    try {

      $list = Engine_Api::_()->getItem('list_listing', $listing_id);
      $email['subject'] = 'Approved/ Disapproved notification';
      $email['title'] = $list->title;
      $owner = Engine_Api::_()->user()->getUser($list->owner_id);
      $email['mail_id'] = $owner->email;
      $list->approved = !$list->approved;

      if (!empty($list->approved)) {
        if (empty($list->approved_date))
          $list->approved_date = date('Y-m-d H:i:s');
        $email['message'] = "Your list  \"" . $email['title'] . " \" approved ";
        Engine_Api::_()->list()->aprovedEmailNotification($list, $email);
      } else {
        $email['message'] = "Your list " . $email['title'] . "  disapproved ";
        Engine_Api::_()->list()->aprovedEmailNotification($list, $email);
      }
      $list->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/list/viewlist');
  }

  //ACTION FOR DELETE THE LISTING
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $listing_id = $this->_getParam('listing_id');
    $this->view->listing_listing_id = $listing_id;

    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getItem('list_listing', $listing_id)->delete();
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('Deleted Succesfully.')
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

}