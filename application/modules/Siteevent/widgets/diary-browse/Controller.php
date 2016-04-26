<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_DiaryBrowseController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //GET ZEND REQUEST OBJECT
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $requestParams = $request->getParams();
        $this->view->viewTypes = $viewTypes = $this->_getParam('viewTypes', array("list", "grid"));
        $siteeventDiaryBrowse = Zend_Registry::isRegistered('siteeventDiaryBrowse') ? Zend_Registry::get('siteeventDiaryBrowse') : null;
        $this->view->statisticsDiary = $this->_getParam('statisticsDiary', array("entryCount", "viewCount"));
        $this->_mobileAppFile = true;
      if ($this->_getParam('ajax', false)) {
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
        $viewTypeDefault = $this->_getParam('viewTypeDefault', 'grid');
        if (!in_array($viewTypeDefault, $viewTypes)) {
            $viewTypeDefault = $viewTypes[0];
        }
        if (!isset($requestParams['viewType'])) {
            $this->view->setAlsoInForm = true;
            $requestParams['viewType'] = $viewTypeDefault;
        }
        //GENERATE SEARCH FORM
        $this->view->form = $form = new Siteevent_Form_Diary_Search();
        $form->populate($requestParams);
        $this->view->formValues = $form->getValues();
        $page = $request->getParam('page', 1);

        //GET PAGINATOR
        $params = array();
        $params['pagination'] = 1;
        $params = array_merge($requestParams, $params);
        $itemCount = $this->_getParam('itemCount', 20);
        $this->view->isSearched = Count($params);
        $this->view->paginator = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getBrowseDiaries($params);

        $this->view->paginator->setItemCountPerPage(12);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->listThumbsValue = $this->_getParam('listThumbsValue', 2);
        $this->view->isAjax = $this->_getParam('isAjax', false);
         $this->view->page = $this->_getParam('page', 1);;
        $this->view->totalCount = $this->view->paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalCount) /12);
        
//        if(!$isappajax){
//        $this->view->page = $this->view->paginator->setCurrentPageNumber($page);
//        $this->view->totalPages = $this->view->paginator->count();
//        }
//        if ($this->view->isAjax) {
//            $this->getElement()->removeDecorator('Title');
//            $this->getElement()->removeDecorator('Container');
//        }
        
        $this->view->params = $params = $this->_getAllParams();

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        if (empty($siteeventDiaryBrowse))
            return $this->setNoRender();

        //GET LEVEL SETTING
        $this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");
    }

}
