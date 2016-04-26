<?php
class Ynresponsivemetro_Widget_MetroEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro' || !Engine_Api::_() -> hasItemType("event"))
	{
		return $this -> setNoRender(true);
	}
	 // Get paginator
    $table = Engine_Api::_()->getItemTable('event');
    $select = $table->select()
      ->where('search = ?', 1)
      ->order('rand()');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
	$this -> view -> title = $this -> _getParam('title', '');
    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
