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
class Siteevent_Widget_PriceInfoSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') && !Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            return $this->setNoRender();
        }

        $this->view->review = $review = '';
        if (Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();
        } elseif (Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            $this->view->review = $review = Engine_Api::_()->core()->getSubject();
            $this->view->siteevent = $siteevent = $review->getParent();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0)) {
            return $this->setNoRender();
        }
        $this->view->layout_column = $this->_getParam('layout_column', 1);


        $params = array();
        if ($this->view->layout_column) {
            $params['limit'] = $this->_getParam('limit', 4);
        }

        $priceInfoTable = Engine_Api::_()->getDbTable('priceinfo', 'siteevent');
        $this->view->priceInfos = $priceInfoTable->getPriceDetails($siteevent->event_id, $params);

        if (Count($this->view->priceInfos) <= 0) {
            return $this->setNoRender();
        }

        $this->view->show_price = (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0) == 1) ? 0 : 1;
        if ($this->view->show_price)
            $this->view->min_price = $priceInfoTable->getMinPrice($siteevent->event_id);
        $this->view->tab_id = "";

        if ($review)
            $this->view->tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.price-info-siteevent');
    }

}
