<?php

class Ynidea_Widget_MyTrophiesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Process form
        $values = $request->getParams();
        $values['user_id'] = $viewer->getIdentity();
        
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'ynidea')->getTrophyPaginator($values);
                        
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
        $this->view->paginator->setItemCountPerPage($items_count);
        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));

        // maximum allowed idea
        //$this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'idea', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->params = $values;
		$this->view->canCreate = true;
		if (!Engine_Api::_() -> authorization() -> isAllowed('ynidea_trophy', $viewer, 'create'))
        {
            $this->view->canCreate = false;
        }
    }
}