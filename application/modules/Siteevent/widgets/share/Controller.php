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
class Siteevent_Widget_ShareController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

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

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') && !Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return $this->setNoRender();
        }

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //GET SUBJECT
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

        //GET SETTINGS
        $this->view->optionsArray = $optionsArray = $this->_getParam('options', array("siteShare", "friend", "report", "print", "socialShare"));

        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->allowSocialSharing = 1;
        if ($subject->getType() == 'siteevent_event') {
            $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
            $dates = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($subject->event_id, $occurrence_id);

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

        $this->view->withoutContainer = $this->_getParam('withoutContainer', false);
        if ($this->view->withoutContainer) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $this->view->content_id = $this->_getParam('content_id', 0);
    }

}
