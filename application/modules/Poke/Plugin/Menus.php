<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Menus.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Plugin_Menus {

  // user_profile
  public function onMenuInitialize_UserProfilePoke($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    //GET THE SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
      return false;
    }
    $result = Engine_Api::_()->poke()->levelSettings($subject);
    $displayname = Engine_Api::_()->poke()->turncation($subject->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation);
    $label = sprintf(Zend_Registry::get('Zend_Translate')->_("Poke %s"), $displayname);
    if ($result) {
      return array(
          'label' => $label,
          'icon' => 'application/modules/Poke/externals/images/poke_icon.png',
          'class' => 'smoothbox',
          'route' => 'poke_general',
          'params' => array(
              'controller' => 'pokeusers',
              'action' => 'pokeuser',
              'pokeuser_id' => $subject->getIdentity(),
              ));
    }
  }

  //user_home
  public function onMenuInitialize_PokeHomeConnection($row) {
    //Getting the user level.
    $pokeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke');
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $send = Engine_Api::_()->authorization()->getPermission($user_level, 'poke', 'send');
    if ($pokeEnabled)
      if ($send) {
        $poke_field_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('poke.conn.setting');

        if (!empty($poke_field_check)) {
          $viewer = Engine_Api::_()->user()->getViewer();
          if ($viewer->getIdentity()) {
            return array(
                'label' => $row->label,
                'icon' => $row->params['icon'],
                'class' => 'smoothbox',
                'route' => 'default',
                'module' => 'poke',
                'controller' => 'index',
                'action' => 'pokesettings'
            );
          }
          return false;
        }
      }
  }
  
  //Conditions for showing Poke link in Sitemobile.
  public function  canCreatePoke(){
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!empty($viewer_id)){
       return true;
    }  
  }

}

?>