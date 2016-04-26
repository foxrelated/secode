<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_AdminSettingsController extends Core_Controller_Action_Admin {

	public function __call($method, $params) {
			/*
				* YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
				* YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
				* REMEMBER:
				*    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
				*    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
				*/
			if (!empty($method) && $method == 'Sitegroupalbum_Form_Admin_Global') {

			}
			return true;
	}
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');      
  	
		if( $this->getRequest()->isPost() ) {
			$sitegroupKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings', null);
			if( !empty($sitegroupKeyVeri) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.lsettings', trim($sitegroupKeyVeri));
			}
			if( $_POST['sitegroupalbum_lsettings'] ) {
				$_POST['sitegroupalbum_lsettings'] = trim($_POST['sitegroupalbum_lsettings']);
			}
		}
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_main_settings');

    $this->view->form = $form = new Sitegroupalbum_Form_Admin_Global();
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

    // It is only for installtion time use after it remove
    if (Engine_Api::_()->sitegroup()->hasPackageEnable() && isset($values['include_in_package']) && !empty($values['include_in_package'])){
        Engine_Api::_()->sitegroup()->oninstallPackageEnableSubMOdules('sitegroupalbum');
      }

      // It is only for installtion time use after it remove
      if(array_key_exists('sitegroupalbum_photolightbox_show', $values)) {
        unset($values['sitegroupalbum_photolightbox_show']);
      }
      foreach ($values as $key => $value) {
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }   
      }

      //REDIRECTING TO "GLOBAL PAGE" FOR SHOWING TAB.
      $this->_helper->redirector->gotoRoute(array('route' => 'admin-default', 'action' => 'index', 'controller' => 'settings'));
    }
  }

  //ACTION FOR FAQ
  public function faqAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_main_faq');
  }


  public function addFeaturedAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');       

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_widget_settings');

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitegroupalbum_Form_Admin_FeaturedAlbum();
    $form->setTitle('Add an Photo as Featured')
            ->setDescription('Using the auto-suggest field below, choose the photo to be made featured.');
    $form->getElement('title')->setLabel('Photo Title');
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitegroup_photo', $values['resource_id']);
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured photo has been added successfully.'))
              ));
    }
  }

  //ACTION FOR PHOTO SUGGESTION DROP-DOWN
  public function getPhotoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $featured = $this->_getParam('featured', 0);
    $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
    $photoName = $photoTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $photoTable->select()
													->setIntegrityCheck(false)
													->from($photoName)
                          ->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array())
													->join($groupTableName, $groupTableName . '.group_id = '. $albumName . '.group_id',array('title AS group_title', 'photo_id as group_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $groupTableName . '.group_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitegroup_group')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($albumName.'.search = ?', 1)
                          ->where($photoName . '.title  LIKE ? ', '%' . $title . '%')
													->limit($limit)
													->order($photoName . '.title')
													->limit($limit);
		$select = $select
						->where($groupTableName . '.closed = ?', '0')
						->where($groupTableName . '.approved = ?', '1')
						->where($groupTableName . '.declined = ?', '0')
						->where($groupTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    
    if (!empty($featured))
      $select->where($photoName . ".featured = ?", 0);

    $photos = $photoTable->fetchAll($select);

    foreach ($photos as $photo) {
      $content_photo = $this->view->itemPhoto($photo, 'thumb.normal');
      $data[] = array(
          'id' => $photo->photo_id,
          'label' => $photo->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  public function featuredAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');        
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_main_photo_featured');

    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
    $photoName = $photoTable->info('name');
    $data = array();
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName);
    //if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array());
   // } 
    $select->where($photoName . ".featured = ?", 1)
            ->order($photoName . '.creation_date DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per group and current group number
    $paginator->setItemCountPerPage(50);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->isAlbum = true;
  }

  public function removeFeaturedAction() {

    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitegroup_photo', $this->_getParam('id'));
        $photo->featured = !$photo->featured;
        $photo->save();
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
    $this->renderScript('admin-settings/un-featured.tpl');
  }

  public function readmeAction() {
    
  }

}

?>