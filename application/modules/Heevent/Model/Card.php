<?php
/**
 * Created by PhpStorm.
 * User: bolot
 * Date: 18.02.14
 * Time: 14:17
 */
class Heevent_Model_Card extends Core_Model_Item_Abstract
{
  public  function getEvent(){
    return Engine_Api::_()->getItem('event', $this->event_id); //TODO Check Availability
  }
}