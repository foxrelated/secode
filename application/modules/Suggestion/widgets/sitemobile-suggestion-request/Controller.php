<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Suggestion_Widget_SitemobileSuggestionRequestController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
        $this->view->requests = $requests = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getRequestsPaginator($viewer, 'suggestion_true');
        $requests->setItemCountPerPage('100');
        }

}
