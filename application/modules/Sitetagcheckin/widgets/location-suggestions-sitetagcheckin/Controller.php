<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Widget_LocationSuggestionsSitetagcheckinController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();    
    $viewer_id = $viewer->getIdentity();

    //GET ADDLOCATION TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
    $addlocationsTableName = $addlocationsTable->info('name');

		$moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModuleAdvalbum = $moduleCore->isModuleEnabled('advalbum');
    if($getEnableModuleAdvalbum) {
			$albumTable = Engine_Api::_()->getItemTable('advalbum_album');
      $albumresource_type = 'advalbum_album';
    } else {
			$albumTable = Engine_Api::_()->getDbtable('albums', 'album');
      $albumresource_type = 'album';
    }
    $albumTableName = $albumTable->info('name');
    $select = $albumTable
                   ->select()
                   ->setIntegrityCheck(false)
                   ->from($albumTableName, array('album_id'))
                   ->join($addlocationsTableName, "$addlocationsTableName.resource_id = $albumTableName.album_id", null)
                   ->where("$addlocationsTableName.resource_type =?", $albumresource_type)
                   ->where("$addlocationsTableName.owner_id =?", $viewer_id);
    $rows = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    $select = $albumTable->select()->from($albumTableName, array('*'))->where('owner_id =?',$viewer_id);

    if(!empty($rows)) {
      $select->where('album_id not in (?)', new Zend_Db_Expr(trim(implode(",", $rows))));
    }

    $select->order('RAND()');

		$row = $albumTable->fetchRow($select);

    if(empty($row))
     return $this->setNoRender();
   
    if(!empty($row)) {
      $album_id = $row->album_id;
			$photoTable = Engine_Api::_()->getDbtable('photos', 'album');
			$select = $photoTable->getPhotoSelect(array('album_id' => $album_id));
      $photoIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			$photoTableName = $photoTable->info('name');
      $this->view->photorow=$photorow="";
      if(!empty($photoIds)) {
				 $select = $addlocationsTable
											->select()
											->setIntegrityCheck(false)
											->from($addlocationsTableName, array('action_id', 'params'))
											->join($photoTableName, "$photoTableName.photo_id = $addlocationsTableName.resource_id", null)
											->where("$addlocationsTableName.resource_type =?", 'album_photo')
											->where("$addlocationsTableName.resource_id in (?)", new Zend_Db_Expr(trim(implode(",", $photoIds))))
											->where("$addlocationsTableName.owner_id =?", $viewer_id)
											->order("$addlocationsTableName.addlocation_id ASC");
				 $this->view->photorow = $photorow = $addlocationsTable->fetchRow($select);
       }
       if(empty($photorow)) {
         $this->view->albumrow = $row;
       } else {
         $this->view->albumrow = $row;
         $this->view->photorow = $photorow; 
         $this->view->params = $params = $photorow->params;

				 //INITILISING DISPALY LOCATION 
				 $this->view->displayLocation = "";

				 //INITILISING GET LOCATION
				 $this->view->getLocation = "";

				 //CHECK PARAMS
				 if (!empty($params)) {

					//CHECK CHECKIN PARAMS EXIST OR NOT
					if (is_array($params) && isset($params['checkin'])) {
						//GET CHECKIN
						$this->view->checkin = $checkin = $params['checkin'];

						//GET PREFIX
						if ($checkin['label'] != false) {
							$this->view->addprefix = $addPrifix = (!empty($checkin['prefixadd'])) ? $this->view->translate($checkin['prefixadd']) : $this->view->translate('at');
						} else {
							$this->view->addprefix = $addPrifix = "";
						}
						$checkinTypeArray = array('Page', 'Business');
						$customcheckinTypeArray = array('Classified', 'Listing', 'Recipe', 'Event');
						if (isset($checkin['type']) && ($checkin['type'] == 'place' || in_array($checkin['type'], $customcheckinTypeArray))) {
							$this->view->action_id = $photorow->action_id;
							if ($this->view->action_id < 0) {
								$this->view->action_id = abs($photorow->action_id);
							}
							$this->view->displayLocation = 1;
							if($checkin['vicinity']) {
								if(isset($checkin['name']) && $checkin['name'] && $checkin['name'] != $checkin['vicinity']) {
									$checkin['label'] = $checkin['name'] . ', ' . $this->view->checkin['vicinity'];
								} else {
									$checkin['label'] = $checkin['vicinity'];
								}
							}
							$this->view->getLocation = $checkin['label'];
						} elseif (isset($checkin['type']) && $checkin['type'] == 'just_use') {
							$this->view->getLocation = $this->view->displayLocation = $checkin['label'];
						} else if (isset($checkin['type']) && in_array($checkin['type'], $checkinTypeArray)) {
							$item = Engine_Api::_()->getItemByGuid($checkin['resource_guid']);
							if ($item) {
								$this->view->displayLocation = $addPrifix . " " . $this->view->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity()));
								$this->view->getLocation = $item->getTitle();
							}
						 }
					 }
				 }
       }
    }
  }

}