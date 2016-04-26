<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_AdminManageController extends Core_Controller_Action_Admin {

  protected $_pluginName = 'Videos Plugin';

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_manage');
    $this->view->formFilter = $formFilter = new Sesvideo_Form_Admin_Manage_Filter();
    $this->view->category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
    $this->view->subcat_id = isset($_GET['subcat_id']) ? $_GET['subcat_id'] : 0;
    $this->view->subsubcat_id = isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : 0;
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($_GET as $key => $value) {
      if ('' === $value) {
        unset($_GET[$key]);
      } else
        $values[$key] = $value;
    }
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $video = Engine_Api::_()->getItem('video', $value)->delete();
        }
      }
    }
    $table = Engine_Api::_()->getDbtable('videos', 'sesvideo');
    $tableName = $table->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $select = $table->select()
            ->from($tableName)
            ->setIntegrityCheck(false)
            ->joinLeft($tableUserName, "$tableUserName.user_id = $tableName.owner_id", 'username');

    $select->order('video_id DESC');
    // Set up select info
    if (isset($_GET['category_id']) && $_GET['category_id'] != 0)
      $select->where('category_id = ?', $values['category_id']);

    if (isset($_GET['subcat_id']) && $_GET['subcat_id'] != 0)
      $select->where('subcat_id = ?', $values['subcat_id']);

    if (isset($_GET['subsubcat_id']) && $_GET['subsubcat_id'] != 0)
      $select->where('subsubcat_id = ?', $values['subsubcat_id']);

    if (!empty($_GET['title']))
      $select->where('title LIKE ?', '%' . $values['title'] . '%');

    if (isset($_GET['is_featured']) && $_GET['is_featured'] != '')
      $select->where('is_featured = ?', $values['is_featured']);

    if (isset($_GET['is_hot']) && $_GET['is_hot'] != '')
      $select->where('is_hot = ?', $values['is_hot']);

    if (isset($_GET['is_sponsored']) && $_GET['is_sponsored'] != '')
      $select->where('is_sponsored = ?', $values['is_sponsored']);
		
	 if (isset($_GET['status']) && $_GET['status'] != '')
      $select->where($tableName.'.status = ?', $values['status']);
	 if (isset($_GET['type']) && $_GET['type'] != '')
      $select->where($tableName.'.type = ?', $values['type']);
		
    if (!empty($values['creation_date']))
      $select->where('date(' . $tableName . '.creation_date) = ?', $values['creation_date']);

    if (isset($_GET['location']) && $_GET['location'] != '')
      $select->where('location != ?', '');

    if (!empty($_GET['owner_name']))
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');

    if (isset($_GET['offtheday']) && $_GET['offtheday'] != '')
      $select->where($tableName . '.offtheday =?', $values['offtheday']);

    if (isset($_GET['rating']) && $_GET['rating'] != '') {
      if ($_GET['rating'] == 1):
        $select->where('rating != ?', 0);
      elseif ($_GET['rating'] == 0 && $_GET['rating'] != ''):
        $select->where('rating =?', 0);
      endif;
    }
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
    $this->view->plugin_name = $this->_pluginName;
  }

  public function chanelAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_managechanels');
    $this->view->formFilter = $formFilter = new Sesvideo_Form_Admin_Manage_Filter();
    $this->view->category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
    $this->view->subcat_id = isset($_GET['subcat_id']) ? $_GET['subcat_id'] : 0;
    $this->view->subsubcat_id = isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : 0;
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $value)->delete();
          $db->query("DELETE FROM engine4_video_chanelvideos WHERE chanel_id = " . $value);
        }
      }
    }
    $this->view->formFilter = $formFilter = new Sesvideo_Form_Admin_Manage_Filterchanel();
    $this->view->category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
    $this->view->subcat_id = isset($_GET['subcat_id']) ? $_GET['subcat_id'] : 0;
    $this->view->subsubcat_id = isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : 0;
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($_GET as $key => $value) {
      if ('' === $value) {
        unset($_GET[$key]);
      } else
        $values[$key] = $value;
    }
    $table = Engine_Api::_()->getDbtable('chanels', 'sesvideo');
    $tableName = $table->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $select = $table->select()
            ->from($tableName)
            ->setIntegrityCheck(false)
            ->joinLeft($tableUserName, "$tableUserName.user_id = $tableName.owner_id", 'username');
    $select->order('chanel_id DESC');
    // Set up select info
    if (isset($_GET['category_id']) && $_GET['category_id'] != 0)
      $select->where('category_id = ?', $values['category_id']);

    if (isset($_GET['subcat_id']) && $_GET['subcat_id'] != 0)
      $select->where('subcat_id = ?', $values['subcat_id']);

    if (isset($_GET['subsubcat_id']) && $_GET['subsubcat_id'] != 0)
      $select->where('subsubcat_id = ?', $values['subsubcat_id']);

    if (!empty($_GET['title']))
      $select->where('title LIKE ?', '%' . $values['title'] . '%');

    if (isset($_GET['is_featured']) && $_GET['is_featured'] != '')
      $select->where('is_featured = ?', $values['is_featured']);

    if (isset($_GET['is_hot']) && $_GET['is_hot'] != '')
      $select->where('is_hot = ?', $values['is_hot']);

    if (isset($_GET['is_verified']) && $_GET['is_verified'] != '')
      $select->where('is_verified = ?', $values['is_verified']);

    if (isset($_GET['is_sponsored']) && $_GET['is_sponsored'] != '')
      $select->where('is_sponsored = ?', $values['is_sponsored']);

    if (!empty($values['creation_date']))
      $select->where('date(' . $tableName . '.creation_date) = ?', $values['creation_date']);

    if (isset($_GET['location']) && $_GET['location'] != '')
      $select->where('location != ?', '');

    if (!empty($_GET['owner_name']))
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');

    if (isset($_GET['offtheday']) && $_GET['offtheday'] != '')
      $select->where($tableName . '.offtheday =?', $values['offtheday']);

    if (isset($_GET['rating']) && $_GET['rating'] != '') {
      if ($_GET['rating'] == 1):
        $select->where('rating != ?', 0);
      elseif ($_GET['rating'] == 0 && $_GET['rating'] != ''):
        $select->where('rating =?', 0);
      endif;
    }
    $page = $this->_getParam('page', 1);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
    $this->view->plugin_name = $this->_pluginName;
  }

  public function playlistAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_manageplaylists');
    $this->view->formFilter = $formFilter = new Sesvideo_Form_Admin_Manage_Filterplaylist();
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('sesvideo_playlist', $value)->delete();
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->query("DELETE FROM engine4_sesvideo_playlistvideos WHERE playlist_id = " . $value);
        }
      }
    }
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($_GET as $key => $value) {
      if ('' === $value) {
        unset($_GET[$key]);
      } else
        $values[$key] = $value;
    }
    $table = Engine_Api::_()->getDbtable('playlists', 'sesvideo');
    $tableName = $table->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $select = $table->select()
            ->from($tableName)
            ->setIntegrityCheck(false)
            ->joinLeft($tableUserName, "$tableUserName.user_id = $tableName.owner_id", 'username');
    $select->order('playlist_id DESC');
    // Set up select info

    if (!empty($_GET['title']))
      $select->where('title LIKE ?', '%' . $values['title'] . '%');

    if (isset($_GET['is_featured']) && $_GET['is_featured'] != '')
      $select->where('is_featured = ?', $values['is_featured']);

    if (isset($_GET['is_sponsored']) && $_GET['is_sponsored'] != '')
      $select->where('is_sponsored = ?', $values['is_sponsored']);

    if (!empty($values['creation_date']))
      $select->where('date(' . $tableName . '.creation_date) = ?', $values['creation_date']);

    if (!empty($_GET['owner_name']))
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');

    if (isset($_GET['offtheday']) && $_GET['offtheday'] != '')
      $select->where($tableName . '.offtheday =?', $values['offtheday']);


    $page = $this->_getParam('page', 1);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
    $this->view->plugin_name = $this->_pluginName;
  }

  //delete playlist
  public function deletePlaylistAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete Playlist?');
    $form->setDescription('Are you sure that you want to delete this playlist? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');

    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getItem('sesvideo_playlist', $id)->delete();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->query("DELETE FROM engine4_sesvideo_playlistvideos WHERE playlist_id = " . $id);
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Playlist Delete Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete-playlist.tpl');
  }

  //delete chanel
  public function deleteChanelAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete Channel?');
    $form->setDescription('Are you sure that you want to delete this channel? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');

    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $id)->delete();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->query("DELETE FROM engine4_video_chanelvideos WHERE chanel_id = " . $id);
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Chanel Delete Successfully.')
      ));
    }
  }

  public function verifiedAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->video_id = $id = $this->_getParam('id');
    $this->view->status = $status = $this->_getParam('status');
    $db = Engine_Db_Table::getDefaultAdapter();
    $table = 'chanels';
    $type_id = 'chanel_id';
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable($table, 'sesvideo')->update(array(
          'is_verified' => $status,
              ), array(
          $type_id . " = ?" => $id,
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);
  }

  public function hotAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->video_id = $id = $this->_getParam('id');
    $this->view->status = $status = $this->_getParam('status');
    $db = Engine_Db_Table::getDefaultAdapter();

    if ($this->_getParam('type', false)) {
      $table = 'chanels';
      $type_id = 'chanel_id';
    } else {
      $table = 'videos';
      $type_id = 'video_id';
    }
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable($table, 'sesvideo')->update(array(
          'is_hot' => $status,
              ), array(
          $type_id . " = ?" => $id,
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);
  }
	 public function approveAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->video_id = $id = $this->_getParam('id');
    $this->view->approve = $approve = $this->_getParam('approve');

    //$this->view->statusChange = $statusChange;
    // Check post
    //if( $this->getRequest()->isPost())
    // {
    $video = Engine_Api::_()->getItem('sesvideo_video', $id);
    $owner = $video->getOwner();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable('videos', 'sesvideo')->update(array(
          'approve' => $approve,
              ), array(
          "video_id = ?" => $id,
      ));
      $db->commit();
			if($approve){
				
				
				// insert action in a separate transaction if video status is a success
				$actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
				$db = $actionsTable->getAdapter();
				$db->beginTransaction();
				try {
					// new action
					$action = $actionsTable->addActivity($owner, $video, 'video_new');
					if ($action) {
						$actionsTable->attachActivity($action, $video);
					}	
					// notify the owner
					Engine_Api::_()->getDbtable('notifications', 'activity')
									->addNotification($owner, $owner, $video, 'video_approved');
					$db->commit();
				} catch (Exception $e) {
					$db->rollBack();
					throw $e; // throw
				}
			}else{
				Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner->email, 'NOTIFY_VIDEO_DISAPPROVED', array(
							'object_title' => $video->getTitle(),
							'object_link' => $video->getHref(),
							'host' => $_SERVER['HTTP_HOST'],
            ));
			}
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);
  }
	
  public function featureSponsoredAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->video_id = $id = $this->_getParam('id');
    $this->view->status = $status = $this->_getParam('status');
    $this->view->category = $category = $this->_getParam('category');
    $this->view->params = $params = $this->_getParam('param');
    if ($status == 1)
      $statusChange = ' ' . $category;
    else
      $statusChange = 'un' . $category;

    //$this->view->statusChange = $statusChange;
    // Check post
    //if( $this->getRequest()->isPost())
    // {
    if ($params == 'videos')
      $col = 'video_id';
    else if ($params == 'chanels')
      $col = 'chanel_id';
    else if ($params == 'playlists')
      $col = 'playlist_id';
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable($params, 'sesvideo')->update(array(
          'is_' . $category => $status,
              ), array(
          "$col = ?" => $id,
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);
  }

  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete Video?');
    $form->setDescription('Are you sure that you want to delete this video? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');

    $id = $this->_getParam('id');
    $this->view->video_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $video = Engine_Api::_()->getItem('video', $id);
        Engine_Api::_()->getApi('core', 'sesvideo')->deleteVideo($video);
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
  }

  public function killAction() {
    $id = $this->_getParam('video_id', null);
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $video = Engine_Api::_()->getItem('video', $id);
        $video->status = 3;
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //view item function
  public function viewAction() {
    $this->view->type = $type = $this->_getParam('type', 1);
    $id = $this->_getParam('id', 1);
    $item = Engine_Api::_()->getItem($type, $id);
    $this->view->item = $item;
  }

  //make item off the day
  public function ofthedayAction() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $type = $this->_getParam('type');
    $param = $this->_getParam('param');
    $this->view->form = $form = new Sesvideo_Form_Admin_Oftheday();
    if ($type == 'video') {
      $item = Engine_Api::_()->getItem('video', $id);
      $form->setTitle("Video of the Day");
      $form->setDescription('Here, choose the start date and end date for this video to be displayed as "Video of the Day".');
      if (!$param)
        $form->remove->setLabel("Remove as Video of the Day");
      $table = 'engine4_video_videos';
      $item_id = 'video_id';
    } elseif ($type == 'sesvideo_chanel') {
      $item = Engine_Api::_()->getItem('sesvideo_chanel', $id);
      $form->setTitle("Chanel of the Day");
      if (!$param)
        $form->remove->setLabel("Remove as Chanel of the Day");
      $form->setDescription('Here, choose the start date and end date for this channel to be displayed as "Channel of the Day".');
      $table = 'engine4_video_chanels';
      $item_id = 'chanel_id';
    } elseif ($type == 'sesvideo_playlist') {
      $item = Engine_Api::_()->getItem('sesvideo_playlist', $id);
      $form->setTitle("Playlist of the Day");
      if (!$param)
        $form->remove->setLabel("Remove as Playlist of the Day");
      $form->setDescription('Here, choose the start date and end date for this playlist to be displayed as "Playlist of the Day".');
      $table = 'engine4_sesvideo_playlists';
      $item_id = 'playlist_id';
    } elseif ($type == 'sesvideo_artist') {
      $item = Engine_Api::_()->getItem('sesvideo_artist', $id);
      $form->setTitle("Artist of the Day");
      $form->setDescription('Here, choose the start date and end date for this artist to be displayed as "Artist of the Day".');
      if (!$param)
        $form->remove->setLabel("Remove as Artist of the Day");
      $table = 'engine4_sesvideo_artists';
      $item_id = 'artist_id';
    }

    if (!empty($id))
      $form->populate($item->toArray());
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $values = $form->getValues();
      $values['starttime'] = date('Y-m-d', strtotime($values['starttime']));
      $values['endtime'] = date('Y-m-d', strtotime($values['endtime']));
      $db->update($table, array('starttime' => $values['starttime'], 'endtime' => $values['endtime']), array("$item_id = ?" => $id));
      if (isset($values['remove']) && $values['remove']) {
        $db->update($table, array('offtheday' => 0), array("$item_id = ?" => $id));
      } else {
        $db->update($table, array('offtheday' => 1), array("$item_id = ?" => $id));
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Successfully updated the item.')
      ));
    }
  }

}
