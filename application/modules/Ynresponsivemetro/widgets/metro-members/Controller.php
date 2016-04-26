<?php
class Ynresponsivemetro_Widget_MetroMembersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
	{
		return $this -> setNoRender(true);
	}
	// Get members
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where('enabled = ?', 1)
      ->order('view_count DESC');

    $this->view->members = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('max', 6));
	$this -> view -> total_members = $paginator->getTotalItemCount();
    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
