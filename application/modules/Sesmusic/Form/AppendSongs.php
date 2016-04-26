<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AppendSongs.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_AppendSongs extends Engine_Form {

  public function init() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $playlistCount = Engine_Api::_()->getDbtable('playlists', 'sesmusic')->getPlaylistsCount(array('viewer_id' => $viewer->getIdentity(), 'column_name' => array('playlist_id', 'title')));

    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesmusic_album', 'addplaylist_max');
    $this->setTitle('Add Songs of this Music Album to Playlist')
            ->setAttrib('id', 'form-playlist-append')
            ->setAttrib('name', 'playlist_add')
            ->setAction($_SERVER['REQUEST_URI']);

    //Init playlist
    $playlists = array();
    if ($quota > count($playlistCount) || $quota == 0)
      $playlists[0] = Zend_Registry::get('Zend_Translate')->_('Create New Playlist');

    $this->addElement('Select', 'playlist_id', array(
        'label' => 'Choose Playlist',
        'multiOptions' => $playlists,
        'onchange' => "updateTextFields()",
    ));

    if ($quota > count($playlistCount) || $quota == 0) {

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
    } else {
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have already created the maximum number of playlists allowed. If you would like to create a new playlist, please delete an old one first. Currently, you can only add songs in your existing playlists.") . "</span></div>";
      $this->addElement('Dummy', 'dummy', array(
          'description' => $description,
      ));
      $this->dummy->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    //Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Add to Playlist',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    //Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    //DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array('decorators' => array('FormElements', 'DivDivDivWrapper')));
  }

}