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
class Siteevent_Form_Priceinfo_Edit extends Siteevent_Form_Priceinfo_Add {

    public $_error = array();

    public function init() {

        parent::init();

        $this->setTitle('Edit Where to Buy')
                ->setDescription("Edit Where to Buy option for this event using the form below.");

        $this->execute->setLabel('Save Changes');
    }

}