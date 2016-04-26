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
class Siteevent_Widget_ListProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();        
        
        $this->view->render = $render = false;
        if($module == 'siteeventticket' && $controller == 'ticket' && $action == 'buy') {
            $this->view->render = $render = true;
        }
        
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') && !$render) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET CATEGORY TABLE
        $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        if (!empty($siteevent->category_id)) {
            $this->view->category_name = $this->view->tableCategory->getCategory($siteevent->category_id)->category_name;

            if (!empty($siteevent->subcategory_id)) {
                $this->view->subcategory_name = $this->view->tableCategory->getCategory($siteevent->subcategory_id)->category_name;

                if (!empty($siteevent->subsubcategory_id)) {
                    $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($siteevent->subsubcategory_id)->category_name;
                }
            }
        }
    }

}