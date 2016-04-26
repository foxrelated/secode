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
class Siteevent_Widget_QuickSpecificationSiteeventController extends Seaocore_Content_Widget_Abstract {

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

        //LISITNG SHOULD BE MAPPED WITH PROFILE
        if (empty($this->view->siteevent->profile_type)) {
            return $this->setNoRender();
        }

        $itemCount = $this->_getParam('itemCount', 5);

        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($siteevent);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSiteevent($siteevent, $this->view->fieldStructure, $itemCount);
        } else {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSMSiteevent($siteevent, $this->view->fieldStructure, $itemCount);
        }
        if (empty($this->view->show_fields)) {
            return $this->setNoRender();
        }

        //GET WIDGET SETTINGS
        $this->view->show_specificationlink = $this->_getParam('show_specificationlink', 1);

        //GET WIDGET SETTINGS
        $this->view->show_specificationtext = $this->_getParam('show_specificationtext', 'More Information');
        if (empty($this->view->show_specificationtext)) {
            $this->view->show_specificationtext = 'More Specifications';
        }

        //FETCH CONTENT DETAILS
        if (!empty($review)) {
            $this->view->tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.specification-siteevent');
        } else {
            $this->view->contentDetails = Engine_Api::_()->siteevent()->getWidgetInfo('siteevent.specification-siteevent', $this->view->identity);

            if (empty($this->view->contentDetails)) {
                $this->view->contentDetails->content_id = 0;
            }
        }
    }

}