<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING THE ALBUMS
  public function indexAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');       
    
    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitegroupalbum_Form_Admin_Manage_Filter();

    //GET GROUP NUMBER
    $group = $this->_getParam('page', 1);

    //GET USER TABLE NAME
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    //GET SITEGROUP TABLE NAME
    $tablesitegroup = Engine_Api::_()->getItemTable('sitegroup_group')->info('name');

    //GET ALBUM TABLE
    $table = Engine_Api::_()->getDbtable('albums', 'sitegroup');

    //GET ALBUM TABLE NAME
    $rName = $table->info('name');

    //SELECT 
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('album_id', 'group_id', 'owner_id', 'creation_date', 'title', 'view_count', 'like_count', 'comment_count', 'default_value','featured','type'))
            ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
            ->joinLeft($tablesitegroup, "$rName.group_id = $tablesitegroup.group_id", 'title AS sitegroup_title');
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
        'order' => 'album_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'album_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    //MAKE PAGINATOR
    $this->view->paginator = array();
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber( $group );
  }

  //ACTION FOR MULTI DELETE ALBUM
  public function multiDeleteAction() {
    
    //GET ALBUM IDS
    $this->view->ids = $album_ids = $this->_getParam('ids', null);

    //COMFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT ALBUM IDS
    $this->view->count = count(explode(",", $album_ids));

    // FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //GETTING EXPLODES ALBUM IDS
      $album_ids_array = explode(",", $album_ids);
      foreach ($album_ids_array as $album_id) {
				//DELETE ALBUM AND IMAGE
				Engine_Api::_()->sitegroupalbum()->deleteContent($album_id);
      }
      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE ALBUM
  public function deleteAction() {
    
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //SEND ALBUM ID TO THE TPL
    $this->view->album_id = $album_id = $this->_getParam('id');

    //GET SITEGROUP ALBUM ITEM
    $sitegroupalbum = Engine_Api::_()->getItem('sitegroup_album', $album_id);

    //SEND DEFAULT ALBUM VALUE TO THE TPL
    $this->view->default_album_value = $sitegroupalbum->default_value;

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        
        //DELETE ALBUM AND IMAGE
        Engine_Api::_()->sitegroupalbum()->deleteContent($album_id);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    //OUTPUT
    $this->renderScript('admin-manage/delete.tpl');
  }

  //ACTION FOR MAKE ALBUM FEATURED AND REMOVE FEATURED ALBUM 
  public function featuredAction() {

    //GET OFFER ID
    $albumId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitegroupalbum = Engine_Api::_()->getItem('sitegroup_album', $albumId);
      if ($sitegroupalbum->featured == 0) {
        $sitegroupalbum->featured = 1;
      } else {
        $sitegroupalbum->featured = 0;
      }
      $sitegroupalbum->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitegroupalbum/manage');
  }

}

?>