<?php
class Yncontest_Widget_ProfileDescriptionController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }	
    // Get subject and check auth  
  	$this->view->contest =Engine_Api::_()->core()->getSubject();    	
  }
}