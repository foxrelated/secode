<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Core.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Plugin_Core
{
	public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete pokes
      $pokeTable = Engine_Api::_()->getDbtable('pokeusers', 'poke');
      $pokeSelect = $pokeTable->select()
      ->where('resourceid = ?', $payload->getIdentity())
      ->orWhere('userid = ?', $payload->getIdentity());
      foreach( $pokeTable->fetchAll($pokeSelect) as $poke ) {
        $poke->delete();
      }
    }
  }
}
?>