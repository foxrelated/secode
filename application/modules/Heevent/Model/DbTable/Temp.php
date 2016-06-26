<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Params.php 30.01.14
 * @author     Bolot
 */


class Heevent_Model_DbTable_Temp extends Engine_Db_Table
{
    public function getEventTemp($event_id,$user_id){
        if($event_id instanceof Event_Model_Event){
            $event_id = $event_id->getIdentity();
        }if($user_id instanceof User_Model_User){
            $user_id = $user_id->getIdentity();
        }
      $event_id = (int)$event_id;
        if(is_integer($event_id) && $event_id > 0 && is_integer($user_id) && $user_id > 0){
            return $this->fetchRow($this->select()->where('event_id = ?', $event_id)->where('user_id = ?', $user_id));
        } else{
            throw new Exception('Wrong parameter');
        }
    }
    public function setEventTemp(array $params){
        /*$db = $this->getAdapter();
        $db->beginTransaction();
        try{
            $row = $this->createRow();
            $row->setFromArray($params);
            $row->save();
            $db->commit();
        } catch(Exception $e){
            $db->rollBack();
            throw $e;
        }*/
    }
}