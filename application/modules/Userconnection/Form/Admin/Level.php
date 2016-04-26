<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Form_Admin_Level extends Engine_Form
{
  public function init()
  { 
		$session = new Zend_Session_Namespace();
    $this
    ->setTitle('Member Level Settings')
    ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level){
    $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    // category field
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level){
    $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    // category field
    $this->addElement('Select', 'level_id', array(
    'label' => 'Member Level',
    'multiOptions' => $levels_prepared,
    'onchange' => 'javascript:fetchLevelSettings(this.value);'
    ));

    $this->addElement('Text', 'level', array(
    'label' => 'Connection Level Setting',
    'description' => 'Enter the maximum level depth till which User Connections are to be shown to users of this Member Level Note: Connections of levels larger than this will not be shown. If the message setting below is chosen to "Yes", then that message will show in the block, otherwise the complete block will not be shown for connection levels larger than the maximum limit.
    For optimal page performance, we recommend that a value between 2 to 5 is chosen (Connection level 1 => Friend, Connection level 2 => Friend of friend).',
    'value' => $session->level,
    ));

    $this->addElement('Button', 'submit', array(
    'label' => 'Save Settings',
    'type' => 'submit',
    'ignore' => true
    ));
  }
}
?>