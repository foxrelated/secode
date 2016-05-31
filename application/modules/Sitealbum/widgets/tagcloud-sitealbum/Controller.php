<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_TagcloudSitealbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
      return $this->setNoRender();
    }

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('loaded_by_ajax', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }

    if (($module == 'sitealbum' && $controller == 'index' && $action == 'tagscloud') || $this->_getParam('notShowExploreTags', false)) {
      $this->view->notShowExploreTags = true;
      $params['notShowExploreTags'] = true;
    }

    if (Engine_Api::_()->core()->hasSubject('album')) {

      //GET SUBJECT
      $sitealbum = Engine_Api::_()->core()->getSubject();

      //GET OWNER INFORMATION
      $this->view->owner_id = $owner_id = $sitealbum->owner_id;
      $this->view->owner = $sitealbum->getOwner();
    } else {
      $this->view->owner_id = $owner_id = 0;
    }

    $params = array();
    $params['orderingType'] = $this->_getParam('orderingType', '1');

    //HOW MANY TAGS WE HAVE TO SHOW
    $total_tags = $this->_getParam('itemCount', 25);

    $this->view->allParams = $params;

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $sitealbum_api = Engine_Api::_()->sitealbum();
    $this->view->count_only = $sitealbum_api->getTags($owner_id, 0, 1, $params);
    if ($this->view->count_only <= 0) {
      return $this->setNoRender();
    }

    $element = $this->getElement();
    $title = $element->getTitle();
    if(empty($title))
        $title = 'Popular Album Tags';
    if ($this->view->owner_id == 0) {
      $element->setTitle(sprintf($this->view->translate($title, (int)
$this->view->count_only)));
    } else {
      $element->setTitle(sprintf($this->view->translate($title,
$this->view->owner->getTitle())));
    }

    if (!$this->view->is_ajax_load)
      return;

    //FETCH TAGS
    $tag_cloud_array = $sitealbum_api->getTags($owner_id, $total_tags, 0, $params);

    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
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
  }

}