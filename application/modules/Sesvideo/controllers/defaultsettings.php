<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$select = new Zend_Db_Select($db);
$select->from('engine4_sesvideo_slides');
$slideContent = $select->query()->fetch();
if (empty($slideContent)) {
		$db->query("INSERT IGNORE INTO `engine4_sesvideo_galleries` (`gallery_name`,`creation_date`,`modified_date`) VALUES ( 'Welcome Page','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')");
		$gallery_id = $db->lastInsertId();
		$db->query("INSERT INTO `engine4_sesvideo_slides` (`gallery_id`, `title`, `title_button_color`, `description`, `description_button_color`, `thumb_icon`, `file_type`, `file_id`, `login_button`, `extra_button`, `signup_button`, `login_button_color`, `login_button_mouseover_color`, `login_button_text`, `login_button_text_color`, `signup_button_color`, `signup_button_mouseover_color`, `signup_button_text`, `signup_button_text_color`, `show_register_form`, `position_register_form`, `extra_button_color`, `extra_button_mouseover_color`, `extra_button_text`, `extra_button_text_color`, `extra_button_link`, `creation_date`, `modified_date`, `order`) VALUES
		( ".$gallery_id.", 'We make worth watching', 'FFFFFF', 'Watch out world’s best videos from our community!!', 'FFFFFF','0', 'mp4', '0', 0, 1, 0, 'FFFFFF', 'EEEEEE', 'Login', '0295FF', '0295FF', '067FDE', 'Signup', 'FFFFFF', 0, 'right', 'F25B3B', 'EA350F', 'Browse All Videos', 'FFFFFF', '/videos/browse',  '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', 0)");
		$slide_id = $db->lastInsertId();
		$storage = Engine_Api::_()->getItemTable('storage_file');
		$filePathVideo = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "video" ;
		if(is_file($filePathVideo. DIRECTORY_SEPARATOR . "welcome_video.mp4")){
		$filename = $storage->createFile($filePathVideo. DIRECTORY_SEPARATOR . "welcome_video.mp4", array(
				'parent_id' => $slide_id,
				'parent_type' => 'sesvideo_slide',
				'user_id' => 1,
		));
		// Remove temporary file
		@unlink($file['tmp_name']);
		$thumb_slide_field_id = $filename->file_id;
		if($thumb_slide_field_id)
		$db->query("UPDATE engine4_sesvideo_slides SET file_id = ".$thumb_slide_field_id." WHERE slide_id = 1");
		}
		if(is_file($filePathVideo. DIRECTORY_SEPARATOR . "welcome_video_icon.jpg")){
		$thumbname = $storage->createFile($filePathVideo. DIRECTORY_SEPARATOR . "welcome_video_icon.jpg", array(
									'parent_id' => $slide_id,
									'parent_type' => 'sesvideo_slide',
									'user_id' => 1,
							));
		// Remove temporary file
		@unlink($file['tmp_name']);
		$thumb_slide_icon = $thumbname->file_id;
		if($thumb_slide_icon)
			$db->query("UPDATE engine4_sesvideo_slides SET thumb_icon = ".$thumb_slide_icon." WHERE slide_id = 1");
		}
}

//Video Welcome Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_welcome')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_welcome',
      'displayname' => 'SES - Advanced Videos & Channels - Welcome Page',
      'title' => 'Video Welcome Page',
      'description' => 'This page is the video welcome page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  
  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $page_id,
    'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  
  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $page_id,
    'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  
  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  
  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  
	//Insert menu
	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesvideo.slideshow',
	'page_id' => $page_id,
	'parent_content_id' => $top_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"gallery_id":"1","full_width":"1","logo":"1","main_navigation":"1","mini_navigation":"1","autoplay":"1","thumbnail":"1","searchEnable":"1","height":"670","title":"","nomobile":"0","name":"sesvideo.slideshow"}',
	));

	//Insert content
	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesbasic.simple-html-block',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"bodysimple":"<div style=\"text-align: center;font-size: 34px;  margin: 30px; 30px\">Watch the world\u2019s best videos from our <br \/>passionate community<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
	));

	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesvideo.tabbed-widget-video',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"enableTabs":["grid"],"openViewType":"grid","viewTypeStyle":"mouseover","showTabType":"1","show_criteria":["watchLater","favouriteButton","location","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","title","category","by","duration"],"pagging":"button","title_truncation_grid":"39","title_truncation_list":"45","title_truncation_pinboard":"45","limit_data_pinboard":"10","limit_data_list":"10","limit_data_grid":"6","show_limited_data":"yes","description_truncation_list":"45","description_truncation_grid":"45","description_truncation_pinboard":"45","height_grid":"270","width_grid":"386","height_list":"230","width_list":"260","width_pinboard":"250","search_type":["recentlySPcreated"],"recentlySPupdated_order":"1","recentlySPcreated_label":"Recently Created","mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","mostSPliked_order":"3","mostSPliked_label":"Most Liked","mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","mostSPrated_order":"5","mostSPrated_label":"Most Rated","mostSPfavourite_order":"6","mostSPfavourite_label":"Most Favourite","hot_order":"7","hot_label":"Most Hot","featured_order":"6","featured_label":"Featured","sponsored_order":"7","sponsored_label":"Sponsored","title":"","nomobile":"0","name":"sesvideo.tabbed-widget-video"}',
	));

	$array['bodysimple'] = '<div style="text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom: 70px;"><a class="sesbasic_animation" href="/videos/browse/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size: 18px;border: 1px solid #345;text-align: center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;">Watch All Videos</a></div>';
	$array['show_content'] = 1;
	$array['title'] = '';
	$array['nomobile'] = 0;
	$array['name'] = 'sesbasic.simple-html-block';
	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesbasic.simple-html-block',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => json_encode($array),
	));



	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesbasic.simple-html-block',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"bodysimple":"<div style=\"font-size: 34px;margin-bottom: 50px;  margin-top: 50px;text-align: center;\">Here, people share your passions for Public Interest<\/span><\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
	));

	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesvideo.video-category',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"height":"230","width":"290","limit":"8","video_required":"0","criteria":"alphabetical","show_criteria":["title","icon"],"mouse_over_title":"1","title":"","nomobile":"0","name":"sesvideo.video-category"}',
	));
	$array['bodysimple'] = '<div style="text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom: 70px;"><a class="sesbasic_animation" href="/videos/categories/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size:18px;border:1px solid #345;text-align: center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;"> Browse All Categories</a></div><div style="font-size: 34px;text-align: center;margin-top:50px;">We are reimagined ... as a collection of interest-specific social channels.<br /><br /></div>';

	$array['show_content'] = 1;
	$array['title'] = '';
	$array['nomobile'] = 0;
	$array['name'] = 'sesbasic.simple-html-block';

	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesbasic.simple-html-block',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => json_encode($array),
	));


	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesvideo.popular-artists',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => '{"popularity":"favourite_count","viewType":"gridview","viewTypeStyle":"mouseover","height":"150","width":"188","limit":"12","title":"","nomobile":"0","name":"sesvideo.popular-artists"}',
	));
	$artists['bodysimple'] = '<div style="text-align: center;margin-top:50px;"><a class="sesbasic_animation" href="/videos/artists/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size:18px;border:1px solid #345;text-align:center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;">Browse All Artists</a><br ><br ></div>';
	$artists['show_content'] = 1;
	$artists['title'] = '';
	$artists['nomobile'] = 0;
	$artists['name'] = 'sesbasic.simple-html-block';
	$db->insert('engine4_core_content', array(
	'type' => 'widget',
	'name' => 'sesbasic.simple-html-block',
	'page_id' => $page_id,
	'parent_content_id' => $main_middle_id,
	'order' => $widgetOrder++,
	'params' => json_encode($artists),
	));
  
}


