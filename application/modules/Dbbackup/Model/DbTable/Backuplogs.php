<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Backuplogs.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Model_DbTable_Backuplogs extends Engine_Db_Table {

  protected $_rowClass = "Dbbackup_Model_Backuplog";

  public function setLog($params = array()) {

    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      $row = $this->createRow();
      $row->setFromArray($params);
      $id = $row->save();
      $db->commit();

      return $id;
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function updateLog($params = array()) {

    if(isset($params['backuplog_id']) && !empty ($params['backuplog_id']))
    { $select = $this->select()
            ->where('backuplog_id = ?', $params['backuplog_id']);
    }else{
      $select = $this->select()
            ->where('filename = ?', $params['filename']);
    }

    $row = $this->fetchRow($select);

    if ($row !== null) {

      $row->setFromArray($params);
      $id = $row->save();
    }
  }

  public function getLog($params = array()) {

    $select = $this->select();


    if( !empty($params['file']) )
    {
      $select->where('type = ? ', 'File');
    }

    if( !empty($params['database']) )
    {
      $select->where('type = ? ', 'Database');
    }

    if( !empty($params['auto']) )
    {
     $select->where('method = ? ', 'Automatic');
    }

    if( !empty($params['manual']) )
    {
       $select->where('method = ? ', 'Manual');
    }
    if( !empty($params['Fail']) )
    {
     $select->where('status = ? ', 'Fail');
    }

    if( !empty($params['Success']) )
    {
       $select->where('status = ? ', 'Success');
    }
    if(!empty($params['order']) && !empty($params['order_title'])){
    	$select->order($params['order_title'].' '. $params['order']);
    } 
    else {
    	$select->order('backuplog_id DESC');
    }
    
    if(isset($params['getlogid']) && !empty($params['getlogid'])) {
      return $select->query()->fetchAll();
    }

   return    $paginator = Zend_Paginator::factory($select);
  }

}
?>
