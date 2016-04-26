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
class Siteevent_Widget_SocialshareSiteeventController extends Engine_Content_Widget_Abstract {

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
        $siteeventSocialshareEvents = Zend_Registry::isRegistered('siteeventSocialshareEvents') ? Zend_Registry::get('siteeventSocialshareEvents') : null;

        //DONT RENDER IF NOT AUTHORIZED
        if (empty($siteeventSocialshareEvents) || !Engine_Api::_()->core()->hasSubject() || empty($this->view->code)) {
            return $this->setNoRender();
        }
    }

}