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

class Sesvideo_Widget_VideoHomeErrorController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create');

    $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->countVideos(array());

    if (count($this->view->paginator) > 0)
      return $this->setNoRender();
  }

}
