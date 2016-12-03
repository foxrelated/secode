<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 2011-09-07 17:18:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Model_Video extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'store_product';

  protected $_owner_type = 'user';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'store_profile',
      'product_id' => $this->product_id,
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getRichContent($view = false, $params = array())
  {


    // if video type is youtube
    if ($this->type == 1) {
      $videoEmbedded = $this->compileYouTube($this->code, $view);
    }
    // if video type is vimeo
    if ($this->type == 2) {
      $videoEmbedded = $this->compileVimeo($this->code, $view);
    }

    if ($this->type == 3) {
      $video_location = Engine_Api::_()->storage()->get($this->file_id, $this->getType())->getHref();
      $videoEmbedded = $this->compileFlowPlayer($video_location, $view);
    }

    return $videoEmbedded;
  }

  public function compileYouTube($code, $view)
  {
    //560 x 340
    $embedded = '<iframe width="' . ($view ? "695" : "425") . '" height="' . ($view ? "391" : "344") . '" src="https://www.youtube.com/embed/' . $code . '" frameborder="0" allowfullscreen></iframe>';

    return $embedded;
  }

  public function compileVimeo($code, $view)
  {


    //640 x 360
    $embedded = '<iframe src="https://player.vimeo.com/video/' . $code . '" width="' . ($view ? "695" : "400") . '" height="' . ($view ? "391" : "230") . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    

    return $embedded;
  }

  private function isVideoConverted()
  {
    return ($this->type == 3 && $this->status == 1 && !empty($this->file_id));
  }

  private function getFileLocation()
  {
    if ($this->isVideoConverted()) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $this->file_id);
      if ($storage_file) {
        return $storage_file->map();
      }
    }
    return '';
  }

  private function getFileExt()
  {
    if ($this->isVideoConverted()) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $this->file_id);
      if ($storage_file) {
        return $storage_file->extension;
      }
    }
  }

  public function compileFlowPlayer($location, $view)
  {

    if ($this->code != 'flv') {
      $location = $this->getFileLocation();

      $html = <<<CODE
<video id="video" controls width="100%" height="auto"><source type="video/mp4" src="{$location}"></video>
CODE;
      return $html;
    }
    $embedded = "<div id='videoFrame'></div><script type='text/javascript'>\$('videoFrame').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$this->video_id',{src: '" . Zend_Registry::get('StaticBaseUrl') . "externals/flowplayer/flowplayer-3.2.18.swf', width: " . ($view ? "480" : "420") . ", height: " . ($view ? "386" : "326") . ", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: " . ($view ? "false" : "true") . ", duration: '$this->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})</script>";
    return $embedded;
  }
}