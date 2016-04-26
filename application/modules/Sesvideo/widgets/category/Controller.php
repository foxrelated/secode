<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Widget_CategoryController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->contentType = $this->_getParam('contentType', 'video');
    $this->view->showType = $this->_getParam('showType', 'simple');
    $this->view->height = $this->_getParam('height', '150');
    $this->view->color = $this->_getParam('color', '#00f');
    $this->view->textHeight = $this->_getParam('text_height', '15');
    $this->view->image = $image = $this->_getParam('image', 1);
    $this->view->storage = Engine_Api::_()->storage();
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesvideo');

    if ($this->view->showType == 'tagcloud' && $this->view->image == 0)
      $this->view->categories = $categoriesTable->getCategory(array('column_name' => '*', 'image' => 1, 'param' => $this->view->contentType));
    else
      $this->view->categories = $categoriesTable->getCategory(array('column_name' => '*', 'param' => $this->view->contentType));

    if (count($this->view->categories) <= 0)
      return $this->setNoRender();
  }

}
