<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_LedBySiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->item = $item = Engine_Api::_()->getItem($siteevent->getParent()->getType(), $siteevent->getParent()->getIdentity());
        $shortType = strtolower($item->getShortType());
        $moduleSiteevent = Engine_Api::_()->getDbtable('modules', 'siteevent');
        if ($siteevent->getParent()->getType() == 'sitereview_listing') {
            $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $item->listingtype_id);
            $this->view->title = $title = "(" . ucfirst($listingType->title_singular) . ") -";
        } elseif ($siteevent->getParent()->getType() != 'user') {
            $this->view->title = $title = "(" . ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.$shortType", $shortType)) . ") -";
        }
    }

}