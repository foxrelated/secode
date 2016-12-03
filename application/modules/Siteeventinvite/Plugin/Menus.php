<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_Plugin_Menus {

    public function canPromote($row) {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.badge', 1) || !Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!$siteevent->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
            return false;
        }
        return array(
            'class' => 'buttonlink siteevent_gutter_promote smoothbox',
            'route' => "siteevent_extended",
            'controller' => 'badge',
            'action' => 'create',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
                'format' => 'smoothbox',
            ),
        );
    }

    public function onMenuInitialize_SiteeventinviteGutterEventinvite($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1))
            return;

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return false;
        }

        $siteeventinviteGutterLink = Zend_Registry::isRegistered('siteeventinviteGutterLink') ? Zend_Registry::get('siteeventinviteGutterLink') : null;
        if (empty($siteeventinviteGutterLink))
            return false;

        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if ($siteevent->getType() !== 'siteevent_event') {
            throw new Event_Model_Exception('This event does not exist.');
        }
        if (!$siteevent->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'invite')) {
            return false;
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        //CHECK IF THE EVENT IS PAST EVENT THEN ALSO DO NOT SHOW THE INVITE AND PROMOTE LINK
        $endDate = $view->locale()->toDateTime(Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id, 'DESC', $occurrence_id));
        $currentDate = $view->locale()->toDateTime(time());
        if (strtotime($endDate) < strtotime($currentDate))
            return false;
        //if (!empty($siteevent) && !empty($viewer_id)) {
        //START MANAGE-ADMIN CHECK
//      $isManageAdmin = Engine_Api::_()->siteevent()->isManageAdmin($siteevent, 'invite');
//      if (empty($isManageAdmin)) {
//        $can_invite = 0;
//      } else {
//        $can_invite = 1;
//      }echo "sdfsdfd";die;
        //END MANAGE-ADMIN CHECK
//      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'siteevent');
//      $manageadminTableName = $manageadminTable->info('name');
//      $select = $manageadminTable->select()
//              ->from($manageadminTableName, 'manageadmin_id')
//              ->where('user_id = ?', $viewer_id)
//              ->where('event_id = ?', $siteevent->event_id)
//              ->limit(1);
//      $rowData = $manageadminTable->fetchAll($select)->toArray();
//      if (!empty($rowData[0]['manageadmin_id'])) {
//        $ismanageadmin = 1;
//      } else {
//        $ismanageadmin = 0;
//      }
//
//      if (($can_invite != 1) || ($viewer_id != $siteevent->owner_id && $ismanageadmin != 1)) {
//        //return false;
//      }
//    }
        // Modify params

        $params = $row->params;
        $params['params']['siteevent_id'] = $siteevent->getIdentity();
        $params['params']['class'] = 'buttonlink icon_siteevents_inviteguests';
        $params['params']['user_id'] = $siteevent->owner_id;
        $params['params']['occurrence_id'] = $occurrence_id;
        return $params;
    }

}

?>