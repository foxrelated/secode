<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Widget_CategoryController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->contentType = $this->_getParam('contentType', 'album');
    $this->view->showType = $this->_getParam('showType', 'simple');
    $this->view->height = $this->_getParam('height', '150');
    $this->view->color = $this->_getParam('color', '#00f');
    $this->view->textHeight = $this->_getParam('text_height', '15');
    $this->view->image = $image = $this->_getParam('image', 1);
    $this->view->storage = Engine_Api::_()->storage();
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesmusic');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    if ($this->view->showType == 'tagcloud' && $this->view->image == 0)
      $this->view->categories = $categoriesTable->getCategory(array('column_name' => '*', 'image' => 1, 'param' => $this->view->contentType));
    else
      $this->view->categories = $categoriesTable->getCategory(array('column_name' => '*', 'param' => $this->view->contentType));

    if (count($this->view->categories) <= 0)
      return $this->setNoRender();
  }

}
