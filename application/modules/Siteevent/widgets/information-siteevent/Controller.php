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
class Siteevent_Widget_InformationSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SETTING
        $this->view->showContent = $this->_getParam('showContent', array("memberCount", "viewCount", "likeCount", "commentCount", "tags", "category", "ownerName", "rsvp", "price", "startDate", "endDate", "location"));
        
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('rsvp', $this->view->showContent)) {
            unset($this->view->showContent['rsvp']);
        }          

        if (Count($this->view->showContent) <= 0) {
            $this->view->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');        
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $this->view->category_name = '';
        if (!empty($siteevent->category_id)) {
            $this->view->category_name = $tableCategory->getCategory($siteevent->category_id)->category_name;
        }

        $this->view->occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        
        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $this->view->canCreate = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");        

        //GET EVENT TAGS
        $this->view->siteeventTags = $siteevent->tags()->getTagMaps();

        if (!empty($this->view->showContent) && in_array('socialShare', $this->view->showContent)) {

            $social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_preferred_5"></a>
                <a class="addthis_button_compact"></a>
                <a class="addthis_counter addthis_bubble_style"></a>
                </div>
                <script type="text/javascript">
                var addthis_config = {
                                    services_compact: "facebook, twitter, linkedin, google, digg, more",
                                    services_exclude: "print, email"
                }
                </script>
                <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

            $settings = Engine_Api::_()->getApi('settings', 'core');

            //GET CODE FROM LAYOUT SETTING
            $this->view->code = $settings->getSetting('siteevent.code.share', $social_share_default_code);

            $this->view->occurrence_id = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
            $dates = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($siteevent->event_id, $occurrence_id);
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//            $endDate = strtotime($view->locale()->toEventDateTime($dates['endtime']));
//            $currentDate = strtotime($view->locale()->toEventDateTime(time()));

            $endDate = strtotime($dates['endtime']);
            $currentDate = time();

            if ($endDate > $currentDate) {
                $this->view->allowSocialSharing = 1;
            } else {
                if ($this->_getParam('allowSocialSharing', 0)) {
                    $this->view->allowSocialSharing = 1;
                } else {
                    $this->view->allowSocialSharing = 0;
                }
            }
        }
    }

}