//Video Home Page
$select = $db->select()
        ->from('engine4_core_pages')
        ->where('name = ?', 'sesvideo_index_home')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_home',
      'displayname' => 'SES - Advanced Videos & Channels - Video Home Page',
      'title' => 'Video Home',
      'description' => 'This is the video home page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $topcontainer_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'left',
      'parent_content_id' => $container_id,
      'order' => 4,
      'params' => '',
  ));
  $left_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $topcontainer_id,
      'order' => 6,
      'params' => '',
  ));
  $topmiddle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');
  
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
  ));
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.video-home-error',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
  ));
  
    
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored-fixed-view',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"videos","featured_sponsored_carosel":"featured","show_criteria":["title","socialSharing","duration","watchlater","likeButton","favouriteButton"],"heightMain":"450","height":"150","info":"most_liked","title_truncation":"45","limit_data":"7","title":"Featured Videos","nomobile":"0","name":"sesvideo.featured-sponsored-fixed-view"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.of-the-day',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"ofTheDayType":"video","show_criteria":["like","comment","rating","view","title","by","socialSharing","likeButton","favouriteButton","favouriteCount","watchLater","songsListShow","duration"],"title_truncation":"22","height":"170","width":"180","title":"Video of the Day","nomobile":"0","name":"sesvideo.of-the-day"}',
  ));
  
      $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"grid","criteria":"5","info":"most_rated","show_criteria":["like","comment","rating","favourite","view","title","by","duration","watchLater"],"title_truncation":"24","height":"100","width":"105","limit_data":"3","title":"Top Rated Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
        $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"5","info":"most_liked","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Most Liked Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
          $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"5","info":"most_viewed","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Most Viewed Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
            $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.recently-viewed-item',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"video","type":"list","criteria":"on_site","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Recently Viewed Videos","nomobile":"0","name":"sesvideo.recently-viewed-item"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"video","view_type":"horizontal","search_type":"","default_search_type":"recentlySPcreated","friend_show":"no","search_title":"yes","browse_by":"no","categories":"yes","location":"yes","kilometer_miles":"no","title":"","nomobile":"0","name":"sesvideo.browse-search"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored-carosel',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"videos","featured_sponsored_carosel":"hot","show_criteria":["title"],"duration":"200","bgColor":"#eee","textColor":"","spacing":"","heightMain":"200","height":"170","width":"217","info":"recently_created","title_truncation":"24","limit_data":"9","aliganment_of_widget":"1","title":"Hot Videos","nomobile":"0","name":"sesvideo.featured-sponsored-carosel"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.tabbed-widget-video',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"enableTabs":["list","grid","pinboard"],"openViewType":"grid","viewTypeStyle":"mouseover","showTabType":"1","show_criteria":["watchLater","favouriteButton","location","playlistAdd","likeButton","socialSharing","like","favourite","comment","view","title","by","duration","descriptionlist","enableCommentPinboard"],"pagging":"pagging","title_truncation_grid":"24","title_truncation_list":"24","title_truncation_pinboard":"45","limit_data_pinboard":"6","limit_data_list":"6","limit_data_grid":"12","show_limited_data":"no","description_truncation_list":"100","description_truncation_grid":"45","description_truncation_pinboard":"45","height_grid":"190","width_grid":"212","height_list":"150","width_list":"220","width_pinboard":"335","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","hot"],"recentlySPupdated_order":"3","recentlySPcreated_label":"Recently Created","mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","mostSPliked_order":"1","mostSPliked_label":"Most Liked","mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","mostSPrated_order":"5","mostSPrated_label":"Most Rated","mostSPfavourite_order":"6","mostSPfavourite_label":"Most Favourite","hot_order":"7","hot_label":"Hot","featured_order":"6","featured_label":"Featured","sponsored_order":"7","sponsored_label":"Sponsored","title":"Popular Videos","nomobile":"0","name":"sesvideo.tabbed-widget-video"}',
  ));
  
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored-carosel',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"videos","featured_sponsored_carosel":"sponsored","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","duration","watchlater","likeButton","favouriteButton"],"duration":"300","bgColor":"","textColor":"","spacing":"","heightMain":"264","height":"200","width":"200","info":"most_liked","title_truncation":"24","limit_data":"8","aliganment_of_widget":"2","title":"Sponsored Videos","nomobile":"0","name":"sesvideo.featured-sponsored-carosel"}',
  ));
  
      $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"6","info":"most_liked","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Hot Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
      $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"5","info":"most_commented","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Most Commented Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
      $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"5","info":"most_favourite","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Most Favourite Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
      $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"list","criteria":"5","info":"recently_created","show_criteria":["like","comment","favourite","view","title","by","duration","watchLater"],"title_truncation":"11","height":"80","width":"105","limit_data":"3","title":"Most Recent Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
}


