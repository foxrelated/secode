<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPhotoController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_AdminPhotoController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');       
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_submain', array(), 'sitegroupalbum_admin_submain_photo_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitegroupalbum', 'type' => 'photos'));
  }

  //ACTION FOR ADDING PHOTO OF THE DAY
  public function addPhotoOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitegroupalbum_Form_Admin_ItemOfDayday();
    $form->setTitle('Add a Photo of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Photo Title from the auto-suggest Photo Title field. The selected Photo will be displayed as "Photo of the Day" for this duration and if more than one photos are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Photo');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitegroup_photo');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitegroup_photo';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Group of the Day has been added successfully.'))
              ));
    }
  }

  public function photoOfDayAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');       

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_submain', array(), 'sitegroupalbum_admin_submain_photo_items');
    
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitegroupalbum_Form_Admin_Manage_Filter();
    $group = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $this->view->photoOfDaysList = $photoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup')->getItemOfDayList($values, 'photo_id', 'sitegroup_photo');
    $photoOfDay->setItemCountPerPage(50);
    $photoOfDay->setCurrentPageNumber($group);
  }

  //ACTION FOR DELETE PHOTO OF DAY
  public function deletePhotoOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));

      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-photo/deletephoto.tpl');
  }

  //ACTION FOR MULTI DELETE PHOTO ENTRIES
  public function multiDeletePhotoAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitegroupitemofthedays = Engine_Api::_()->getItem('sitegroup_itemofthedays', (int) $value);
          if (!empty($sitegroupitemofthedays)) {
            $sitegroupitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'photo-of-day'));
  }

  // ACTION FOR CHANGE SETTINGS OF TABBED ALBUM WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitegroupalbum_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF PHOTOS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
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

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}
?>
