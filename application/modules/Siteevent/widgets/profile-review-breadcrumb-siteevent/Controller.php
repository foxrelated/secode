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
class Siteevent_Widget_ProfileReviewBreadcrumbSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            return $this->setNoRender();
        }

        //GET REVIEWS
        $this->view->reviews = Engine_Api::_()->core()->getSubject();

        //GET EVENT 
        $this->view->siteevent = $siteevent = $this->view->reviews->getParent();


        //GET TAB ID
        $this->view->tab_id = Engine_Api::_()->siteevent()->existWidget('siteevent_reviews', 0);

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