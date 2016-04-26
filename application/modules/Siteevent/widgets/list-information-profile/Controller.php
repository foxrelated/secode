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
class Siteevent_Widget_ListInformationProfileController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //DONT RENDER IF NOT AUTHORIZED
        $this->view->siteevent_like = true;

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->resource_id = $resource_id = $siteevent->getIdentity();
        $this->view->resource_type = $resource_type = $siteevent->getType();

        $this->view->showContent = $this->_getParam('showContent', array("memberCount", "photo", "title", "postedBy", "postedDate", "viewCount", "likeCount", "commentCount", "tags", "location", "phone", "email", "website", "price", "description", "newlabel", "sponsored", "featured", "photosCarousel", "diary", "reviewCreate", "startDate", "endDate", "showeventtype", "showeventtime"));
        $this->view->actionLinks = $this->_getParam('actionLinks', 1);
        $this->view->truncationDescription = $this->_getParam('truncationDescription', 300);
        //IF FACEBOOK PLUGIN IS THERE THEN WE WILL SHOW DEFAULT FACEBOOK LIKE BUTTON.
        $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        $default_like = 1;
        $this->view->success_showFBLikeButton = 0;
        if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') { 
            $this->view->success_showFBLikeButton = Engine_Api::_()->facebookse()->showFBLikeButton('siteevent');
            $default_like = 2;
        }
        $this->view->like_button = $this->_getParam('like_button', $default_like);

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

        //GET EVENT TAGS
        $this->view->siteeventTags = $siteevent->tags()->getTagMaps();
        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, 'edit');
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        //POPULATE FORM
        $row = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);

        //POPULATE FORM
        $this->view->email = $row->email;
        $this->view->phone = $row->phone;
        $this->view->website = $row->website;

        $this->view->create_review = ($siteevent->owner_id == $viewer->getIdentity()) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) || empty($this->view->showContent) || !in_array('reviewCreate', $this->view->showContent)) {
            $this->view->create_review = 0;
        }
        //GET NAVIGATION
        $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_gutter");
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $this->view->price = $siteevent->price;
        }
        $this->view->owner = Engine_Api::_()->core()->getSubject()->getOwner();
    }

}