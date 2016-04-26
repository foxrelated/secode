<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: EditArtist.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesmusic_Form_Admin_EditArtist extends Sesmusic_Form_Admin_AddArtist {

  public function init() {

    parent::init();
    $this->setTitle('Edit This Artist')->setDescription('Edit the details of the artist.');
  }

}