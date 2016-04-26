<?php

class Ynfundraising_Widget_CampaignsProfileInfoController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
		$this->view->limit = 8;
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynfundraising_campaign');
	    $this->view->campaign = $subject;     
		$album = $subject->getSingletonAlbum();  
		$this->view->photos = $album->getCollectiblesPaginator();
		if($subject->video_url)
		{
			$session = new Zend_Session_Namespace('mobile');
    		$mobile = $session->mobile;
			
			$url = $subject->video_url;
			$new_code = @pathinfo($url);
	        $url= preg_replace("/#!/", "?", $url);
	
	        // get v variable from the url
	        $arr = array();
	        $arr = @parse_url($url);
	        $code = "code";
	        $parameters = $arr["query"];
	        parse_str($parameters, $data);
	        $code = $data['v'];
	        if($code == "") {
	          $code = $new_code['basename'];
	        }
			
			$view = true;
			$autoplay = false;
			if( !$mobile ) 
			{
		      $embedded = '<object width="500" height="300">
		      <param name="movie" value="http://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
		      <param name="allowFullScreen" value="true"/>
		      <param name="allowScriptAccess" value="always"/>
		      <embed src="http://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1'.($view?"":"&autoplay=1").'" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="'.($view?"500":"500").'" height="'.($view?"300":"300").'" wmode="transparent"/>
		      <param name="wmode" value="transparent" />
		      </object>';
		    } 
		    else 
		    {
		      $autoplay = !$mobile && !$view;
		      $embedded = '
		        <iframe
		        title="YouTube video player"
		        id="videoFrame"
		        class="youtube_iframe'.($view?"_big":"_small").'"'.
				'width="500"
		        height="300"
		            '.'
		        src="http://www.youtube.com/embed/'.$code.'?wmode=opaque'.($autoplay?"&autoplay=1":"").'"
		        frameborder="0"
		        allowfullscreen=""
		        scrolling="no">
		        </iframe>
		        <script type="text/javascript">
		          en4.core.runonce.add(function() {
		            var doResize = function() {
		              var aspect = 16 / 9;
		              var el = document.id("videoFrame");
		              var parent = el.getParent();
		              var parentSize = parent.getSize();
		              el.set("width", parentSize.x);
		              el.set("height", parentSize.x / aspect);
		            }
		            window.addEvent("resize", doResize);
		            doResize();
		          });
		        </script>
		      ';
		    }	
			
			$this->view->image_embed = "http://img.youtube.com/vi/$code/default.jpg";
		}
		else
			$embedded = "";
		$this->view->embedVideo = $embedded;
    }

}
