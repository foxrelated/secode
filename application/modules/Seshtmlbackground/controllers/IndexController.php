<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Seshtmlbackground_IndexController extends Core_Controller_Action_Standard
{
  public function searchAction() {
  
    $text = $this->_getParam('text', null);
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $select = $table->select()->where('title LIKE ? OR description LIKE ? OR keywords LIKE ? OR hidden LIKE ?', '%' . $text . '%');
    $select->limit('10');
    $results = Zend_Paginator::factory($select);
    foreach ($results as $result) {
      $itemType = $result->type;
      if(Engine_Api::_()->hasItemType($itemType)) {
        if($itemType == 'sesblog')
        continue;
        $item = Engine_Api::_()->getItem($itemType, $result->id);
        $item_type = ucfirst($item->getShortType());
	$photo_icon_photo = $this->view->itemPhoto($item, 'thumb.icon');
	$data[] = array(
	  'id' => $result->id,
	  'label' => $item->getTitle(),
	  'photo' => $photo_icon_photo,
	  'url' => $item->getHref(),
	  'resource_type' => $item_type,
	);
      }
    }
    $data[] = array(
      'id' => 'show_all',
      'label' => $text,
      'url' => 'all',
      'resource_type' => ''
    );
    return $this->_helper->json($data);
  }
}