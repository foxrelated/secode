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


class Heevent_Model_DbTable_Counts extends Engine_Db_Table
{
    public function getEventCounts($event_id){
        if($event_id instanceof Event_Model_Event){
            $event_id = $event_id->getIdentity();
        }
        $event_id = (int)$event_id;
        if(is_integer($event_id) && $event_id > 0){
            return $this->fetchRow($this->select()->where('event_id = ?', $event_id));
        } else{
            throw new Exception('Wrong parameter');
        }
    }
    public function getEventCountsCount($event_id){
        if($event_id instanceof Event_Model_Event){
            $event_id = $event_id->getIdentity();
        }
      $event_id = (int)$event_id;
        if(is_integer($event_id) && $event_id > 0){
            return $this
                ->fetchRow($this->select()->from($this->info('name'),array('count_ticket'=> new Zend_Db_Expr('SUM(count_ticket)')))->where('event_id = ?', $event_id));
        } else{
            throw new Exception('Wrong parameter');
        }
    }
    public function setEventCounts(array $params){
        $db = $this->getAdapter();
        $db->beginTransaction();
        try{
            $row = $this->createRow();
            $row->setFromArray($params);
            $row->save();
            $db->commit();
        } catch(Exception $e){
            $db->rollBack();
            throw $e;
        }
    }
}
