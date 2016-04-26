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
class Siteevent_Form_Admin_Badge_Edit extends Siteevent_Form_Admin_Badge_Create {

    public function init() {
        parent::init();
        $this->setTitle('Edit Badge Entry')
                ->setDescription('Edit your Badge here and click on "Save Changes" to save it.');
        $this->submit->setLabel('Save Changes');
    }

}