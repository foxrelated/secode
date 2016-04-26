<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poke
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FriendsController.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Poke_FriendsController extends Core_Controller_Action_User {

  public function init() {
    // Try to set subject
    $user_id = $this->_getParam('user_id', null);
    if ($user_id && !Engine_Api::_()->core()->hasSubject()) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if ($user) {
        Engine_Api::_()->core()->setSubject($user);
      }
    }  
  }

	public function suggestAction() {
    $subject_guid = $this->_getParam('subject', null);
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($subject_guid && (stripos($subject_guid, 'event') !== false || stripos($subject_guid, 'group') !== false)) {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    } else {
      $subject = $viewer;
    }
    if (!$viewer->getIdentity()) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      $select = $subject->membership()->getMembersObjectSelect();

      if (0 < ($limit = (int) $this->_getParam('limit', 10))) {
        $select->limit($limit);
      }

      if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
        $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
      }
      $select->where('`' . $table->info('name') . '`.`user_id` <> ?', $viewer->getIdentity());
      $select->order("{$table->info('name')}.displayname ASC");

      $ids = array();
      foreach ($select->getTable()->fetchAll($select) as $friend) {
        $data[] = array(
            'type' => 'user',
            'id' => $friend->getIdentity(),
            'guid' => $friend->getGuid(),
            'label' => $friend->getTitle(),
            'photo' => $this->view->itemPhoto($friend, 'thumb.icon','', array('nolazy' => true)),
            'url' => $friend->getHref(),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

}