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
class Sesvideo_Widget_chanelVideoLabelController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		
	if (!Engine_Api::_()->core()->hasSubject('video') && !Engine_Api::_()->core()->hasSubject('sesvideo_chanel')) {
      return $this->setNoRender();
    }
		if(Engine_Api::_()->core()->hasSubject('video'))
   	 $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');
		else{
			 $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sesvideo_chanel');  
			 $setting = Engine_Api::_()->getApi('settings', 'core');
			if (!$setting->getSetting('video_enable_chanel', 1)) {
				return $this->setNoRender();
			}
		}
		
		$this->view->option = $this->_getParam('option',array('hot','verified','sponsored','featured'));
  }
}