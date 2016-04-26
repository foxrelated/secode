<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mix.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Form_Admin_Mix extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Mixed Suggestions')
            ->setDescription('Here, you can select the suggestions that you want to be shown amongst mixed suggestions(shown in the Recommendations widget and on the explore suggestion page), and also configure the number of entries. [Note : Please enable the Mixed Suggestions widget from the Layout Editor for this.]');


    $this->addElement('Text', 'sugg_mix_wid', array(
        'label' => 'Recommendations Widget',
        'description' => "How many suggestions do you want to display in the Recommendations widget ?",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.mix.wid')
    ));

    $this->addElement('Radio', 'recomended_ajax_enabled', array(
        'label' => 'Default Ajax Based Display for Recommendations Widget',
        'description' => 'Do you want to enable ajax based display of recommendation widget as default?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('recomended.ajax.enabled', 1),
    ));

    $this->addElement('Radio', 'messagefriend', array(
        'label' => 'Message-a-Friend Suggestions',
        'description' => "Do you want Message-a-Friend suggestions to be part of Mixed Suggestions ? [This suggestion shows to the user a friend that he/she has not contacted/messaged since a long time, and provides the user a quick link to message that friend.]",
        'multiOptions' => array(
            1 => 'Yes, make this suggestion a part of Mixed Suggestions.',
            0 => 'No, do not make this suggestion a part of Mixed Suggestions.'
        ),
        'value' => $this->getStatus('messagefriend'),
    ));


    $this->addElement('Radio', 'friendfewfriend', array(
        'label' => 'Suggest-Friends-to-Friend Suggestions',
        'description' => "Do you want Suggest-Friends-to-Friend suggestions to be part of Mixed Suggestions ? [This suggestion shows to the user a friend of his/her who has few friends on the site, and enables the user to suggest friends to him/her.]",
        'multiOptions' => array(
            1 => 'Yes, make this suggestion a part of Mixed Suggestions.',
            0 => 'No, do not make this suggestion a part of Mixed Suggestions.'
        ),
        'value' => $this->getStatus('friendfewfriend'),
    ));

    $this->addElement('Radio', 'friendphoto', array(
        'label' => 'Profile Picture Suggestions',
        'description' => "Do you want Profile Picture suggestions to be part of Mixed Suggestions ? [This suggestion shows to the user a friend of his/her who does not have a profile picture, and enables the user to suggest a profile picture to this friend.]",
        'multiOptions' => array(
            1 => 'Yes, make this suggestion a part of Mixed Suggestions.',
            0 => 'No, do not make this suggestion a part of Mixed Suggestions.'
        ),
        'value' => $this->getStatus('friendphoto'),
    ));



    $this->addElement('Radio', 'friend', array(
        'label' => 'Friend Suggestions',
        'description' => "Do you want Friend suggestions to be part of Mixed Suggestions ?",
        'multiOptions' => array(
            1 => 'Yes, make this suggestion a part of Mixed Suggestions.',
            0 => 'No, do not make this suggestion a part of Mixed Suggestions.'
        ),
        'value' => $this->getStatus('friend'),
    ));



   $modTable = Engine_Api::_()->getItemTable('suggestion_modinfo', 'suggestion');
   $modTableName = $modTable->info('name');

   $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
   $coreTableName = $coreTable->info('name');

   $select = $coreTable->select()
                    ->setIntegrityCheck(false)
                    ->from($coreTable, array())
                    ->joinInner($modTableName, '' . $modTableName . '.module = ' . $coreTableName . '.name')
		    ->where($modTableName . '.module != ?', 'user')
		    ->where($modTableName . '.enabled = ?', 1)
		    ->where($coreTableName . '.enabled =?', 1);
    $fetch = $select->query()->fetchAll();
    foreach( $fetch as $modInfo ) {

    if( !empty($modInfo['module']) && !empty($modInfo['item_type']) ) {
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modInfo['module'])) {
	$this->addElement('Radio', $modInfo['module'], array(
	    'label' => $modInfo['item_title'] . ' Suggestions',
	    'description' => "Do you want " . $modInfo['item_title'] . " suggestions to be part of Mixed Suggestions ?",
	    'multiOptions' => array(
		1 => 'Yes, make this suggestion a part of Mixed Suggestions.',
		0 => 'No, do not make this suggestion a part of Mixed Suggestions.'
	    ),
	    'value' => $this->getStatus($modInfo['module']),
	));
      }
     }

    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true
    ));
  }

  public function getStatus($pluginName) {
    $getInfo = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getValue($pluginName);
    if (!empty($getInfo)) {
      return $getInfo[$pluginName]['status'];
    }
    return 0;
  }

}
?>