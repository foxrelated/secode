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
class Siteevent_Widget_ArchivesSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject();
        $owner = $siteevent->getOwner();

        //SHOW ARCHIVES
        $this->view->archive_siteevent = Engine_Api::_()->getDbtable('events', 'siteevent')->getArchiveSiteevent($owner);

        if (Count($this->view->archive_siteevent) <= 0) {
            return $this->setNoRender();
        }
    }

}