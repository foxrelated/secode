<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_Api_Core extends Core_Api_Abstract {

    public function sendSuggestion($reciever_object, $sender_object, $event_id) {
        $suggTable = Engine_Api::_()->getItemTable('suggestion');
        $sugg = $suggTable->createRow();
        $sugg->owner_id = $reciever_object->getIdentity();
        $sugg->sender_id = $sender_object->getIdentity();
        $sugg->entity = 'siteevent';
        $sugg->entity_id = $event_id;
        $sugg->save();

        // Add in the notification table for show in the "update".
        // $reciever_object : Object which are geting suggestion.
        // $sender_obj : Object which are sending suggestion.
        // $sugg : Object from which table we'll link.
        // suggestion_siteevent :notification type.
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($reciever_object, $sender_object, $sugg, 'event_suggestion');
    }

    public function setInvitePackages() {
        $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.isvar');
        $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.basetime');
        $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.filepath');
        $currentbase_time = time();
        $word_name = strrev('lruc');
        $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;

        if (($currentbase_time - $base_result_time > 3715200) && empty($check_result_show)) {
            $is_file_exist = file_exists($file_path);
            if (!empty($is_file_exist)) {
                $fp = fopen($file_path, "r");
                while (!feof($fp)) {
                    $get_file_content .= fgetc($fp);
                }
                fclose($fp);
                $modGetType = strstr($get_file_content, $word_name);
            }
            if (empty($modGetType)) {
                Engine_Api::_()->siteevent()->setDisabledType();
                Engine_Api::_()->getItemtable('siteevent_package')->setEnabledPackages();
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventinvite.set.type', 1);
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventinvite.utility.type', 1);
            } else {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventinvite.isvar', 1);
            }
        }
    }

    public function getMailBody($subject, $inviter_name, $site_title_linked, $site_title) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $occurrence_id = $_POST['occurrence_id'];
        $translate = Zend_Registry::get('Zend_Translate');
        $viewer = Engine_Api::_()->user()->getViewer();
        $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
                . $_SERVER['HTTP_HOST']
                . $subject->getHref() . '/' . $occurrence_id;
        $event_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $subject->title . '</a>';
        $event_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
        $body = '';
        if (!$subject->is_online) {


            $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($subject->event_id, $occurrence_id);
            $startDateObject = new Zend_Date(strtotime($eventdateinfo['starttime']));
            $endDateObject = new Zend_Date(strtotime($eventdateinfo['endtime']));
            if ($view->viewer() && $view->viewer()->getIdentity()) {
                $tz = $view->viewer()->timezone;
                $startDateObject->setTimezone($tz);
                $endDateObject->setTimezone($tz);
            }
            if ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')) {

                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM'));


                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($startDateObject) . ' - ' . $view->locale()->toTime($endDateObject);
            } else {

                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($startDateObject) . ' - ' . $view->locale()->toDateTime($endDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($endDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($endDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($endDateObject);
            }
            $location = !empty($subject->location) ? $translate->_('Location:') . ' ' . $subject->location : '';
            $venue_name = !empty($subject->venue_name) ? $translate->_('Venue Name:') . ' ' . $subject->venue_name : '';
            $event_details = $translate->_('Event Name:') . ' ' . $event_title_linked . '
          
         ' . $venue_name . '
           
' . $location . '
  
' . $translate->_('Date & Time:') . ' ' . $optionText;

            $body = sprintf($translate->_('Your friend %1s has invited you for an event. Following are the details:'), $inviter_name) . '
           
' . $event_details;
        } else {
            $event_details = '';
            $body = sprintf($translate->_('Your friend %1s has invited you to join %2s.'), $inviter_name, $event_title_linked);
        }
        return $body;
    }

    public function getMailBodyOther($subject, $event_title_linked, $store_title = null, $viewer=null, $occurrence_id = null) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if(!$occurrence_id) {
            if (isset($_POST['occurrence_id']))
                $occurrence_id = $_POST['occurrence_id'];
            else
                $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        
        }  
        $translate = Zend_Registry::get('Zend_Translate');

        $body = '';
        if (!$subject->is_online) {


            $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($subject->event_id, $occurrence_id);
            $startDateObject = new Zend_Date(strtotime($eventdateinfo['starttime']));
            $endDateObject = new Zend_Date(strtotime($eventdateinfo['endtime']));
            if ($view->viewer() && $view->viewer()->getIdentity()) {
                $tz = $view->viewer()->timezone;
                $startDateObject->setTimezone($tz);
                $endDateObject->setTimezone($tz);
            }
            if ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')) {

                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM'));


                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($startDateObject) . ' - ' . $view->locale()->toTime($endDateObject);
            } else {

                $optionText = $view->locale()->toDateTime($startDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($startDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($startDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($startDateObject) . ' - ' . $view->locale()->toDateTime($endDateObject, array('format' => 'MMM')) . ' ' . $view->locale()->toDateTime($endDateObject, array('format' => 'd')) . ', ' . $view->locale()->toDateTime($endDateObject, array('format' => 'yyyy')) . ' ' . $view->locale()->toTime($endDateObject);
            }

            $location = !empty($subject->location) ? $translate->_('Location:<b>') . ' ' . $subject->location . '</b><br />' : '';
            $venue_name = !empty($subject->venue_name) ? $translate->_('Venue Name:<b>') . ' ' . $subject->venue_name . '</b><br />' : '';
            $event_details = $translate->_('Event Name:<b>') . ' ' . $event_title_linked . '</b><br >' . $venue_name . $location . $translate->_('Date & Time:<b>') . ' ' . $optionText . '</b>';

            $body = sprintf($translate->_('Your friend %1s has invited you for an event. Following are the details:'), $viewer->getTitle()) . '<br />' . $event_details;
        } else {
            $event_details = '';
            $body = sprintf($translate->_('Your friend %1s has invited you to join %2s.'), $viewer->getTitle(), $event_title_linked);
        }

        return $body;
    }

    /**
     *
     * @param $user_id: User id for this find out 'first level friend' of this ID.
     * @param $limit: Limit which set by the site admin.
     * @param $status: key of array.
     * @return Empty or Integer.
     */
    public function user_first_level_friend($event_id, $occurrence_id, $search) {

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        //FETCH USER MEMBER FROM MEMBERSHIP TABLE
        $membershipTable = $this->getMemberTableObj();
        $membershipTableName = $membershipTable->info('name');

        $selfTable = $this->getSelfTableObj('user');
        $selfTableName = $selfTable->info('name');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        //MEMBERSHIP TABLE OF SITEEVENT TO CHECK HOW MANY FRIENDS ALREADY HAVE BEEN JOIND THIS EVENT OCCURRENCE.
        $user_ids = $siteevent->membership()->getEventMembers($event_id, $occurrence_id);
        $user_ids[] = $user_id;
        if($siteevent->parent_type != 'user') { 
          $totalInvitableMember = Engine_Api::_()->siteevent()->getContentModuleMembers($event_id, $user_ids);
          $select = $selfTable->select()
                    ->from($selfTableName, array('user_id AS resource_id'));
          if(!empty($totalInvitableMember))
            $select->where($selfTableName . '.user_id IN (?)', (array) $totalInvitableMember);
          else
            $select->where($selfTableName . '.user_id =?', 0);
          $select->order('displayname ASC');          
        }
        else {
          $select = $membershipTable->select()
                  ->setIntegrityCheck(false)
                  ->from($membershipTableName, array('resource_id'))
                  ->joinLeft($selfTableName, "$selfTableName.user_id = $membershipTableName.resource_id", NULL)
                  ->where($membershipTableName . '.user_id = ?', $user_id)
                  ->where($selfTableName . '.verified = ?', 1)
                  ->where($membershipTableName . '.active = ?', 1)
                  ->where($selfTableName . '.enabled = ?', 1)
                  ->order($selfTableName . '.member_count DESC');
          if (!empty($user_ids))
              $select->where($membershipTableName . '.resource_id NOT IN (?)', (array) $user_ids);
         
        }
        if (($search != 'show_friend_suggestion') && !empty($search))
              $select->where("$selfTableName.displayname LIKE ?", '%' . $search . '%');
       
        $fetch_friend_table = Zend_Paginator::factory($select);
        return $fetch_friend_table;
    }

    /**
     * Returns the object of table.
     *
     * @param $userId: Optional( Row Id ), If required the only one row object.
     * @return Object
     */
    public function getUserTableObj($userId = null) {
        if (!empty($userId)) {
            return Engine_Api::_()->getItem('user', $userId);
        }
        return Engine_Api::_()->getItemTable('user');
    }

    /**
     * Returns the object of table.
     *
     * @param $modName: Optional( Module Name ), Which membership table object we required.
     * @return Object
     */
    public function getMemberTableObj($modName = 'user') {
        return Engine_Api::_()->getDbtable('membership', $modName);
    }

    /**
     * Returns the object of table.
     *
     * @param $modName: Table Itemtype, which defined in settings/manifest.php file.
     * @param $modId: Optional( Row Id ), If required the only one row object.
     * @return Object
     */
    public function getSelfTableObj($modName, $modId = null) {
        if (empty($modName)) {
            return;
        }
        if (!empty($modId)) {
            return Engine_Api::_()->getItem($modName, $modId);
        }
        return Engine_Api::_()->getItemTable($modName);
    }

    /**
     * Returns the members information being displayed in the widget : Friends suggestion/Ajax
     *
     * @param $path_array: Array of the suggested user/users.
     * @return Array
     */
    public function selected_users_information($path_array, $selected_friend_show) {
        $users_id = false;
        if (!empty($path_array) && is_array($path_array)) {
            $users_id = implode(",", $path_array);
        }
        $users_id = $this->getTrimStr($users_id);

        //FETCH RECORD FROM USER TABLE
        $user_table = Engine_Api::_()->getItemTable('user');
        $select_user_table = $user_table->select()->where("user_id IN ($users_id)");
        if ($selected_friend_show) {
            $user_info_array = Zend_Paginator::factory($select_user_table);
        } else {
            $user_info_array = $user_table->fetchAll($select_user_table);
        }
        return $user_info_array;
    }

    /**
     * Returns the trim string or 0.
     *
     * @param $str: String
     * @return String or 0
     */
    public function getTrimStr($str) {
        $str = trim($str, ',');
        if (empty($str)) {
            return 0;
        } else {
            $str = trim($str, ",");
            return $str;
        }
    }

}