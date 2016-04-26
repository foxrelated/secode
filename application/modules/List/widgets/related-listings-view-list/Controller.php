<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_RelatedListingsViewListController extends Engine_Content_Widget_Abstract
{ 
  public function indexAction()
  { 
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject('list_listing')) {
			return $this->setNoRender();
		}

		//GET LISTING SUBJECT
		$subject = Engine_Api::_()->core()->getSubject();

		//GET VARIOUS WIDGET SETTINGS
		$this->view->statisticsRating = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1);
		$this->view->truncation = $this->_getParam('truncation', 23);
		$related = $this->_getParam('related', 'categories');

		$params = array();

		If($related == 'tags') {

			//GET TAGS
			$listingTags = $subject->tags()->getTagMaps();

			$params['tags'] = array();
			foreach ($listingTags as $tag) {
				$params['tags'][] = $tag->getTag()->tag_id;
			}

			if(empty($params['tags'])) {
				return $this->setNoRender();
			}

		}
		elseif($related == 'categories') {
			$params['category_id'] = $subject->category_id;
		}
		else {
			return $this->setNoRender();
		}

    //FETCH LISTINGS
		$params['listing_id'] = $subject->listing_id;
    $params['orderby'] ='RAND()';
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = Engine_Api::_()->getDbtable('listings', 'list')->widgetListingsData($params);

    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}