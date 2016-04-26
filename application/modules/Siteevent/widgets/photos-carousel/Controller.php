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
class Siteevent_Widget_PhotosCarouselController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $siteeventPhotosCarousel = Zend_Registry::isRegistered('siteeventPhotosCarousel') ? Zend_Registry::get('siteeventPhotosCarousel') : null;

        $this->view->album = $album = $siteevent->getSingletonAlbum();
        $this->view->photo_paginator = $photo_paginator = $album->getCollectiblesPaginator();
        $this->view->total_images = $photo_paginator->getTotalItemCount();
        $minMum = $this->_getParam('minMum', 0);

        if (empty($siteeventPhotosCarousel) || empty($this->view->total_images) || $this->view->total_images < $minMum) {
            return $this->setNoRender();
        }

        $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 3);
        $this->view->includeInWidget = $this->_getParam('includeInWidget', null);
        $photo_paginator->setItemCountPerPage(100);

        if ($this->view->includeInWidget) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
    }

}