<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Params.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Model_DbTable_Params extends Engine_Db_Table
{
  public function getEventParams($event_id){
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
  public function setEventParams($event_id, array $params){
    $params['event_id'] = $event_id;
    $db = $this->getAdapter();
    $db->beginTransaction();

    try{
      $row = $this->getEventParams($event_id);
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
}
