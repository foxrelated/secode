<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Logsearch.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Logsearch extends Engine_Form {

  public function init() {


    $type_prepared = array('0' => '', '1' => 'Database', '2' => 'File');
    $method_prepared = array('0' => '', '1' => 'Manual', '2' => 'Automatic');

    $this->addElement('Select', 'type', array(
            'multiOptions' => $type_prepared,
            'decorators' => array(
                    'ViewHelper'
            )
    ));

    $this->addElement('Select', 'method', array(
            'multiOptions' => $method_prepared,
            'decorators' => array(
                    'ViewHelper'
            )
    ));

    $this->addElement('Button', 'submit', array(
            'label' => 'View Log',
            'type' => 'submit',
            'decorators' => array(
                    'ViewHelper'
            )
    ));

    $this->addElement('Button', 'clear', array(
            'label' => 'Clear Log',
            'type' => 'submit',
            'decorators' => array(
                    'ViewHelper'
            )
    ));



    $this->addDisplayGroup(array('type', 'method', 'submit', 'clear'), 'group');

    $button_group = $this->getDisplayGroup('group');
    $button_group->addDecorator('DivDivDivWrapper');
  }

}