//Video Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_browse',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Videos Page',
      'title' => 'Browse Videos',
      'description' => 'This page lists videos.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-video',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"enableTabs":["list","grid","pinboard"],"openViewType":"grid","show_criteria":["watchLater","favouriteButton","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","title","category","by","duration","descriptionlist","descriptionpinboard","enableCommentPinboard"],"sort":"mostSPliked","title_truncation_list":"70","title_truncation_grid":"30","description_truncation_list":"230","description_truncation_grid":"45","description_truncation_pinboard":"60","height_list":"180","width_list":"260","height_grid":"270","width_grid":"305","width_pinboard":"305","limit_data_pinboard":"10","limit_data_grid":"15","limit_data_list":"20","pagging":"pagging","title":"","nomobile":"0","name":"sesvideo.browse-video"}',
  ));

  // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"video","view_type":"vertical","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored","verified","hot"],"default_search_type":"mostSPliked","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"Search Videos","nomobile":"0","name":"sesvideo.browse-search"}',
  ));
  
    // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu-quick',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '',
  ));
  
    // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.tag-cloud',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"color":"#000","type":"video","text_height":"15","height":"150","itemCountPerPage":"20","title":"Browse By Tags","nomobile":"0","name":"sesvideo.tag-cloud"}',
  ));
  
    // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.category',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"video","showType":"simple","image":"1","color":"#00f","text_height":"15","height":"150","title":"Browse By Category","nomobile":"0","name":"sesvideo.category"}',
  ));
  
    // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"video","type":"grid","criteria":"5","info":"most_viewed","show_criteria":["like","comment","rating","favourite","view","title","by","category","duration","watchLater"],"title_truncation":"45","height":"130","width":"180","limit_data":"3","title":"Most Viewed Videos","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
}


//Video Browse Pinboard Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_browse-pinboard')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_browse-pinboard',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Videos Pinboard Page',
      'title' => 'Browse Videos Pinboard',
      'description' => 'This page lists videos.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"video","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored","verified","hot"],"default_search_type":"mostSPliked","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesvideo.browse-search"}',
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-video',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"enableTabs":["pinboard"],"viewTypeStyle":"fixed","openViewType":"pinboard","show_criteria":["featuredLabel","sponsoredLabel","hotLabel","watchLater","favouriteButton","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","title","category","by","duration","enableCommentPinboard"],"sort":"mostSPliked","title_truncation_list":"45","title_truncation_grid":"45","title_truncation_pinboard":"50","description_truncation_list":"50","description_truncation_grid":"45","description_truncation_pinboard":"45","height_list":"230","width_list":"260","height_grid":"270","width_grid":"389","width_pinboard":"300","limit_data_pinboard":"22","limit_data_grid":"22","limit_data_list":"20","pagging":"auto_load","title":"","nomobile":"0","name":"sesvideo.browse-video"}',
  ));
}


//Browse Playlist Page
$widgetOrder = 1;
$select = $db->select()
        ->from('engine4_core_pages')
        ->where('name = ?', 'sesvideo_playlist_browse')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_playlist_browse',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Video Playlist Page',
      'title' => 'Browse Video Playlist Page',
      'description' => 'This is the video playlist page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
  ));
  $topcontainer_id = $db->lastInsertId('engine4_core_content');


  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $topcontainer_id,
      'order' => 6,
  ));
  $topmiddle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
  ));
  $right_id = $db->lastInsertId('engine4_core_content');
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
  ));
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.alphabet-search',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"playlists","nomobile":"0","name":"sesvideo.alphabet-search"}',
  ));
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.browse-playlists',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"creation_date","Type":"0","information":["viewCount","title","description","postedby","share","favourite","favouriteButton","watchLater","favouriteCount","featuredLabel","sponsoredLabel","likeButton","socialSharing","likeCount","showVideosList"],"description_truncation":"60","itemCount":"5","title":"","nomobile":"0","name":"sesvideo.browse-playlists"}',
  ));
  
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.playlist-browse-search',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"searchOptionsType":["searchBox","view","show"],"title":"","nomobile":"0","name":"sesvideo.playlist-browse-search"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.popular-playlists',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showOptionsType":"all","showType":"carouselview","popularity":"favourite_count","information":["postedby","viewCount","favouriteCount","videoCount","songsListShow"],"viewType":"vertical","height":"210","width":"250","limit":"6","title":"Most Favourite Playlists","nomobile":"0","name":"sesvideo.popular-playlists"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.popular-playlists',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showOptionsType":"all","showType":"gridview","popularity":"video_count","information":["postedby","viewCount","favouriteCount","videoCount","songsListShow"],"viewType":"vertical","height":"200","width":"250","limit":"1","title":"Playlists with Max Videos","nomobile":"0","name":"sesvideo.popular-playlists"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.popular-playlists',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showOptionsType":"all","showType":"gridview","popularity":"view_count","information":["postedby","viewCount","favouriteCount","videoCount"],"viewType":"vertical","height":"200","width":"250","limit":"3","title":"Most Viewed Videos","nomobile":"0","name":"sesvideo.popular-playlists"}',
  ));
}


//Artist Browse Page

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_artist_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_artist_browse',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Artists Page',
      'title' => 'Browse Artists',
      'description' => 'This page display lists of artists.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Top Main
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"Type":"0","information":["showfavourite","showrating"],"height":"275","width":"303","itemCount":"9","title":"","nomobile":"0","name":"sesvideo.browse-artists"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.of-the-day',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"ofTheDayType":"artist","show_criteria":["like","comment","favourite","rating","view","title","by","socialSharing","likeButton","favouriteButton","watchLater","videoListShow","duration"],"title_truncation":"45","height":"220","width":"180","title":"Artist Of The Day","nomobile":"0","name":"sesvideo.of-the-day"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"favourite_count","viewType":"gridview","viewTypeStyle":"fixed","height":"220","width":"250","limit":"1","title":"Most favourite Artist","nomobile":"0","name":"sesvideo.popular-artists"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"rating","viewType":"listview","viewTypeStyle":"fixed","height":"160","width":"250","limit":"4","title":"Most Rated Artists","nomobile":"0","name":"sesvideo.popular-artists"}',
  ));
}


//Category Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_category_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_category_browse',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Category Page',
      'title' => 'Browse Category',
      'description' => 'This page lists categories.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));
  $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
  if (is_file($PathFile . "banner" . DIRECTORY_SEPARATOR . 'category.jpg')) {
    if (!file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin')) {
      mkdir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin', 0777, true);
    }
    copy($PathFile . "banner" . DIRECTORY_SEPARATOR . 'category.jpg', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin/category_banner_sesvideo.jpg');
    $category_banner = 'public/admin/category_banner_sesvideo.jpg';
  } else {
    $category_banner = '';
  }
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.banner-category',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"description":"Discover top-notch videos, creators, and collections related to your interests, hand-selected by our 100-percent-human curation team.","sesvideo_categorycover_photo":"' . $category_banner . '","title":"Categories","nomobile":"0","name":"sesvideo.banner-category"}'
  ));
  
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesbasic.simple-html-block',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"bodysimple":"<div style=\"font-size:30px;margin-bottom: 15px;margin:15px\">All Categories<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.video-category',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"height":"130","width":"290","limit":"16","video_required":"0","criteria":"admin_order","show_criteria":["title","icon","countVideos"],"mouse_over_title":"1","title":"","nomobile":"0","name":"sesvideo.video-category"}'
  ));
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.category-associate-video',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"show_criteria":["like","comment","rating","view","title","favourite","by","featuredLabel","sponsoredLabel","hotLabel"],"popularity_video":"rating","pagging":"button","count_video":"0","criteria":"most_video","category_limit":"5","video_limit":"5","seemore_text":"+ See all [category_name]","allignment_seeall":"left","height":"160","width":"250","title":"","nomobile":"0","name":"sesvideo.category-associate-video"}'
  ));
}


