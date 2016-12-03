<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Format.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Migrate_Migrate extends Engine_Form {

    public function init() {

        $this->loadDefaultDecorators();
        $this->setTitle('Migrate RSVP Events')
            ->setDescription('Here, you can migrate RSVP events into Ticket based events. Once migration script will complete successfully, one free ticket title ‘Entry Ticket’ will be created for all the ongoing and upcoming events and also a free order will be created for every Attending members (Excluded ‘Not Attending’ and ‘Maybe Attending’ members) of that occurrence. This migration script has been created for one time migration use only and at a time only one migration instance could be run.')
            ->setName('siteeventticket_migrate');
            $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Button', 'migrate', array(
            'label' => 'Start Migration',
            'onclick' => 'startMigration()',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));
    }

}
