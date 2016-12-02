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
class Sitetagcheckin_Widget_SynceventsLocationController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();

    if ($moduleName == 'event' || $moduleName == 'ynevent') {

			//IF THERE IS NO SUBJECT THEN SET NO RENDER
			if (!Engine_Api::_()->core()->hasSubject()) {
				return $this->setNoRender();
			}

			$subject = Engine_Api::_()->core()->getSubject();
			$seLocationsTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
			if (empty($subject->seao_locationid)) {
				$select = $seLocationsTable->select()->where('location = ?', $subject->location);
				$results = $seLocationsTable->fetchRow($select);
				$eventstable = Engine_Api::_()->getItemTable('event');
				if(empty($results->location_id)) {
					//Accrodeing to event  location entry in the seaocore location table.
					if (!empty($subject->location)) {
						$seaoLocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocationId($subject->location);
						//event table entry of location id.
						$eventstable->update(array('seao_locationid'=>  $seaoLocation), array('event_id =?' => $subject->event_id));
					}
				} else {
					//event table entry of location id.
					$eventstable->update(array('seao_locationid'=>  $results->location_id), array('event_id =?' => $subject->event_id));
				}
			}
	  }
		return $this->setNoRender();
  }
}