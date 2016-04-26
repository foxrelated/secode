<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Append.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Append extends Engine_Form {

  public function init() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $playlistCount = Engine_Api::_()->getDbtable('playlists', 'sesvideo')->getPlaylistsCount(array('viewer_id' => $viewer->getIdentity(), 'column_name' => array('playlist_id', 'title')));
    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'addplaylist_max');
    $this->setTitle('Add Video To Playlist')
            ->setAttrib('id', 'form-playlist-append')
            ->setAttrib('name', 'playlist_add')
            ->setAction($_SERVER['REQUEST_URI']);
    $playlists = array();
    if ($quota > count($playlistCount) || $quota == 0)
      $playlists[0] = Zend_Registry::get('Zend_Translate')->_('Create New Playlist');
    
    if ($quota > count($playlistCount) || $quota == 0) {
			$this->addElement('Select', 'playlist_id', array(
        'label' => 'Choose Playlist',
        'multiOptions' => $playlists,
        'onchange' => "updateTextFields()",
    ));
      $this->addElement('Text', 'title', array(
          'label' => 'Playlist Name',
          'placeholder' => 'Enter Playlist Name',
          'style' => '',
          'filters' => array(
              new Engine_Filter_Censor(),
          ),
      ));
      $this->addElement('Textarea', 'description', array(
          'label' => 'Playlist Description',
          'placeholder' => 'Enter Playlist Description',
          'maxlength' => '300',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '300')),
              new Engine_Filter_EnableLinks(),
          ),
      ));
      //Init album art
      $this->addElement('File', 'mainphoto', array(
          'label' => 'Playlist Photo',
      ));
      $this->mainphoto->addValidator('Extension', false, 'jpg,png,gif,jpeg');
			  //Privacy Playlist View
    $this->addElement('Checkbox', 'is_private', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Do you want to make this playlist private?"),
        'value' => 0,
        'disableTranslator' => true
    ));
    //Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Add Playlists',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
		$orCondition = ' or ';
    } else {
			$orCondition = '';
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have already created the maximum number of playlists allowed. If you would like to create a new playlist, please delete an old one first. Currently, you can only add songs in your existing playlists.") . "</span></div>";
      $this->addElement('Dummy', 'dummy', array(
          'description' => $description,
      ));
      $this->dummy->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }
  
    //Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => $orCondition,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    //DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array('decorators' => array('FormElements', 'DivDivDivWrapper')));
  }

}
