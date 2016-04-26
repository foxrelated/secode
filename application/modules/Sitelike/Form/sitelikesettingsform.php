<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitelikesettingsform.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Form_sitelikesettingsform extends Engine_Form {

  public function init() {
    $this
        ->setTitle( 'Like Settings' )
        ->setDescription( 'Do you want your profile to be liked by site members ?' ) ;
    // Get current user id.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    //Check id in "userconnection table"
    $connection_value = Engine_Api::_()->getItem( 'sitelike_mysettings' , $user_id ) ;
    if ( empty( $connection_value ) ) {
      $connection_value = 0 ;
    }
    else {
      $connection_value = 1 ;
    }
    $this->addElement( 'Radio' , 'like' , array (
      'multiOptions' => array (
        0 => 'Yes' ,
        1 => 'No'
      ) ,
      'value' => $connection_value ,
    ) ) ;
    $this->addElement( 'Button' , 'submit' , array (
      'label' => 'Save' ,
      'type' => 'submit' ,
    ) ) ;
  }

}