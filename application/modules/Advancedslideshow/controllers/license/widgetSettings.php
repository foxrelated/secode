<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//GET SLIDESHOW TABLE
$slideshowTable = Engine_Api::_()->getDbtable('advancedslideshows', 'advancedslideshow');
$slideshowTableName = $slideshowTable->info('name');

//GET PAGE TABLE
$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
$pageTableName = $pageTable->info('name');

//FETCH PAGE ID FOR HOME PAGE
$page_id = $widget_page = $pageTable->select()
                    ->from($pageTable->info('name'), array('page_id'))
                    ->where("name = ?", 'core_index_index')
                    ->group('page_id')
										->query()
                    ->fetchColumn();

//PROCEED IF PAGE ID IS NOT EMPTY
if(!empty($widget_page)) {

	//GET CONTENT TABLE
	$contentTable = Engine_Api::_()->getDbtable('content', 'core');
	$contentTableName = $contentTable->info('name');

	//CHECK TAHT WIDGET IS ALREADY CREATED FOR HOME PAGE MIDDLE COLUMN1
	$content_id = $contentTable->select()
											->from($contentTable->info('name'), array('content_id'))
											->where("name = ?", 'advancedslideshow.middle1-advancedslideshows')
											->where('page_id = ?', $widget_page)
											->group('page_id')
											->query()
											->fetchColumn();

	//CHECK TAHT WIDGET IS ALREADY CREATED FOR HOME PAGE MIDDLE COLUMN1
	$advancedslideshow_id = $slideshowTable->select()
																				->from($slideshowTableName, array('advancedslideshow_id'))
																				->where('widget_page = ?', $widget_page)
																				->where('widget_position = ?', 'middle_column1')
																				->query()
																				->fetchColumn();

	//LEFT & RIGHT COLUMN SHOULD BE THERE FOR WIDGET
	$left_container_id = $contentTable->select()
											->from($contentTable->info('name'), array('content_id'))
											->where("type = ?", 'container')
											->where("name = ?", 'left')
											->where('page_id = ?', $widget_page)
											->query()
											->fetchColumn();

	//LEFT & RIGHT COLUMN SHOULD BE THERE FOR WIDGET
	$right_container_id = $contentTable->select()
											->from($contentTable->info('name'), array('content_id'))
											->where("type = ?", 'container')
											->where("name = ?", 'right')
											->where('page_id = ?', $widget_page)
											->query()
											->fetchColumn();

	//PROCEED IF WIDGE IS NOT CREATED FOR HOME PAGE MIDDLE COLUMN1
	if(empty($content_id) && empty($advancedslideshow_id) && !empty($left_container_id) && !empty($right_container_id)) {
		$slideshow = $slideshowTable->createRow();
		$slideshow->owner_id = 1;
		$slideshow->owner_type = 'user';
		$slideshow->widget_title = 'Home Page - Middle Column';
		$slideshow->slideshow_type = 'fadd';
		$slideshow->widget_position = 'middle_column1';
		$slideshow->widget_page = $widget_page;
		$slideshow->width = 517;
		$slideshow->height = 250;
		$slideshow->target = 0;
		$slideshow->blinds = 24;
		$slideshow->interval = 8000;
		$slideshow->delay = 2000;
		$slideshow->duration = 750;
		$slideshow->progressbar = 1;
		$slideshow->overlap = 1;
		$slideshow->random = 0;
		$slideshow->slide_title = 1;
		$slideshow->slide_caption = 1;
		$slideshow->caption_position = 1;
		$slideshow->controller = 1;
		$slideshow->thumbnail = 1;
		$slideshow->start_index = 0;
		$slideshow->caption_backcolor = '#000000';
		$slideshow->thumb_backcolor = '#ffffff';
		$slideshow->thumb_bordcolor = '#DDDDDD';
		$slideshow->thumb_bordactivecolor = '#E9F4FA';
		$slideshow->enabled = 1;
		$slideshow->network = 0;
		$slideshow->level = 0;
		$slideshow->slide_resize = 1;
		$slideshow->save();

		//PLACED SLIDESHOW AT RESPECTIVE POSITION AND PAGE AFTER SUCCESSFULLY CREATION
		$widgetName = 'advancedslideshow.middle1-advancedslideshows';
		$contentTable   = Engine_Api::_()->getDbtable('content', 'core');
		$contentTableName = $contentTable->info('name');
		$selectContainer  = $contentTable->select()
						->from($contentTableName, array('content_id'))
						->where('page_id = ?', $page_id)
						->where('name = ?', 'main')
						->where('type = ?', 'container')
						->limit(1);
		$fetchContent = $selectContainer->query()->fetchAll();
		if ( !empty($fetchContent) ) {

			//FIND OUT THE CONTEND ID OF POSITION WHERE WE HAVE TO PLACED THE WIDGET
			$container_id = $fetchContent[0]['content_id'];
			$selectPosition  = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('parent_content_id = ?', $container_id)
							->where('type = ?', 'container')
							->where('name = ?', 'middle')
							->limit(1);
			$fetchContent = $selectPosition->query()->fetchAll();
			if( !empty($fetchContent) ) {

				//CHECK THAT WIDGET SHOULD NOT BE ALREADY THERE
				$pagePositionId = $fetchContent[0]['content_id'];
				$IsWidgetSelect  = $contentTable->select()
								->from($contentTableName, array('content_id'))
								->where('page_id = ?', $page_id)
								->where('type = ?', 'widget')
								->where('name = ?', $widgetName)
								->where('parent_content_id = ?', $pagePositionId);
				$IsWudgetEnabled = $IsWidgetSelect->query()->fetchAll();
				if( empty($IsWudgetEnabled) ) {

					//FINALLY CREATE THE WIDGET
					$widgetDisplay = $contentTable->createRow();   
					$widgetDisplay->page_id = $page_id;
					$widgetDisplay->type = 'widget';
					$widgetDisplay->name = $widgetName;
					$widgetDisplay->parent_content_id = $pagePositionId;
					$widgetDisplay->order = 9999;
					$widgetDisplay->save();

					//SAVE CONTENT ID IN 'engine4_advancedslideshows'
					$slideshow->widget_content_id = $widgetDisplay->content_id;
					$slideshow->save();
				}
				else {
					//SAVE CONTENT ID IN 'engine4_advancedslideshows'
					$slideshow->widget_content_id = $IsWudgetEnabled[0]['content_id'];
					$slideshow->save();
				}
			}
		}
	}
}

?>