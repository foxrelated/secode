<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Model_DbTable_Templates extends Engine_Db_Table
{
  protected $_rowClass = 'Goal_Model_Template';
  
  public function getTemplatesAssoc()
  {
    $stmt = $this->select()
        ->from($this, array('template_id', 'title'))
        ->order('title ASC')
        ->query();
    
    $data = array();
    foreach( $stmt->fetchAll() as $template ) {
      $data[$template['template_id']] = $template['title'];
    }
    return $data;
  }
}