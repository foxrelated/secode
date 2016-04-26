<?php
class Ynresponsivemetro_Widget_MetroBlogsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro' || !Engine_Api::_() -> hasItemType("blog"))
	{
		return $this -> setNoRender(true);
	}
	// Get paginator
    $table = Engine_Api::_()->getItemTable('blog');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where('draft = ?', 0)
      ->order('rand()');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
	
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