//Video Manage Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_manage',
      'displayname' => 'SES - Advanced Videos & Channels - Video Manage Page',
      'title' => 'My Videos',
      'description' => 'This page lists a user\'s videos.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert tabbed content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.tabbed-widget-videomanage',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"enableTabs":["list","grid","pinboard"],"openViewType":"grid","showTabType":"1","show_criteria":["watchLater","favouriteButton","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","location","title","category","by","duration","descriptionlist","descriptiongrid","descriptionpinboard","enableCommentPinboard"],"pagging":"auto_load","limit_data":"9","title_truncation_grid":"45","title_truncation_list":"45","title_truncation_pinboard":"45","description_truncation_list":"120","description_truncation_grid":"45","description_truncation_pinboard":"45","height_grid":"250","width_grid":"319","height_list":"200","width_list":"280","width_pinboard":"300","manage_video_tabbed_option":["videos","likedSPvideos","ratedSPvideos","favouriteSPvideos","featuredSPvideos","sponsoredSPvideos","hotSPvideos","watchSPlaterSPvideos","mySPchannels","followedSPchannels","likedSPchannels","favouriteSPchannels","featuredSPchannels","sponsoredSPchannels","hotSPchannels","mySPplaylists","featuredSPplaylists","sponsoredSPplaylists"],"videos_label":"Videos","videos_order":"1","likedSPvideos_label":"Liked Videos","likedSPvideos_order":"2","ratedSPvideos_label":"Rated Videos","ratedSPvideos_order":"3","favouriteSPvideos_label":"Favourite Videos","favouriteSPvideos_order":"4","featuredSPvideos_label":"Featured Videos","featuredSPvideos_order":"5","sponsoredSPvideos_label":"Sponsored Videos","sponsoredSPvideos_order":"6","hotSPvideos_label":"Hot Videos","hotSPvideos_order":"7","watchSPlaterSPvideos_label":"Watch Later Videos","watchSPlaterSPvideos_order":"8","mySPchannels_label":"My Channels","mySPchannels_order":"9","followedSPchannels_label":"Followed Channels","followedSPchannels_order":"10","likedSPchannels_label":"Liked Channels","likedSPchannels_order":"11","favouriteSPchannels_label":"Favourite Channels","favouriteSPchannels_order":"12","featuredSPchannels_label":"Featured Channels","featuredSPchannels_order":"13","sponsoredSPchannels_label":"Sponsored Channels","sponsoredSPchannels_order":"14","hotSPchannels_label":"Hot Channels","hotSPchannels_order":"15","mySPplaylists_label":"My Playlists","mySPplaylists_order":"16","featuredSPplaylists_label":"Featured Playlists","featuredSPplaylists_order":"17","sponsoredSPplaylists_label":"Sponsored Playlists","sponsoredSPplaylists_order":"18","title":"","nomobile":"0","name":"sesvideo.tabbed-widget-videomanage"}'
  ));
}


//Video Location Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_locations')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_locations',
      'displayname' => 'SES - Advanced Videos & Channels - Video Location Page',
      'title' => 'Video Locations',
      'description' => 'This page show video locations.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"video","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored","verified","hot"],"default_search_type":"recentlySPcreated","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesvideo.browse-search"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.video-location',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"location":"United Kingdom","lat":"56.6465227","lng":"-6.709638499999983","location-data":null,"title":"","nomobile":"0","name":"sesvideo.video-location"}',
  ));
}


//Video Create Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_create')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
 $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_create',
      'displayname' => 'SES - Advanced Videos & Channels - Video Create Page',
      'title' => 'Video Create',
      'description' => 'This page allows video to be added.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));
}


//Video Edit Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_edit')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_edit',
      'displayname' => 'SES - Advanced Videos & Channels - Video Edit Page',
      'title' => 'Video Edit',
      'description' => 'This page allows video to be edited.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));
}

//Browse Tag Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_index_tags')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_tags',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Tags Page',
      'title' => 'Video / Chanel Browse Tags Page',
      'description' => 'This page is the browse video / chanel tag page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 7,
  ));
  $main_right_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.tag-video-chanel',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));
}

//Artist View Page

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_artist_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_artist_view',
      'displayname' => 'SES - Advanced Videos & Channels - Artist View Page',
      'title' => 'View Artist',
      'description' => 'This page displays a artist.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
  ));
  $middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.breadcrumb',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"artist","title":"","nomobile":"0","name":"sesvideo.breadcrumb"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.profile-artist',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"informationArtist":["favouriteCountAr","ratingCountAr","description","ratingStarsAr","addFavouriteButtonAr"],"enableTabs":["list","grid","pinboard"],"viewTypeStyle":"fixed","openViewType":"grid","show_criteria":["watchLater","favouriteButton","playlistAdd","likeButton","socialSharing","like","favourite","comment","location","rating","view","title","category","by","duration","descriptionlist","enableCommentPinboard"],"title_truncation_grid":"24","title_truncation_list":"50","title_truncation_pinboard":"24","description_truncation_list":"120","description_truncation_grid":"45","description_truncation_pinboard":"45","limit_data":"12","pagging":"auto_load","height_grid":"270","width_grid":"293","height_list":"200","width_list":"280","width_pinboard":"300","title":"","nomobile":"0","name":"sesvideo.profile-artist"}',
  ));
}


//Video Playlist View Page
$select = $db->select()
        ->from('engine4_core_pages')
        ->where('name = ?', 'sesvideo_playlist_view')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {

  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_playlist_view',
      'displayname' => 'SES - Advanced Videos & Channels - Video Playlist View Page',
      'title' => 'Video Playlist View Page',
      'description' => 'This is the video playlist view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $topcontainer_id = $db->lastInsertId('engine4_core_content');


  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $topcontainer_id,
      'order' => 6,
      'params' => '',
  ));
  $topmiddle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.playlist-view-page',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
  ));
}


//Channel Photo View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_chanel_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_chanel_view',
      'displayname' => 'SES - Advanced Videos & Channels - Channel Photo View Page',
      'title' => 'Channel Photo View Page',
      'description' => 'This is the channel photo view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2
  ));
  $main_id = $db->lastInsertId();
  // Insert middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6,
  ));
  $middle_id = $db->lastInsertId();
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.chanel-photo-view-page',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"criteria":["like","favourite","tagged","slideshowPhoto"],"maxHeight":"550","view_more_like":"17","view_more_favourite":"10","view_more_tagged":"10","title":"","nomobile":"0","name":"sesvideo.chanel-photo-view-page"}'
  ));
}


