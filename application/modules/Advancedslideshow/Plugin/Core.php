<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Advancedslideshow_Plugin_Core
{
	public function onRenderLayoutDefault($event)
  {
		//GET CONTENT TABLE
		$contentTable = Engine_api::_()->getDbTable('content', 'core');

		//MAKE QUERY
		$contentData = $contentTable->select()
													->from($contentTable->info('name'), array('content_id'))
													->where('type = ?', 'widget')
													->where('page_id >= ?', 1)
													->where('page_id <= ?', 2)
													->where('name LIKE ?', '%advancedslideshow.fullwidth%')
													->query()
													->fetchColumn();

		//IF CONTENT DATA IS NOT EMPTY
		if(!empty($contentData)) {

			$view = $event->getPayload();
			$view_object = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

			//CORE MODULE VERSION
			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
			$coreversion = $coremodule->version;
			$oldversion = 0;
			if($coreversion < '4.2.2') {
				$oldversion = 1;
			}

			//GET SLIDESHOW TABLE
			$slideshowTable = Engine_api::_()->getDbTable('advancedslideshows', 'advancedslideshow');

			$header_slideshow_type = '';
			$header_slideshow_type = $slideshowTable->select()
														->from($slideshowTable->info('name'), array('slideshow_type'))
														->where('widget_position = ?', 'full_width4')
														->where('enabled = ?', 1)
														->query()
														->fetchColumn();

			$footer_slideshow_type = '';
			$footer_slideshow_type = $slideshowTable->select()
														->from($slideshowTable->info('name'), array('slideshow_type'))
														->where('widget_position = ?', 'full_width5')
														->where('enabled = ?', 1)
														->query()
														->fetchColumn();

			if($header_slideshow_type == 'flom' || $footer_slideshow_type == 'flom') {
				$view->headLink()
							->prependStylesheet($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/floom.css');

				if(!empty($oldversion)) {
					$view->headScript()
							->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/floom.js');
				}
				else {
					$view->headScript()
							->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/floom.js');
				}
			}

                        if((!empty($footer_slideshow_type) && ($footer_slideshow_type == 'noob')) || (!empty($header_slideshow_type) && ($header_slideshow_type == 'noob'))) {
                            $view->headLink()->prependStylesheet($view_object->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_noobslideshow.css');
                            $view->headScript()->appendFile($view_object->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
                        }
                
			if(($footer_slideshow_type != '' &&  $footer_slideshow_type != 'flom') || ($header_slideshow_type != '' && $header_slideshow_type != 'flom')) {
				$view->headLink()
							->prependStylesheet($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/styles/slideshow.css');                                

				if(!empty($oldversion)) {
					$view->headScript()
							->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.js');
				}
				else {
					$view->headScript()
							->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.js');
				}

				if($header_slideshow_type == 'fold' || $footer_slideshow_type == 'fold') {
					if(!empty($oldversion)) {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.fold.js');
					}
					else {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.fold.js');
					}
				}
		
				if($header_slideshow_type == 'zndp' || $footer_slideshow_type == 'zndp') {
					if(!empty($oldversion)) {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.kenburns.js');
					}
					else {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.kenburns.js');
					}
				}

				if($header_slideshow_type == 'flas' || $footer_slideshow_type == 'flas') {
					if(!empty($oldversion)) {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/oldversion/slideshow.flash.js');
					}
					else {
						$view->headScript()
								->appendFile($view_object->layout()->staticBaseUrl.'application/modules/Advancedslideshow/externals/scripts/slideshow.flash.js');
					}
				}
			}
		}
	}
}
?>