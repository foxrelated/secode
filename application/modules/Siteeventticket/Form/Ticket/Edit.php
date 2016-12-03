<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Ticket_Edit extends Siteeventticket_Form_Ticket_Add {

    public function init() {
        parent::init();
        $this->setTitle('Edit Ticket Details')
                ->setDescription("Edit your ticket below and click on 'Save Changes' button to make the updated ticket available to the buyers (users).");
        $this->submit->setLabel('Save Changes');
    }

}
