<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Geotag.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Geotag extends Engine_Form {

  public function init() {

    //GET DECORATORS
    $this->loadDefaultDecorators();

   $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Below, you can select the location related sources that would be available to users while adding locations to their photo albums, photos and status updates (using '<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>Advanced Activity Feeds / Wall Plugin</a>'). You can choose amongst Google Places, Pages (from '<a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a>'), Businesses (from '<a href='http://www.socialengineaddons.com/socialengine-directory-businesses-plugin' target='_blank'>Directory / Businesses Plugin</a>'), Groups (from '<a href='http://www.socialengineaddons.com/socialengine-groups-communities-plugin' target='_blank'>Groups / Communities Plugin</a>') and stores (from '<a href='http://www.socialengineaddons.com/socialengine-stores-marketplace-plugin' target='_blank'>Stores / Marketplace Plugin</a>') 
having locations associated with them."));

    $this->getDecorator('Description')->setOption('escape', false); 

    //GEO TAG HEADING
    $this
            ->setTitle('Location Entities Settings')
            ->setDescription($description);

    //SELECTABLE CONTENT
    $selectableContent = array("googleplaces" => "Google Places");

    //CHECK SITEPAGE IS ENABLED OR NOT
    $sitepageEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

    $pageLink = sprintf(Zend_Registry::get('Zend_Translate')->_('%1sDirectory / Pages Plugin%2s'), "<a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>", "</a>");

    if ($sitepageEnabled) {
      $selectableContent = array_merge($selectableContent, array('pages' => "Directory Items / Pages (from '$pageLink')"));
    }

    $businessLink = sprintf(Zend_Registry::get('Zend_Translate')->_('<a href="http://www.socialengineaddons.com/socialengine-directory-businesses-plugin" target="_blank">Directory / Businesses Plugin</a>'));

    //CHECK SITEBUSINESS IS ENABLED OR NOT
    $sitebusinessEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
    if ($sitebusinessEnabled) {
      $selectableContent = array_merge($selectableContent, array('businesses' => "Directory Items / Businesses (from '$businessLink')"));
    }

    //CHECK SITEGROUP IS ENABLED OR NOT
    $sitegroupEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');

    $groupLink = sprintf(Zend_Registry::get('Zend_Translate')->_('%1sGroups / Communities Plugin%2s'), "<a href='http://www.socialengineaddons.com/socialengine-groups-communities-plugin' target='_blank'>", "</a>");

    if ($sitegroupEnabled) {
      $selectableContent = array_merge($selectableContent, array('groups' => "Groups / Communities (from '$groupLink')"));
    }

    //CHECK SITESTORE IS ENABLED OR NOT
    $sitestoreEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');

    $storeLink = sprintf(Zend_Registry::get('Zend_Translate')->_('%1sStores / Marketplace Plugin%2s'), "<a href='http://www.socialengineaddons.com/socialengine-stores-marketplace-plugin' target='_blank'>", "</a>");

    if ($sitestoreEnabled) {
      $selectableContent = array_merge($selectableContent, array('stores' => "Stores / Marketplace (from '$storeLink')"));
    }

    //CHECK SITEEVENT IS ENABLED OR NOT
    $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');

    $eventLink = sprintf(Zend_Registry::get('Zend_Translate')->_('%1sAdvanced Events Plugin%2s'), "<a href='http://www.socialengineaddons.com/socialengine-advanced-events-plugin' target='_blank'>", "</a>");

    if ($siteeventEnabled) {
      $selectableContent = array_merge($selectableContent, array('events' => "Advanced Events (from '$eventLink')"));
    }

    //SELECTABLE CHECKBOX
    $this->addElement('MultiCheckbox', 'sitetagcheckin_selectable', array(
        'label' => 'Selectable Location Entities',
        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Choose the location related sources that would be available to users while adding locations to their photo albums, photos and status updates. (Note: Users will be able to check-in from status update box (add location to their status updates) if the '%1sAdvanced Activity Feeds / Wall Plugin%2s' is installed on your site.)"), "<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>", '</a>'),
        'multiOptions' => $selectableContent,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.selectable', array_keys($selectableContent)),
        'escape' => false
    ));
    $this->sitetagcheckin_selectable->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    //PREVIOUS TAGGED LOCATION
    $this->addElement('Radio', 'sitetagcheckin_tagged_location', array(
        'label' => 'Previous Tagged Location Entities',
        'description' => "Do you want to show to users their previously tagged location entities in the location auto-suggest when they click on it to choose a location? (The auto-suggest for location selection suggests to users location entities based on their previous location tags, locations around their current location, and locations matching their typed text.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.tagged.location', 1),
    ));

    //EXECUTE
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));
  }

}