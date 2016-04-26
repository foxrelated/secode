<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: EditArtist.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_EditArtist extends Sesvideo_Form_Admin_AddArtist {

  public function init() {

    parent::init();
    $this->setTitle('Edit This Artist')->setDescription('Edit the details of the artist.');
  }

}