//Channel View Page
$select = new Zend_Db_Select($db);
$page_id = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_chanel_index')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_chanel_index',
      'displayname' => 'SES - Advanced Videos & Channels - Channel View Page',
      'title' => 'Channel View Page',
      'description' => 'This is the channel view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $right_id = $db->lastInsertId();
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.breadcrumb',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"chanel","title":"","nomobile":"0","name":"sesvideo.breadcrumb"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-cover',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"photo":"pPhoto","tab":"inside","option":["report","like","delete","edit","favourite","rating","stats","verified"],"title":"","nomobile":"0","name":"sesvideo.chanel-cover"}',
  ));
  
    // middle column
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.container-tabs',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"max":"7"}',
  ));
  $tab_id = $db->lastInsertId('engine4_core_content');

  
  // tabs
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-videos',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"show_criteria":["watchLater","favouriteButton","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","title","category","by","duration","descriptionlist","descriptionpinboard","enableCommentPinboard"],"enableTabs":["list","grid","pinboard"],"openViewType":"grid","limit_data_pinboard":"10","limit_data_grid":"20","limit_data_list":"20","pagging":"button","title_truncation_list":"60","title_truncation_grid":"30","title_truncation_pinboard":"30","description_truncation_list":"240","description_truncation_grid":"45","description_truncation_pinboard":"45","height_grid":"270","width_grid":"305","height_list":"180","width_list":"260","width_pinboard":"305","title":"Videos","nomobile":"0","name":"sesvideo.chanel-videos"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-overview',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Overview"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-info',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Info","name":"sesvideo.chanel-info"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-photos',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Photos","titleCount":true}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.channel-follow-user',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Followers"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.chanel-discussion',
      'parent_content_id' => $tab_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Discussion"}',
  ));

  // right column
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.advance-share',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"advShareOptions":["privateMessage","siteShare","quickShare","addThis"],"title":"","nomobile":"0","name":"sesvideo.advance-share"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.channel-follow',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","name":"sesvideo.channel-follow"}',
  ));
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.people-like-item',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"limit_data":"12","title":"Who Liked This Channel","nomobile":"0","name":"sesvideo.people-like-item"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.people-favourite-item',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"limit_data":"12","title":"Who Favourite This Channel","nomobile":"0","name":"sesvideo.people-favourite-item"}',
  ));
}


//Channel Create Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_chanel_create')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_chanel_create',
      'displayname' => 'SES - Advanced Videos & Channels - Channel Create Page',
      'title' => 'Channel Create',
      'description' => 'This page allows channel to be added.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));
}


//Category View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_category_index')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_category_index',
      'displayname' => 'SES - Advanced Videos & Channels - Category View Page',
      'title' => 'Video Browse',
      'description' => 'This page lists category view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.category-view',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"show_subcat":"1","show_subcatcriteria":["icon","title","countVideo"],"mouse_over_title":"0","heightSubcat":"160","widthSubcat":"290","show_criteria":["featuredLabel","sponsoredLabel","hotLabel","like","comment","rating","favourite","view","title","by"],"pagging":"button","video_limit":"15","height":"240","width":"300","title":"","nomobile":"0","name":"sesvideo.category-view"}'
  ));
}


//Channel category Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_chanel_category')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_chanel_category',
      'displayname' => 'SES - Advanced Videos & Channels - Channel Category Browse Page',
      'title' => 'Video Browse',
      'description' => 'This page lists channel as per category.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'left',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_left_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-chanel-quick',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '',
  ));
      $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.of-the-day',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"ofTheDayType":"chanel","show_criteria":["like","comment","favourite","rating","view","title","by","socialSharing","featuredLabel","sponsoredLabel","hotLabel","likeButton","favouriteButton","watchLater","videoListShow","duration"],"title_truncation":"24","height":"180","width":"180","title":"Channel of the Day","nomobile":"0","name":"sesvideo.of-the-day"}',
  ));
      $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored-carosel',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"chanels","featured_sponsored_carosel":"sponsored","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","videoCount","duration","watchlater","likeButton","favouriteButton"],"duration":"300","bgColor":"","textColor":"","spacing":"","heightMain":"254","height":"200","width":"200","info":"recently_created","title_truncation":"24","limit_data":"6","aliganment_of_widget":"2","title":"Sponsored Channels","nomobile":"0","name":"sesvideo.featured-sponsored-carosel"}',
  ));
      $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"chanel","type":"grid","criteria":"5","info":"most_favourite","show_criteria":["like","comment","rating","favourite","view","title","by","category","duration","watchLater","socialSharing","likeButton","favouriteButton"],"title_truncation":"24","height":"180","width":"180","limit_data":"3","title":"Most Favourite Channels","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
      $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"chanel","type":"grid","criteria":"5","info":"most_rated","show_criteria":["like","comment","rating","favourite","view","title","by","category","duration","watchLater","socialSharing","likeButton","favouriteButton"],"title_truncation":"24","height":"180","width":"180","limit_data":"3","title":"Top Rated Channels","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.alphabet-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"chanels","nomobile":"0","name":"sesvideo.alphabet-search"}',
  ));

    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"chanel","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored","verified","hot"],"default_search_type":"recentlySPcreated","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesvideo.browse-search"}',
  ));
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-category-chanel',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '[""]',
  ));
}


