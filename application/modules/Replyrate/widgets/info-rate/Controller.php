<?php

class Replyrate_Widget_InfoRateController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$pageSubject = Engine_Api::_()->core()->hasSubject() ? Engine_Api::_()->core()->getSubject() : null;
    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    /*if( !$viewer->getIdentity()) {
      return $this->setNoRender();
    }*/
    if( !Engine_Api::_()->authorization()->isAllowed('replyrate', null, 'view') ) return $this->setNoRender();	 

	$user = $viewer->getIdentity();
	if ($pageSubject) $user = $pageSubject->getIdentity();
	  	
	$table = Engine_Api::_()->getDbtable('recipients', 'messages');
	$convers = Engine_Api::_()->getDbtable('conversations', 'messages');
    $rName = $table->info('name');
    $cName = $convers->info('name');
    $select = $convers->select()
      ->from($cName, new Zend_Db_Expr('COUNT(1) AS row_count'))
      ->join($rName, "`{$cName}`.`conversation_id` = `{$rName}`.`conversation_id`", null)
      ->where("`{$cName}`.`user_id` != ?", $user)
	  ->where("`{$rName}`.`user_id` = ?", $user)
	  ->where("`{$rName}`.`inbox_deleted` = ?", 0)
      ;
	$inbox_conversation = $table->fetchRow($select);

	$table = Engine_Api::_()->getDbtable('recipients', 'messages');
	$convers = Engine_Api::_()->getDbtable('conversations', 'messages');
	$mess = Engine_Api::_()->getDbtable('messages', 'messages');
    $rName = $table->info('name');
    $cName = $convers->info('name');
	$mName = $mess->info('name');
    $select = $convers->select()
      ->from($cName, new Zend_Db_Expr('COUNT(1) AS row_count'))
      ->join($rName, "`{$cName}`.`conversation_id` = `{$rName}`.`conversation_id`", null)
	  ->join($mName, "`{$rName}`.`outbox_message_id` = `{$mName}`.`message_id`", null)
      ->where("`{$cName}`.`user_id` != ?", $user)
	  ->where("`{$rName}`.`user_id` = ?", $user)
	  ->where("`{$rName}`.`inbox_deleted` = ?", 0)
      ;
	$inbox_reply = $table->fetchRow($select);

	$settings = Engine_Api::_()->getApi('settings', 'core');
	if ($inbox_conversation->row_count > 0)	
	  $this->view->percent = $percent = round(100*$inbox_reply->row_count/$inbox_conversation->row_count) <= 100 ? round(100*$inbox_reply->row_count/$inbox_conversation->row_count) : 100;
	else $this->view->percent = $percent =0;
	    

	$this->view->bordercolor = isset($settings->reply_bgcolor) ? $settings->reply_bgcolor : '#5f93b4';
	$this->view->backgroundcolor = isset($settings->reply_tcolor) ? $settings->reply_tcolor : '#d0e2ec';
  }
}  