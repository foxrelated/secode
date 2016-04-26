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
class Siteevent_Widget_AddToMyCalendarSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->calendarOptions = $calendarOptions = $this->_getParam('calendarOptions');
        if (in_array('google', $calendarOptions)) {
            $this->view->googlelink = Engine_Api::_()->siteevent()->getGoogleCalenderLink($siteevent);
        }

        if (in_array('yahoo', $calendarOptions)) {
            $this->view->yahoolink = Engine_Api::_()->siteevent()->getYahooCalenderLink($siteevent);
        }

        $this->view->contentFullWidth = $this->_getParam('contentFullWidth');
        
        if (empty($calendarOptions))
            return $this->setNoRender();
    }

}
