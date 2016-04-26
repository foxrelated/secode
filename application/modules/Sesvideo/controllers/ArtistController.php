<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ArtistController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_ArtistController extends Core_Controller_Action_Standard {

  //Browse Action
  public function browseAction() {
    $this->_helper->content->setEnabled();
  }

  //Artist View Action
  public function viewAction() {
    $this->_helper->content->setEnabled();
  }

}
