<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_BreadcrumbAlbumViewController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    if (!Engine_Api::_()->core()->hasSubject('album'))
      return $this->setNoRender();
    $this->view->album = Engine_Api::_()->core()->getSubject('album');
  }
}