<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MusicSearch.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Form_MusicSearch extends Engine_Form {

  public function init() {

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box layout_music_browse_search',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('GET');

    parent::init();

    $this->addElement('Text', 'search', array(
        'label' => 'Search Music:'
    ));

    $this->addElement('Select', 'show', array(
        'label' => 'Show',
        'onchange' => '',
        'multiOptions' => array(
            '1' => 'Everyone\'s Playlists',
            '2' => 'Only My Friends\' Playlists',
        ),
    ));

    $this->addElement('Select', 'sort', array(
        'label' => 'Browse By:',
        'onchange' => '',
        'multiOptions' => array(
            'recent' => 'Most Recent',
            'popular' => 'Most Popular',
        ),
    ));

    $this->addElement('Hidden', 'user');
  }

}