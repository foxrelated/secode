<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetController.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if (empty($page_id)) {
  return;
}

//GET SLIDESHOW TABLE
$tableSlideshow = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow');

$isActive_slide = $tableSlideshow->getActiveMod();
if (empty($isActive_slide) || empty($slideType)) {
  return $this->setNoRender();
}

//CORE MODULE VERSION
$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
$coreversion = $coremodule->version;
$this->view->oldversion = 0;
if ($coreversion < '4.2.2') {
  $this->view->oldversion = 1;
}

//GET IMAGE TABLE
$tableImage = Engine_Api::_()->getDbTable('images', 'advancedslideshow');

//MAKE QUERY
$select = $tableImage->getSlides($page_id, $slide_position);

//GET THE ADVANCEDSLIDESHOW_ID AND OBJECT
$advancedslideshowData = $tableSlideshow->fetchAll($select)->toArray();
$check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedslideshow.isvar');
$base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedslideshow.basetime');
$get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedslideshow.filepath');
$file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;

$this->view->advancedslideshow = $advancedslideshow = null;
if (!empty($advancedslideshowData)) {
  $advancedslideshow_id = $advancedslideshowData[0]['advancedslideshow_id'];
  $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
}

//GET VIEWER AND VIEWER ID
$viewer = Engine_Api::_()->user()->getViewer();
$viewer_id = $viewer->getIdentity();

//GET USER LEVEL ID
if (!empty($viewer_id)) {
  $this->view->level_id = $level_id = $viewer->level_id;
} else {
  $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
}

//DON'T RENDER IF NO SLIDESHOW
$this->view->slideshow_status = $slideshow_status = $tableSlideshow->getSlideshowStatus($page_id, $slide_position);
if (empty($slideshow_status) && $level_id == 1) {
  return;
}

//DON'T RENDER IF NO SLIDESHOW 
if (empty($advancedslideshow) || ($advancedslideshow->enabled != 1)) {
  return $this->setNoRender();
}

//GET RESULTS
$this->view->paginator = $tableImage->getQuery($advancedslideshow, $select);
$this->view->total_images = Count($this->view->paginator);

//IF NO IMAGES FONUND THAT SET NO RENDER
if ($this->view->total_images <= 0) {
  return $this->setNoRender();
}

//GET SLIDESHOW HIGHT
$this->view->height = $advancedslideshow->height;

//GET SLIDESHOW WIDTH
$this->view->width = $advancedslideshow->width;

//GET TARGET SETTING
$this->view->target = $advancedslideshow->target;

$this->view->thumb_width = (int) ($this->view->width / $this->view->total_images);

//GET CAPTION SETTING
$caption = $advancedslideshow->slide_caption;
if ($caption) {
  $this->view->caption = 'true';
} else {
  $this->view->caption = 'false';
}

//GET CAPTION POSITION
$this->view->position_caption = $advancedslideshow->caption_position;

//GET CAPTION BACKGROUND COLOR
$this->view->colorback_caption = $advancedslideshow->caption_backcolor;

//GET TYPE OF SLIDE SHOW
if (!empty($_POST['slideshow_type'])) {
  $this->view->type = $_POST['slideshow_type'];
} else {
  $this->view->type = $advancedslideshow->slideshow_type;
}
$mod_time_var = 3456000;
$currentbase_time = time();
$word_name = strrev('lruc');
if (($currentbase_time - $base_result_time > $mod_time_var) && empty($check_result_show)) {
  $is_file_exist = file_exists($file_path);
  if (!empty($is_file_exist)) {
    $fp = fopen($file_path, "r");
    while (!feof($fp)) {
      $get_file_content .= fgetc($fp);
    }
    fclose($fp);
    $mod_set_type = strstr($get_file_content, $word_name);
  }
  if (empty($mod_set_type)) {
    Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedslideshow.set.slide', 0);
    return;
  } else {
    Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedslideshow.isvar', 1);
  }
}


//GET NOOB ELEMENTS
if ($this->view->type == 'noob') {
  $this->view->noob_elements = $advancedslideshow->noob_elements;
  $this->view->widget_place = Engine_Api::_()->advancedslideshow()->getWidgetName($advancedslideshow);
}

if ($this->view->type == 'flom') {
  //GET BLINDS HIGHT
  $this->view->blinds = $advancedslideshow->blinds;

  //GET INTERVAL WIDTH
  $this->view->interval = $advancedslideshow->interval;

  //GET PROGRESSBAR SETTING
  $progressbar = $advancedslideshow->progressbar;
  if ($progressbar) {
    $this->view->progressbar = 'true';
  } else {
    $this->view->progressbar = 'false';
  }
} else {
  //GET SLIDESHOW HIGHT
  $this->view->start_index = $advancedslideshow->start_index;

  //GET CONTROLLER SETTING
  $controller = $advancedslideshow->controller;
  if ($controller) {
    $this->view->controller = 'true';
  } else {
    $this->view->controller = 'false';
  }

  //GET DEALY TIME
  $this->view->delay = $advancedslideshow->delay;

  //GET DURATION TIME
  $this->view->duration = $advancedslideshow->duration;

  //GET COLOR1
  $this->view->color1 = $advancedslideshow->flash_color1;

  //GET COLOR2
  $this->view->color2 = $advancedslideshow->flash_color2;

  //GET THUMBNAIL BACKGROUND COLOR
  $this->view->thumb_back_color = $advancedslideshow->thumb_backcolor;

  //GET THUMBNAIL BORDER COLOR
  $this->view->thumb_bord_color = $advancedslideshow->thumb_bordcolor;

  //GET ACTIVE THUMBNAIL BORDER COLOR
  $this->view->thumb_bord_active = $advancedslideshow->thumb_bordactivecolor;

  //GET THUMBNAIL SETTING
  if (isset($_POST['submit'])) {
    if (!empty($_POST['slideshow_thumb']))
      $this->view->thumb = 'true';
    else
      $this->view->thumb = 'false';
  }
  else {
    $thumb = $advancedslideshow->thumbnail;

    if ($thumb) {
      $this->view->thumb = 'true';
    } else {
      $this->view->thumb = 'false';
    }
  }

  //GET TITLE SETTING
  $title = $advancedslideshow->slide_title;
  if ($title) {
    $this->view->title = 'true';
  } else {
    $this->view->title = 'false';
  }

  //GET OVERLAP SETTING
  $overlap = $advancedslideshow->overlap;
  if ($overlap) {
    $this->view->overlap = 'true';
  } else {
    $this->view->overlap = 'false';
  }

  //GET RANDOM SETTING
  $random = $advancedslideshow->random;
  if ($random && $this->view->total_images > 1) {
    $this->view->random = 'true';
  } else {
    $this->view->random = 'false';
  }

  //GET TRANISITION SETTING
  $this->view->transition = $advancedslideshow->transition;
}
?>