//Channel Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesvideo_chanel_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_chanel_browse',
      'displayname' => 'SES - Advanced Videos & Channels - Browse Channel Page',
      'title' => 'Video Browse',
      'description' => 'This page lists videos.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'left',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_left_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));
  
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-chanel-quick',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.of-the-day',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"ofTheDayType":"chanel","show_criteria":["like","comment","favourite","rating","view","title","by","socialSharing","featuredLabel","sponsoredLabel","hotLabel","likeButton","favouriteButton","watchLater","videoListShow","duration"],"title_truncation":"24","height":"180","width":"180","title":"Channel of the Day","nomobile":"0","name":"sesvideo.of-the-day"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored-carosel',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"chanels","featured_sponsored_carosel":"sponsored","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","videoCount","duration","watchlater","likeButton","favouriteButton"],"duration":"300","bgColor":"","textColor":"","spacing":"","heightMain":"254","height":"200","width":"200","info":"most_liked","title_truncation":"24","limit_data":"6","aliganment_of_widget":"2","title":"Sponsored Channels","nomobile":"0","name":"sesvideo.featured-sponsored-carosel"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"chanel","type":"grid","criteria":"5","info":"recently_created","show_criteria":["like","comment","rating","favourite","view","title","by","category","duration","watchLater","socialSharing","likeButton","favouriteButton"],"title_truncation":"24","height":"180","width":"180","limit_data":"3","title":"Most Favourite Channels","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => $widgetOrder++,
      'params' => '{"tableName":"chanel","type":"grid","criteria":"5","info":"most_rated","show_criteria":["like","comment","rating","favourite","view","title","by","category","duration","watchLater","socialSharing","likeButton","favouriteButton"],"title_truncation":"24","height":"180","width":"180","limit_data":"3","title":"Top Rated Channels","nomobile":"0","name":"sesvideo.featured-sponsored"}',
  ));
  
  
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.alphabet-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"chanels","nomobile":"0","name":"sesvideo.alphabet-search"}'
  ));
  
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"search_for":"chanel","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored","verified","hot"],"default_search_type":"recentlySPcreated","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesvideo.browse-search"}'
  ));
    $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesvideo.browse-chanel',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"show_criteria":["description","follow","followButton","favouriteButton","likeButton","verified","rating","socialeShare","like","comment","photo","view","title","favourite","by","chanelPhoto","chanelVideo","chanelThumbnail","videoCount","watchLater"],"pagging":"button","count_chanel":"1","criteria":"most_chanel","category_limit":"7","chanel_limit":"10","video_limit":"10","seemore_text":"+ See all [category_name]","allignment_seeall":"left","title_truncation":"45","description_truncation":"210","height":"80","width":"120","title":"","nomobile":"0","name":"sesvideo.browse-chanel"}'
  ));

}


//Member Profile Page
$select = new Zend_Db_Select($db);
$select->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
$select = new Zend_Db_Select($db);
$select->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'sesvideo.profile-videos');
$info = $select->query()->fetch();
if (empty($info)) {

  // container_id (will always be there)
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'container')
          ->limit(1);
  $container_id = $select->query()->fetchObject()->content_id;

  // middle_id (will always be there)
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content')
          ->where('parent_content_id = ?', $container_id)
          ->where('type = ?', 'container')
          ->where('name = ?', 'middle')
          ->limit(1);
  $middle_id = $select->query()->fetchObject()->content_id;

  // tab_id (tab container) may not always be there
  $select
          ->reset('where')
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->where('page_id = ?', $page_id)
          ->limit(1);
  $tab_id = $select->query()->fetchObject();
  if ($tab_id && @$tab_id->content_id) {
    $tab_id = $tab_id->content_id;
  } else {
    $tab_id = null;
  }

  // tab on profile
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.profile-videos',
      'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
      'order' => 12,
      'params' => '{"title":"Videos","titleCount":true}',
  ));
}


//Video View Page
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sesvideo_index_view')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {

  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesvideo_index_view',
      'displayname' => 'SES - Advanced Videos & Channels - Video View Page',
      'title' => 'View Video',
      'description' => 'This is the view page for a video.',
      'custom' => 0,
      'provides' => 'subject=video',
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  // containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 1,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 3,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // middle column content
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.breadcrumb',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"video","title":"","nomobile":"0","name":"sesvideo.breadcrumb"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.video-view-page',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"advSearchOptions":["likeCount","viewCount","commentCount","favouriteButton","addToPlaylist","watchLater","favouriteCount","rateCount","openVideoLightbox","editVideo","deleteVideo","shareAdvance","reportVideo","peopleLike","favourite","comment","artist"],"autoplay":"0","likelimit_data":"11","favouritelimit_data":"11","advShareOptions":["privateMessage","siteShare","quickShare","addThis","embed"],"title":"","nomobile":"0","name":"sesvideo.video-view-page"}',
  ));

  // right column
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.show-same-tags',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"0":"","title":"Similar Videos"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.show-also-liked',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"0":"","title":"People Also Liked"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesvideo.show-same-poster',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"0":"","title":"From the same member"}',
  ));
}


//Check ffmpeg path for correctness
$select = new Zend_Db_Select($db);
if (function_exists('exec') && function_exists('shell_exec')) {
  // Api is not available
  //$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
  $ffmpeg_path = $db->select()
          ->from('engine4_core_settings', 'value')
          ->where('name = ?', 'sesvideo.ffmpeg.path')
          ->limit(1)
          ->query()
          ->fetchColumn(0);
  $output = null;
  $return = null;
  if (!empty($ffmpeg_path)) {
    exec($ffmpeg_path . ' -version', $output, $return);
  }
  // Try to auto-guess ffmpeg path if it is not set correctly
  $ffmpeg_path_original = $ffmpeg_path;
  if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
    $ffmpeg_path = null;
    // Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      // @todo
    }
    // Not windows
    else {
      $output = null;
      $return = null;
      @exec('which ffmpeg', $output, $return);
      if (0 == $return) {
        $ffmpeg_path = array_shift($output);
        $output = null;
        $return = null;
        exec($ffmpeg_path . ' -version', $output, $return);
        if (0 == $return) {
          $ffmpeg_path = null;
        }
      }
    }
  }
  if ($ffmpeg_path != $ffmpeg_path_original) {
    $count = $db->update('engine4_core_settings', array(
        'value' => $ffmpeg_path,
            ), array(
        'name = ?' => 'sesvideo.ffmpeg.path',
    ));
    if ($count === 0) {
      try {
        $db->insert('engine4_core_settings', array(
            'value' => $ffmpeg_path,
            'name' => 'sesvideo.ffmpeg.path',
        ));
      } catch (Exception $e) {
        
      }
    }
  }
}

