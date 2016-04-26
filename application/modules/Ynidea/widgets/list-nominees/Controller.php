<?php

class Ynidea_Widget_ListNomineesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
       $viewer = Engine_Api::_()->user()->getViewer();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Process form
        $values = $request->getParams();
        $values['user_id'] = $viewer->getIdentity();
        
       /*

        if (!empty($values['parent_type']) && !empty($values['subject_id'])) {
            $item = Engine_Api::_()->getItem($values['parent_type'], $values['subject_id']);
            if ($item && $item instanceof Core_Model_Item_Abstract) {
                $this->view->item = $item;
            }
        }    
           */           
        
        $this->view->paginator = $paginator =
            Engine_Api::_()->getApi('core', 'ynidea')->getNomineesPaginator($values);
                        
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
        $this->view->paginator->setItemCountPerPage($items_count);

        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));

        // maximum allowed videos
        //$this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->params = $values;
    }

}