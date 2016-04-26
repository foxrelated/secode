<?php

class Ynidea_Widget_ListTrophiesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
       $viewer = Engine_Api::_()->user()->getViewer();

        $request = Zend_Controller_Front::getInstance()->getRequest();     
		$orderby = $request->getParam('orderby',"");
		$values = $request->getParams();
		if($orderby)
		{
			$values['orderby'] = $orderby;
			$values['direction'] = 'DESC';
		}   
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'ynidea')->getTrophyPaginator($values);                        
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
        $this->view->paginator->setItemCountPerPage($items_count);

        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));
       
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->params = $values;
    }

}