$catgoryData = array('0' => array('Cooking & Health', 'cooking.jpg', 'cooking.png',''),array('Travel & Events', 'travel.jpg', 'travel.png',''),array('Sports', 'sport.jpg', 'sport.png',''),array('Science & Technology', 'technology.jpg', 'technology.png',''),array('People & Blogs', 'people.jpg', 'people.png',''),array('Nonprofits & Activism', 'nonprofits-activism.jpg', 'nonprofits-activism.png',' '),array('News & Politics', 'news.jpg', 'news.png',''),array('Music', 'music.jpg', 'music.png',''),array('Howto & Style', 'howto.jpg', 'howto.png',''),array('Gaming', 'gaming.jpg', 'gaming.png',''),array('Film & Animation', 'animation.jpg', 'film.png',''),array('Entertainment', 'entertainment.jpg', 'entertaintment.png',''),array('Education', 'education.jpg', 'education.png',''),array('Comedy', 'comedy.jpg', 'comedy.png',''),array('Autos & Vehicles', 'auto.jpg', 'auto.png',''),array('Pets & Animals','pets.jpg','pets.png',''));
$CookingHealth = array('0'=>array('Baking','baking.jpg','baking.png',''),array('Drinks','drinks.jpg','drink.png',''),array('Cooking','cooking.jpg','cooking.png',''),array('Grilling','grilling.jpg','grilling.png',''));
$Baking = array('0'=>array('Cake','cake.jpg','cake.png',''));
$Sports = array('0'=>array('Bikes','bikes.jpg','bike.png',''),array('Outdoor Sports','outdoor-sport.jpg','outdoor-sport.png',''),array('Skate','skate.jpg','skate.png',''),array('Sky','sky.jpg','sky.png',''));
$HowtoStyle = array('0'=>array('Fashion Film','how-to-style.jpg',''));
$FilmAnimation = array('0'=>array('2D','2d.jpg','2d.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('3D/CG','3d.jpg','3d.png','.'),array('Projection Mapping','projection-mapping.jpg','projection-mapping.png',''));
$Comedy = array('0'=>array('Stand Up','stand-up.jpg','stand-up.png',''));
 $table_exist_categories = $db->query('SHOW TABLES LIKE \'engine4_video_categories\'')->fetch();
      if (empty($table_exist_categories)) {
        $db->query('CREATE TABLE IF NOT EXISTS `engine4_video_categories` (
          `category_id` int(11) unsigned NOT NULL auto_increment,
          `slug` varchar(255) NOT NULL,
          `category_name` varchar(128) NOT NULL,
          `subcat_id` int(11)  NULL DEFAULT 0,
          `subsubcat_id` int(11)  NULL DEFAULT 0,
          `title` varchar(255) DEFAULT NULL,
          `description` text,
          `thumbnail` int(11) NOT NULL DEFAULT 0,
          `cat_icon` int(11) NOT NULL DEFAULT 0,
          `order` int(11) NOT NULL DEFAULT 0,
          `profile_type` int(11) DEFAULT NULL,
          PRIMARY KEY (`category_id`),
          KEY `category_id` (`category_id`,`category_name`),
          KEY `category_name` (`category_name`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1');
        foreach ($catgoryData as $key => $value) {
          //Upload categories icon
          $db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "',0,0,'','".$value[3]."')");
          $catId = $db->lastInsertId();
          $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
          if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
            $cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $catId);
          else
            $cat_icon = 0;
          if (is_file($PathFile . "banner" . DIRECTORY_SEPARATOR . $value[1]))
            $thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner" . DIRECTORY_SEPARATOR . $value[1], $catId, true);
          else
            $thumbnail_icon = 0;
          $db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $catId);
					$valueName = str_replace(array(' ','&','/'),array('','',''),$value[0]);
					if(isset(${$valueName})){
						foreach(${$valueName} as $value){
							$db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','".$catId."',0,'','')");
							$subId = $db->lastInsertId();
							$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
							if (is_file($PathFile . "icons".DIRECTORY_SEPARATOR.'sub-categories'   . DIRECTORY_SEPARATOR . $value[2]))
								$cat_icon = $this->setCategoryPhoto($PathFile . "icons".DIRECTORY_SEPARATOR.'sub-categories'  . DIRECTORY_SEPARATOR . $value[2], $subId);
							else
								$cat_icon = 0;
							if (is_file($PathFile . "banner".DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1]))
								$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner".DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1], $subId, true);
							else
								$thumbnail_icon = 0;
							$db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subId);
							$valueSubName = str_replace(array(' ','&','/'),array('','',''),$value[0]);
							if(isset(${$valueSubName})){
								foreach(${$valueSubName} as $value){
									$db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','0','".$catId."','','".$value[3]."')");
									$subsubId = $db->lastInsertId();
									$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
									if (is_file($PathFile . "icons" .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2]))
										$cat_icon = $this->setCategoryPhoto($PathFile . "icons" .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2], $subsubId);
									else
										$cat_icon = 0;
									if (is_file($PathFile . "banner" .DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1]))
										$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner" .DIRECTORY_SEPARATOR.'sub-category' .  DIRECTORY_SEPARATOR . $value[1], $subsubId, true);
									else
										$thumbnail_icon = 0;
									$db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subsubId);
								}
							}
						}
					}	
          $runInstallCategory = true;
        }
      }
		 $table_exist_categories = $db->query('SHOW TABLES LIKE \'engine4_video_categories\'')->fetch();
      if (!empty($table_exist_categories)) {
        $description = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'description\'')->fetch();
        if (empty($description)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `description` text ;");
        }
        $order = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'order\'')->fetch();
        if (empty($order)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `order` INT(11) NOT NULL DEFAULT 0 ;");
        }
        $title = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'title\'')->fetch();
        if (empty($title)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `title` VARCHAR( 255 ) NOT NULL ;");
        }
        $slug = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'slug\'')->fetch();
        if (empty($slug)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `slug` VARCHAR( 255 ) NOT NULL ;");
        }
        $subcat_id = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'subcat_id\'')->fetch();
        if (empty($subcat_id)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `subcat_id` INT( 11 )  NULL DEFAULT '0';");
        }
        $subsubcat_id = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'subsubcat_id\'')->fetch();
        if (empty($subsubcat_id)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `subsubcat_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
        $category_id = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'category_id\'')->fetch();
        if (empty($category_id)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `category_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
        $thumbnail = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'thumbnail\'')->fetch();
        if (empty($thumbnail)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `thumbnail` INT( 11 ) NOT NULL DEFAULT 0 ;");
        }
        $cat_icon = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'cat_icon\'')->fetch();
        if (empty($cat_icon)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `cat_icon` INT( 11 ) NOT NULL DEFAULT 0 ;");
        }
        $profile_type = $db->query('SHOW COLUMNS FROM engine4_video_categories LIKE \'profile_type\'')->fetch();
        if (empty($profile_type)) {
          $db->query("ALTER TABLE `engine4_video_categories` ADD `profile_type` INT( 11 ) NULL ;");
        }
        $db->query("UPDATE `engine4_video_categories` set `title` = category_name where title = ''");
        $db->query("UPDATE `engine4_video_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'),'/','-')) where slug = ''");
        $db->query("UPDATE `engine4_video_categories` SET `order` = `category_id` WHERE `order` = 0");
      }
      if (empty($runInstallCategory)) {
        foreach ($catgoryData as $key => $value) {
          //Upload categories icon
          $catId = $db->query("SELECT category_id,thumbnail,cat_icon FROM `engine4_video_categories` WHERE category_name = '" . $value[0] . "'")->fetchAll();
          if (empty($catId[0]['category_id'])){
						$db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "',0,0,'','')");
          	$catId = $db->lastInsertId();
					}else if(empty($catId[0]['thumbnail']) && empty($catId[0]['cat_icon']) && ($catId[0]['thumbnail'] == 0) && ($catId[0]['cat_icon'] == 0)){
						$catId = $catId[0]['category_id'];
					}else
						continue;
          $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
          if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
            $cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $catId);
          else
            $cat_icon = 0;
          if (is_file($PathFile . "banner" . DIRECTORY_SEPARATOR . $value[1]))
            $thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner" . DIRECTORY_SEPARATOR . $value[1], $catId, true);
          else
            $thumbnail_icon = 0;
          $db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $catId);
					$valueName = str_replace(array(' ','&','/'),array('','',''),$value[0]);
					if(isset(${$valueName})){
						foreach(${$valueName} as $value){
							$db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','".$catId."',0,'','')");
							$subId = $db->lastInsertId();
							$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
							if (is_file($PathFile . "icons"  .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2]))
								$cat_icon = $this->setCategoryPhoto($PathFile . "icons"  .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2], $subId);
							else
								$cat_icon = 0;
							if (is_file($PathFile . "banner" .DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1]))
								$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner" .DIRECTORY_SEPARATOR.'sub-category' .DIRECTORY_SEPARATOR . $value[1], $subId, true);
							else
								$thumbnail_icon = 0;
							$db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subId);
							$valueSubName = str_replace(array(' ','&','/'),array('','',''),$value[0]);
							if(isset(${$valueSubName})){
								foreach(${$valueSubName} as $value){
									$db->query("INSERT IGNORE INTO `engine4_video_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','0','".$catId."','','".$value[3]."')");
									$subsubId = $db->lastInsertId();
									$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
									if (is_file($PathFile . "icons" .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2]))
										$cat_icon = $this->setCategoryPhoto($PathFile . "icons"  .DIRECTORY_SEPARATOR.'sub-categories' . DIRECTORY_SEPARATOR . $value[2], $subsubId);
									else
										$cat_icon = 0;
									if (is_file($PathFile . "banner"  .DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1]))
										$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banner"  . DIRECTORY_SEPARATOR.'sub-category' . DIRECTORY_SEPARATOR . $value[1], $subsubId, true);
									else
										$thumbnail_icon = 0;
									$db->query("UPDATE `engine4_video_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subsubId);
									$db->query("UPDATE `engine4_video_categories` set `title` = category_name WHERE category_id = ".$subsubId );
									$db->query("UPDATE `engine4_video_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'),'/','-'))  WHERE category_id = ".$subsubId);
									$db->query("UPDATE `engine4_video_categories` SET `order` = `category_id`  WHERE category_id = ".$subsubId);
								}
							}
							$db->query("UPDATE `engine4_video_categories` set `title` = category_name WHERE category_id = ".$subId );
							$db->query("UPDATE `engine4_video_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'),'/','-'))  WHERE category_id = ".$subId);
							$db->query("UPDATE `engine4_video_categories` SET `order` = `category_id`  WHERE category_id = ".$subId);
						}
					}
				$db->query("UPDATE `engine4_video_categories` set `title` = category_name WHERE category_id = ".$catId );
        $db->query("UPDATE `engine4_video_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'),'/','-'))  WHERE category_id = ".$catId);
        $db->query("UPDATE `engine4_video_categories` SET `order` = `category_id`  WHERE category_id = ".$catId);
        }
      }


