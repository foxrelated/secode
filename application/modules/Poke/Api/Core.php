<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Core.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Api_Core extends Core_Api_Abstract {

  public function getPokesPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getPokesSelect($params));

    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if (empty($params['limit'])) {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('poke.page', 10);
      $paginator->setItemCountPerPage($page);
    }

    return $paginator;
  }

  public function turncation($string, $length=null) {
    if (empty($length)) {
      $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('poke.title.turncation', 16);
    }
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  public function levelSettings($subject) {

    //Getting the loggFed in user information.
    $viewer = Engine_Api::_()->user()->getViewer();

    $table = Engine_Api::_()->getItemTable('poke_setting');
    $select = $table->select()->where("user_id = $subject->user_id");
    $fetch_record = $table->fetchAll($select)->toArray();
    if (!empty($fetch_record)) {
      return;
    }

    //Getting the user level.
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    //Getting the user level permission.
    $send = Engine_Api::_()->authorization()->getPermission($user_level, 'poke', 'send');
    $pokeoption = Engine_Api::_()->authorization()->getPermission($user_level, 'poke', 'auth_view');
    //Getting the logged in user id.
    $userid = Engine_Api::_()->user()->getViewer()->getIdentity();
    //Getting tne displayname of the user.
    $displayname = Engine_Api::_()->poke()->turncation($subject->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation);
    $label = Zend_Registry::get('Zend_Translate')->_("Poke %s");
    $label = sprintf($label, $displayname);
    $flag = 0;
    //Checking which user can see those link.
    if ($send) {
      if ($pokeoption == 'owner_member') {
        $table = Engine_Api::_()->getDbtable('membership', 'user');
        $select = $table->select()
                ->where('resource_id = ?', $userid)
                ->where('user_id = ?', $subject->user_id)
                ->where('active = ?', 1)
                ->group('resource_id');
        $row = $table->fetchRow($select);
        if ($row !== null) {
          $flag = 1;
        }
      } elseif ($pokeoption == 'mutual_friends') {
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $sugg_friend_id = $subject->user_id;
        $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
        $friendsName = $friendsTable->info('name');

        $select = $friendsTable->select()
                ->setIntegrityCheck(false)
                ->from($friendsName, array('user_id'))
                ->join($friendsName, "`{$friendsName}`.`user_id`=`{$friendsName}_2`.user_id", null)
                ->where("`{$friendsName}`.resource_id = ?", $userid)
                ->where("`{$friendsName}`.user_id = ?", $subject->user_id)
                ->where("`{$friendsName}`.active = ?", 1)
                ->orwhere("`{$friendsName}`.resource_id = ?", $sugg_friend_id) // Id of Loggedin user friend.
                ->where("`{$friendsName}_2`.resource_id = ?", $user_id) // Loggedin user Id.
                ->where("`{$friendsName}`.active = ?", 1)
                ->where("`{$friendsName}_2`.active = ?", 1);
        $row = $select->query()->fetchAll();
        if (!empty($row)) {
          $flag = 1;
        }
      } elseif ($pokeoption == 'friend_networks') {
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $networkMembershipName = $networkMembershipTable->info('name');
        $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
        $friendsName = $friendsTable->info('name');
        $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
        $select
                ->from($networkMembershipName, 'user_id')
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
                ->where("`{$networkMembershipName}_2`.user_id = ?", $subject->getIdentity())
        ;
        $data = $select->query()->fetch();
        if (!empty($data)) {
          $flag = 1;
        }
      } else {
        $flag = 1;
      }
    }

    if ($flag == 1) {
      return true;
    } else {
      return false;   
    }
  }

}

?>