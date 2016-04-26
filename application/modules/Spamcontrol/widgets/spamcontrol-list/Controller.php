<?php

class Spamcontrol_Widget_SpamcontrolListController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
   
    $subject_id = Engine_Api::_()->core()->getSubject()->getIdentity();
    
    $this->view->warn = $warn = Engine_Api::_()->getDbtable('warn','spamcontrol')->getUserWarn($subject);
    
    if(sizeof($warn)< 1){
      $this->setNoRender();
      return;
    }
  }
}