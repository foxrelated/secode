<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Announcement_Edit extends Siteevent_Form_Announcement_Create {

    public function init() {

        parent::init();
        $this->setTitle('Edit Announcement')
                ->setDescription('Edit the announcement for your event below.');

        // Change the submit label
        $this->getElement('submit')->setLabel('Edit Announcement');
    }

}