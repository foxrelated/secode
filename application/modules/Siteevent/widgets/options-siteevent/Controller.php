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
class Siteevent_Widget_OptionsSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }



        //GET NAVIGATION
        $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_gutter");

        if (Count($this->view->gutterNavigation) <= 0) {
            return $this->setNoRender();
        }
    }

}
