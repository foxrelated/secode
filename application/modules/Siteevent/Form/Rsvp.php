<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rsvp.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Rsvp extends Engine_Form {

    public function init() {
        $this
                ->setMethod('POST')
                ->setAction($_SERVER['REQUEST_URI'])
        ;

        $this->addElement('Radio', 'rsvp', array(
            'multiOptions' => array(
                2 => 'Attending',
                1 => 'Maybe Attending',
                0 => 'Not Attending',
            //3 => 'Awaiting Approval',
            ),
        ));
    }

}
