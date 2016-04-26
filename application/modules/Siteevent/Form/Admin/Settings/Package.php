<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Settings_Package extends Engine_Form {

    public function init() {

        $this->setTitle('Packages Settings')
                ->setName('siteevent_package_settings');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $packageInfoArray = array('price' => 'Price','billing_cycle'=> 'Billing Cycle','duration'=>'Duration','featured'=>'Featured','sponsored'=>'Sponsored','rich_overview'=>'Rich Overview','videos'=>'Videos','photos'=>'Photos','description'=>'Description');
        
      if(Engine_Api::_()->siteevent()->hasTicketEnable()){
        $packageInfoArray = array_merge($packageInfoArray,array('ticket_type' => 'Ticket Types', 'commission' => 'Commission'));
      }    
              //VALUE FOR ENABLE/DISABLE PACKAGE
      $this->addElement('Radio', 'siteevent_package_setting', array(
          'label' => 'Packages',
          'description' => 'Do you want Packages to be activated? Packages can vary, based upon their features available to the events created under them. If enabled, users will have to select a package in the first step of event creation, which can be changed again later. Event owners can manage their package from ‘Packages’ section available on the \'Event Dashboard\'. [Note: If you have enabled packages on your site, then feature settings for events will depend on packages and member levels based feature settings will be off. If packages are disabled, then feature settings for events could be configured from member levels.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'javascript:showUiOption(this.value)',
          'value' => $settings->getSetting('siteevent.package.setting',1),
      ));

      $this->addElement('Radio', 'siteevent_package_view', array(
          'label' => 'Package View',
          'description' => 'Select the view type of packages that will be shown in the first step of event creation.',
          'multiOptions' => array(
              1 => 'Vertical',
              0 => 'Horizontal'
          ),
          'value' => $settings->getSetting('siteevent.package.view',1),
      ));

      $this->addElement('MultiCheckbox', 'siteevent_package_information', array(
          'label' => 'Package Information',
          'description' => 'Select the information options that you want to be available in package details.',
          'multiOptions' =>  $packageInfoArray,       
          'value' => $settings->getSetting('siteevent.package.information', array_keys($packageInfoArray)),
      ));

        $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}