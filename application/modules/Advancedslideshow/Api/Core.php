<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Api_Core extends Core_Api_Abstract {
  const THUMB_WIDTH = 80;
  const THUMB_HEIGHT = 51;

  /**
   * Create a slide
   *
   * @param array params
   * @param array file
   * @return created image object
   */
  public function createImage($params, $file) {
    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      //GET IMAGE INFO AND RESIZE
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path . '/m_' . $name . '.' . $extension;
      $thumbName = $path . '/t_' . $name . '.' . $extension;

      //GET SLIDESHOW OBJECT
      $advancedslideshow_id = $params['advancedslideshow_id'];
      $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

      //GET SLIDESHOW HEIGHT
      $height = $advancedslideshow->height;

      //GET SLIDESHOW WIDTH
      $width = $advancedslideshow->width;

      if($advancedslideshow->slide_resize) {
        $image = Engine_Image::factory();
        $image->open($file['tmp_name'])
                ->resize($width, $height)
                ->write($mainName)
                ->destroy();

        $image = Engine_Image::factory();
        $image->open($file['tmp_name'])
                //->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->resize($width, $height)
                ->write($thumbName)
                ->destroy();          
      }
      else {
        $image = Engine_Image::factory();
        $image->open($file['tmp_name'])
               //->resize($width, $height)
               ->write($mainName)
               ->destroy();

        $image = Engine_Image::factory();
        $image->open($file['tmp_name'])
               //->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
               ->write($thumbName)
               ->destroy();         
      }


      $image_params = array(
          'parent_id' => 5,
          'parent_type' => 'advancedslideshow',
      );

      $imageFile = Engine_Api::_()->storage()->create($mainName, $image_params);
      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $image_params);

      $imageFile->bridge($thumbFile, 'thumb.normal');

      $params['file_id'] = $imageFile->file_id;
      //$params['image_id'] = $imageFile->file_id;
    }

    $row = Engine_Api::_()->getDbtable('images', 'advancedslideshow')->createRow();
    $row->setFromArray($params);
    //$row->extension = $extension;
    $row->save();

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach ($levels as $level) {
      $level_id = $level->getIdentity();
      $levels_prepared[] = strval($level_id);
    }

    // prepare networks
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();

    if (count($networks) > 0) {
      foreach ($networks as $network) {
        $network_id = $network->getIdentity();
        $networks_prepared[] = strval($network_id);
      }
    }

    $networks_prepared = Zend_Json_Encoder::encode($networks_prepared);
    $levels_prepared = Zend_Json_Encoder::encode($levels_prepared);

    $row->network = $networks_prepared;
    $row->level = $levels_prepared;
    $row->show_public = 1;

    $row->save();

    return $row;
  }

  /**
   * Delete a directory recurively
   *
   * @param string dir
   */
  public function deleteDirectory($dir) {
    @chmod($dir, 0777);
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir") {
            Engine_Api::_()->advancedslideshow()->deleteDirectory($dir . "/" . $object);
          } else {
            @unlink($dir . "/" . $object);
          }
        }
      }
      reset($objects);
      @rmdir($dir);
    }
  }

  /**
   * Delete slideshow's belongings
   *
   * @param int advancedslideshow_id
   */
  public function deleteSlideshow($advancedslideshow_id) {
    $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    //DELETE IMAGES
    $rows = Engine_Api::_()->getItemTable('advancedslideshow_image')->getImages($advancedslideshow_id);
    foreach ($rows as $key => $image_ids) {
      $image_id = $image_ids->image_id;
      $image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);
      //$image_id = $image->image_id;
      //$extension = $image->extension;
      $image->delete();
      //unlink(APPLICATION_PATH . "/public/advancedslideshow/1000000/1000/5/" . $image_id . 't.' . $extension);
    }

    //FINALLY DELETE SLIDESHOW AND WIDGET
    if (!empty($advancedslideshow)) {

      //DELETE WIDGET FROM 'enging4_core_content'
      if (!empty($advancedslideshow->widget_content_id)) {
        Engine_Api::_()->getDbtable('content', 'core')->delete(array('content_id =?' => $advancedslideshow->widget_content_id));
      }

      $advancedslideshow->delete();
    }
  }

  /**
   * Get page id
   *
   * @param int content_id
   * @return page id
   */
	public function getPageId($content_id) {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $contentTableName = $contentTable->info('name');
    $page_id = 0;
    $page_id = $contentTable->select()
            ->from($contentTableName, array('page_id'))
            ->where('content_id = ?', $content_id)
            ->query()
            ->fetchColumn();    

    if($page_id != 1 && $page_id != 2 && (($module == 'sitepage' && $coreSettings->getSetting('sitepage.layoutcreate', 0)) || ($module == 'sitebusiness' && $coreSettings->getSetting('sitebusiness.layoutcreate', 0)) || ($module == 'sitegroup' && $coreSettings->getSetting('sitegroup.layoutcreate', 0)) || ($module == 'sitestore' && $coreSettings->getSetting('sitestore.layoutcreate', 0))) && ($controller == 'index') && ($action == 'view')) {
        $corePageTable = Engine_Api::_()->getDbTable('pages', 'core');
        $corePageTableName = $corePageTable->info('name');
        $page_id = 0;
        $page_id = $corePageTable->select()
                        ->from($corePageTableName, array('page_id'))
                        ->where('name = ?', "$module".'_index_view')
                        ->query()
                        ->fetchColumn();
        return $page_id;        
    }	    
		

    return $page_id;
  }

  public function getParamsUrl($params) {

    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct'))
      return '';

    $url = '';
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $params = Zend_Json::decode($params);
    if (array_key_exists('subsubcategory_id', $params) && !empty($params['subsubcategory_id'])) {
      $url = $view->url(array('category_id' => $params['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['category_id'])->getCategorySlug(), 'subcategory_id' => $params['subcategory_id'], 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['subcategory_id'])->getCategorySlug(), 'subsubcategory_id' => $params['subsubcategory_id'], 'subsubcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['subsubcategory_id'])->getCategorySlug()), "sitestoreproduct_general_subsubcategory");
    } elseif (array_key_exists('subcategory_id', $params) && !empty($params['subcategory_id'])) {
      $url = $view->url(array('category_id' => $params['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['category_id'])->getCategorySlug(), 'subcategory_id' => $params['subcategory_id'], 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['subcategory_id'])->getCategorySlug()), "sitestoreproduct_general_subcategory");
    } elseif (array_key_exists('category_id', $params) && !empty($params['category_id'])) {
      $url = $view->url(array('category_id' => $params['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $params['category_id'])->getCategorySlug()), "sitestoreproduct_general_category");
    }

    return $url;
  }

  /**
   * Get Noob Slides Array
   *
   * @param int $advancedslideshow_id: Slideshow id
   * @return Array
   */
  public function getNoobSlidesArray($advancedslideshow) {
    $table = Engine_Api::_()->getDbTable('images', 'advancedslideshow');
    $tableName = $table->info('name');
    $select = $table->select()
            ->from($tableName, array('image_id', 'slide_html', 'caption', 'url', 'file_id', 'advancedslideshow_id'))
            ->where('enabled =?', 1)
            ->where('advancedslideshow_id =?', $advancedslideshow->advancedslideshow_id);

    if (!empty($advancedslideshow->random))
      $select->order('RAND()');else $select->order('order');

    $sqlObj = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getQuery($advancedslideshow, $select);

    // $sqlObj = $select->query()->fetchAll();

    $slidesArray = array();
    foreach ($sqlObj as $item) {
        
      if (!empty($item->slide_html)) {
        $tempArray = @unserialize($item->slide_html);
        $slidesArray[] = array("thumb_id" => $tempArray['thumb_id'], "slide_html" => $tempArray['slide_html'], "caption" => $item->caption);
      } else {
                      
        $slidesArray[] = array("image_id" => $item->image_id, "caption" => $item->caption, "url" => $item->url, 'mainImage' => $item->getPhotoUrl(), 'thumbImage' => $item->getPhotoUrl('thumb.normal'));      
      }
      
    }

    return $slidesArray;
  }

  public function getWidgetName($advancedslideshow, $getWidgetPlace = true) {
    $widget_position = $advancedslideshow->widget_position;
    if ($widget_position == 'left_column1' || $widget_position == 'left_column2') {
      if ($widget_position == 'left_column1') {
        $widget_place = 'left1';
      } elseif ($widget_position == 'left_column2') {
        $widget_place = 'left2';
      }
    } elseif ($widget_position == 'right_column1' || $widget_position == 'right_column2') {
      $widget_place = 'left';
      if ($widget_position == 'right_column1') {
        $widget_place = 'right1';
      } elseif ($widget_position == 'right_column2') {
        $widget_place = 'right2';
      }
    } elseif ($widget_position == 'middle_column1' || $widget_position == 'middle_column2') {
      if ($widget_position == 'middle_column1') {
        $widget_place = 'middle1';
      } elseif ($widget_position == 'middle_column2') {
        $widget_place = 'middle2';
      }
    } elseif ($widget_position == 'extreme1' || $widget_position == 'extreme2') {
      if ($widget_position == 'extreme1') {
        $widget_place = 'extended1';
      } elseif ($widget_position == 'extreme2') {
        $widget_place = 'extended2';
      }
    } elseif ($widget_position == 'full_width1' || $widget_position == 'full_width2') {
      if ($widget_position == 'full_width1') {
        $widget_place = 'fullwidth1';
      } elseif ($widget_position == 'full_width2') {
        $widget_place = 'fullwidth2';
      }
    } elseif ($widget_position == 'full_width4' || $widget_position == 'full_width5') {
      if ($widget_position == 'full_width4') {
        $widget_place = 'fullwidth4';
      } elseif ($widget_position == 'full_width5') {
        $widget_place = 'fullwidth5';
      }
    }

    if (!empty($getWidgetPlace))
      return $widget_place;
    else
      return 'advancedslideshow.' . $widget_place . '-advancedslideshows';
  }

  /**
   * This function return the complete path of image, from the photo id.
   *
   * @param $id: The photo id.
   * @param $type: The type of photo required.
   * @return Image path.
   */
  public function displayPhoto($id, $type = 'thumb.profile') {
    if (empty($id)) {
      return null;
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($id, $type);
    if (!$file) {
      return null;
    }

    // Get url of the image
    $src = $file->map();
    return $src;
  }
  public function manualPhoto($photo, $advancedslideshow_id) {
         $file = $photo->getFileName();
         $fileName = $file;
        
        if (!$fileName) {
            $fileName = basename($file);
        }
        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'advanceslideshow_noobslides',
            'parent_id' => $advancedslideshow_id,
        );
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($mainPath)
                ->destroy();
        // Store
      $iMain = $filesTable->createFile($mainPath, $params);
      // Remove temp files
        @unlink($mainPath);
     return $iMain->getPhotoUrl();
    }
}

?>
