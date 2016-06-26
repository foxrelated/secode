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


class Heevent_Model_DbTable_Tickets extends Engine_Db_Table
{
    public function getEventTickets($event_id){
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
    public function setEventTickets($event_id, array $params){
        $params['event_id'] = $event_id;
        $db = $this->getAdapter();
        $db->beginTransaction();
        if($params['ticket_price'] == -1 && $params['ticket_count'] == -1){
            return;
        }
        try{
            $row = $this->getEventTickets($event_id);
            if(!$row)
                $row = $this->createRow();
            $row->setFromArray($params);
            $row->save();
            $db->commit();
        } catch(Exception $e){
            $db->rollBack();
            throw $e;
        }
    }
    public function getEventTicketCount($event_id){
        if($event_id instanceof Event_Model_Event){
            $event_id = $event_id->getIdentity();
        }
        $event_id = (int)$event_id;
        if(is_integer($event_id) && $event_id > 0){
            return $this
                ->fetchRow($this->select()->from($this->info('name'),array('ticket_count'=> new Zend_Db_Expr('SUM(ticket_count)')))->where('event_id = ?', $event_id));
        } else{
            throw new Exception('Wrong parameter');
        }
    }
}
