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
class Siteevent_Widget_TagcloudSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();
        $this->view->loaded_by_ajax = $is_ajax_load = $this->_getParam('loaded_by_ajax', true);
        $this->view->isajax = $isajax = $this->_getParam('isajax', false);
        $allParams  = array('isajax' => 1, 'loaded_by_ajax' => 1);
        if (($module == 'siteevent' && $controller == 'index' && $action == 'tagscloud') || $this->_getParam('notShowExploreTags', false)) {
            $this->view->notShowExploreTags = true;
            $allParams['notShowExploreTags'] = true;
        }
        $this->view->allParams  = $allParams;
        if (Engine_Api::_()->core()->hasSubject('siteevent_event')) {

            //GET SUBJECT
            $siteevent = Engine_Api::_()->core()->getSubject();

            //GET OWNER INFORMATION
            $this->view->owner_id = $owner_id = $siteevent->owner_id;
            $this->view->owner = $siteevent->getOwner();
        } else {
            $this->view->owner_id = $owner_id = 0;
        }

        $params = array();
        $params['orderingType'] = $this->_getParam('orderingType', '1');
        $params['eventType'] = $this->_getParam('eventType', 'upcoming');

        //HOW MANY TAGS WE HAVE TO SHOW
        $total_tags = $this->_getParam('itemCount', 25);

        //CONSTRUCTING TAG CLOUD
        $tag_array = array();
        $siteevent_api = Engine_Api::_()->siteevent();

          $element = $this->getElement();
          if(strstr($element->getTitle(), '%s')) {            
            if ($this->view->owner_id == 0) {
                $count_only = $siteevent_api->getTags($owner_id, 0, 1, $params);
                $element->setTitle($this->view->translate($element->getTitle(), $count_only));
            } else {              
                $element->setTitle($this->view->translate($element->getTitle(), $this->view->owner->getTitle()));
            }
          }

        if (!$is_ajax_load || ($is_ajax_load && $isajax)) {
      //FETCH TAGS
      $tag_cloud_array = $siteevent_api->getTags($owner_id, $total_tags, 0, $params);
      if(is_array($tag_cloud_array)) {
        foreach ($tag_cloud_array as $vales) {
          $tag_array[$vales['text']] = $vales['Frequency'];
          $tag_id_array[$vales['text']] = $vales['tag_id'];
        }
      }

      if (!empty($tag_array)) {
        $max_font_size = 18;
        $min_font_size = 12;
        $max_frequency = max(array_values($tag_array));
        $min_frequency = min(array_values($tag_array));
        $spread = $max_frequency - $min_frequency;
        if ($spread == 0) {
          $spread = 1;
        }
        $step = ($max_font_size - $min_font_size) / ($spread);

        $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);
        $this->view->tag_data = $tag_data;
        $this->view->tag_id_array = $tag_id_array;
      }
      $this->view->tag_array = $tag_array;

      if (empty($this->view->tag_array)) {
        return $this->setNoRender();
      }
      $this->view->showcontent = true;
      if($isajax) {        
        $this->getElement()->removeDecorator('Container');
      }
    }
  }

}