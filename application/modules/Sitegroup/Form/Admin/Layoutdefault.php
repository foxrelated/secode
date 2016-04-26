<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutdefault.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_Layoutdefault extends Engine_Form {

  public function init() {

    $this->setAttrib('id', 'form-upload');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {

			$group_profile_layout = "Group Timeline Cover Photo Layout - 1) " . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/admin/tabbedlayout.gif\');">Tabbed Layout</a>'. '     '. '2) <a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/admin/withouttabbedlayout.gif\');">Without Tabbed Layout</a>';
			$group_member_layout = "Group Cover Photo Layout - 1) " . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/admin/tabbedlayout.gif\');">Tabbed Layout</a>'. '     '. '2) <a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/admin/withouttabbedlayout.gif\');">Without Tabbed Layout</a>';

			$this->addElement( 'Radio' , 'sitegroup_layout_cover_photo' , array (
				'label' => 'Cover Photo Layout',
				'description' => "Select a layout for the cover photo to be placed on the profile of groups on your site.",
				'multiOptions' => array (
					1 => $group_profile_layout,
					0 => $group_member_layout
				) ,
				//'value' => $coreSettings->getSetting('sitegroup.layout.cover.photo', 1),
        'escape' => false
			));
    }

    $this->addElement('Radio', 'sitegroup_layout_setting', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formRadioButtonStructure.tpl',
                    'class' => 'form element'
            )))));

    $this->addElement('Button', 'submit1', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

  }

}

?>