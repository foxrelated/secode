<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Controller.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */

class Poke_Form_Sitemobile_Search extends Engine_Form
{

  public function init() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    //INIT TO
    $this->addElement('Text', 'search', array(
        'autocomplete' => 'off',
        'placeholder' => $view->translate("Enter friends' name to poke them"),
        //'description' => 'Separate tags with commas.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));


    //INIT TO
    $this->addElement('hidden', 'toValues', array(
      'value' => ""
    ));

  }
  
}

