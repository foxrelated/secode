<?php

class Socialstore_Widget_ProductVideoController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		$checkVideo = Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('video');
		$checkYnVideo = Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('ynvideo');
		$product = Engine_Api::_()->getItem('social_product', Zend_Registry::get('product_detail_id'));
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() == 0) {
      		
      		$isAdmin = 0;
      	}
		else {
      		$level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
	    	if( in_array($level->type, array('admin', 'moderator')) ) {
	      		$isAdmin = 1;
	    	}
	    	else {
	    		$isAdmin = 0;
	    	}
		}
		if ((!$checkVideo && !$checkYnVideo) || $product->video_url == '' || $product->deleted == 1 || (($viewer->getIdentity() != $product->owner_id || $isAdmin == 0) && ($product->view_status == "hide" || $product->approve_status != "approved"))) {
			$this->setNoRender(true);
			return;
		}
		$video_url = $product->video_url;
		$request = new Zend_Controller_Request_Http($video_url);
		Zend_Controller_Front::getInstance()->getRouter()->route($request);
        $params = $request->getParams();
        $video_id = $params['video_id'];
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $video = Engine_Api::_()->getItem('video', $video_id);
        $can_embed = true;
	    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1) ) {
	      $can_embed = false;
	    } else if( isset($video->allow_embed) && !$video->allow_embed ) {
	      $can_embed = false;
	    }
	    $this->view->can_embed = $can_embed;
	    $embedded = "";
	    if( $video->status == 1 ) {
	      if( !$video->isOwner($viewer) ) {
	        $video->view_count++;
	        $video->save();
	      }
	      $embedded = $video->getRichContent(true);
	    }
	    $this->view->video = $video;
	    $this->view->videoEmbedded = $embedded;
	}

}