//update query
if(Engine_Api::_()->getDbtable('modules', 'core')->hasModule('video')){
$db->query("UPDATE engine4_core_jobtypes SET plugin = 'Sesvideo_Plugin_Job_Encode',title = 'Advanced Videos & Channels Plugin Video Encode',module='sesvideo' WHERE plugin = 'video_Plugin_Job_Encode'");

$db->query("UPDATE engine4_core_jobtypes SET plugin = 'Sesvideo_Plugin_Job_Maintenance_RebuildPrivacy' ,title = 'Advanced Videos & Channels Plugin Rebuild Video Privacy',module='sesvideo' WHERE plugin = 'video_Plugin_Job_Maintenance_RebuildPrivacy'");
} else {
  $db->query("INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `enabled`, `multi`, `priority`) VALUES('Advanced Videos & Channels Plugin Video Encode', 'video_encode', 'sesvideo', 'Sesvideo_Plugin_Job_Encode', 1, 2, 75),('Advanced Videos & Channels Plugin Rebuild Video Privacy', 'video_maintenance_rebuild_privacy', 'sesvideo', 'Sesvideo_Plugin_Job_Maintenance_RebuildPrivacy', 1, 1, 50)");
}

$this->defaultArtists();

//Default Artist Work
$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesvideo' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "artist_photos" . DIRECTORY_SEPARATOR;
$tableManageActionsTable = Engine_Api::_()->getDbtable('artists', 'sesvideo');
$select = $tableManageActionsTable->select()->from($tableManageActionsTable->info('name'), array('artist_id'))->where('artist_photo = ?', 0);
$results = $tableManageActionsTable->fetchAll($select);
foreach ($results as $result) {
  $fileName = $result->artist_id . '.jpg';
  $fileId = $this->setPhoto($PathFile . $fileName, $result->artist_id);
  $db->query("UPDATE `engine4_sesvideo_artists` SET `artist_photo` = '" . $fileId->file_id . "' WHERE artist_id = " . $result->artist_id);
}

$table_exist_video = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
if (!empty($table_exist_video)) {
	$importthumbnail = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'importthumbnail\'')->fetch();
	if (empty($importthumbnail)) {
		$db->query('ALTER TABLE  `engine4_video_videos` ADD  `importthumbnail` TINYINT( 1 ) NOT NULL DEFAULT "0";');
	}
}

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesvideo_admin_main_imoprtthumbnails", "sesvideo", "Import Thumbnails", "", \'{"route":"admin_default","module":"sesvideo","controller":"settings", "action":"import-thumbnails"}\', "sesvideo_admin_main", "", 15);');

$table_exist_video = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
if (!empty($table_exist_video)) {
	$approve = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'approve\'')->fetch();
	if (empty($approve)) {
		$db->query('ALTER TABLE `engine4_video_videos` ADD `approve` TINYINT(1) NOT NULL DEFAULT "1";');
	}
}

$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_video_approve', 'sesvideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_video_disapprove', 'sesvideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo], [sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo], [object_description]');");

$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
	  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_upload_option" as `name`,
    5 as `value`,
    \'["youtube","youtubePlaylist","vimeo","dailymotion","url","embedcode"]\' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
  
$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_approve" as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("user");');

$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "video" as `type`,
    "video_approve" as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("public");');

