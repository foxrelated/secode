<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ArtistSettings.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_ArtistSettings extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $link = $view->baseUrl() . '/admin/sesmusic/settings/artists/';

    $description = $this->getTranslator()->translate('Below settings will affect artists on your website. You can add artists on your website from the Manage Artists section from here:');

    $moreinfo = $this->getTranslator()->translate('More Info: <a href="%1$s" target="_blank"> "Manage Artits"</a>');

    $description = vsprintf($description . $moreinfo, array($link));
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $this->setTitle('Artist Settings')
            ->setDescription($description);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('MultiCheckbox', 'sesmusic_artistlink', array(
        'label' => 'Allow "Add to Favorite"',
        'description' => 'Do you want to allow members of your website to add artists to their favorites?',
        'multiOptions' => array('favourite' => 'Add to Favourite'),
        'value' => unserialize($settings->getSetting('sesmusic.artistlink', 'a:3:{i:0;s:9:"favourite";}')),
    ));

    $this->addElement('Radio', 'sesmusic_artist_rating', array(
        'label' => 'Allow Rating',
        'description' => 'Do you want to allow users to give ratings on artists on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'rating_artist(this.value)',
        'value' => $settings->getSetting('sesmusic.artist.rating', 1),
    ));

    $this->addElement('Radio', 'sesmusic_rateartist_again', array(
        'label' => 'Allow to Edit Rating',
        'description' => 'Do you want to allow users to edit their ratings on artists on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.rateartist.again', 1),
    ));

    $this->addElement('Radio', 'sesmusic_rateartist_show', array(
        'label' => 'Show Earlier Rating',
        'description' => 'Do you want to show earlier ratings on artists on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.rateartist.show', 1),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}