<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageplaylistsController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_AdminManageplaylistsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_admin_main', array(), 'sesmusic_admin_main_playlists');

    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sesmusic');

    $this->view->formFilter = $formFilter = new Sesmusic_Form_Admin_FilterPlaylists();
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $playlists = Engine_Api::_()->getItem('sesmusic_playlist', $value);
          Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('playlist_id =?' => $value));
          $favouriteTable->delete(array('resource_type =?' => 'sesmusic_playlist', 'resource_id =?' => $value));
          $playlists->delete();
        }
      }
    }

    $values = array();

    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();
    
    $values = array_merge(array(
        'order' => $_GET['order'],
        'order_direction' => $_GET['order_direction'],
            ), $values);
    
    $this->view->assign($values);

    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $playlistTable = Engine_Api::_()->getDbTable('playlists', 'sesmusic');
    $playlistTableName = $playlistTable->info('name');

    $select = $playlistTable->select()
            ->setIntegrityCheck(false)
            ->from($playlistTableName)
            ->joinLeft($tableUserName, "$playlistTableName.owner_id = $tableUserName.user_id", null)
            ->order(( !empty($_GET['order']) ? $_GET['order'] : 'playlist_id' ) . ' ' . ( !empty($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC' ));

    if (!empty($_GET['owner_name']))
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');

    if (!empty($_GET['title']))
      $select->where($playlistTableName . '.title LIKE ?', '%' . $_GET['title'] . '%');

    if (!empty($_GET['creation_date']))
      $select->where($playlistTableName . '.creation_date LIKE ?', $_GET['creation_date'] . '%');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(100);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  //Delete Playlist Action
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->playlist_id = $id = $this->_getParam('id');

    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sesmusic');

    //Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $playlist = Engine_Api::_()->getItem('sesmusic_playlist', $id);
        Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('playlist_id =?' => $id));
        $favouriteTable->delete(array('resource_type =?' => 'sesmusic_playlist', 'resource_id =?' => $id));
        $playlist->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('You have successfully delete playlist.')
      ));
    }
    //Output
    $this->renderScript('admin-manageplaylists/delete.tpl');
  }

  //Featured Action
  public function featuredAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesmusic_playlist', $id);
      $item->featured = !$item->featured;
      $item->save();
    }
    $this->_redirect('admin/sesmusic/manageplaylists');
  }

  public function viewAction() {
    $this->view->item = Engine_Api::_()->getItem('sesmusic_playlist', $this->_getParam('id', null));
  }

}