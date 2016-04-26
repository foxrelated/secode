<?php

class Ynidea_Widget_ProfileIdeasController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Process form
        $values = $request->getParams();
        $values['user_id'] = $subject->getIdentity();
        
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($values);
        
                             
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
        $this->view->paginator->setItemCountPerPage($items_count);
        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));

        // maximum allowed idea
        //$this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'idea', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->params = $values;
        
        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
          $this->_childCount = $paginator->getTotalItemCount();
        }
		if($paginator->getTotalItemCount() <= 0 )
			$this->setNoRender();
    }
    public function getChildCount()
      {
        return $this->_childCount;
      }
}
