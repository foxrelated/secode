<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Widget_TermsOfUseController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->terms_of_use = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($siteevent->event_id, 'terms_of_use');

        if (empty($this->view->terms_of_use)) {
            return $this->setNoRender();
        }
    }

}
