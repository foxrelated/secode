<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Catmap.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Form_Admin_Settings_Catmap extends Engine_Form {

  public function init() {

    $this
            ->setTitle("Choose a Profile Type for Mapping")
            ->setDescription('Choose a Profile Type from the drop down below to be mapped with the Category and click on "Save" button.')
            ->setMethod('POST');

    //Prepare Profile Types
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('video');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
			
     /* if (count($options) == 1) {
        //Empty and Hidden Profile Types
        $this->addElement('Hidden', 'profile_types', array(
            'value' => $options[0]->option_id
        ));
      } elseif (count($options) > 0) {*/
        $options = $profileTypeField->getElementParams('video');
        unset($options['options']['order']);
        unset($options['options']['multiOptions']['0']);
        $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
            'label' => 'Choose Profile Type',
            'required' => true,
            'allowEmpty' => false,
        )));
      //}
    }

    $this->addElement('Button', 'execute', array(
        'label' => 'Save',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
        'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'label' => 'cancel',
        'link' => true,
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addDisplayGroup(array(
        'execute',
        'cancel'
            ), 'buttons');
  }

}
