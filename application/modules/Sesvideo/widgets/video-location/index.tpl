<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/richMarker.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/marker.js'); ?>
<script type="text/javascript">
var markers = [];
var map;
var oms;
function initialize() {
	var bounds = new google.maps.LatLngBounds();
	 map = new google.maps.Map(document.getElementById('map-canvas'), {
		zoom: 10,
		 scrollwheel: true,
		center: new google.maps.LatLng(<?php echo $this->lat; ?>, <?php echo $this->lng; ?>),
	});
	 oms = new OverlappingMarkerSpiderfier(map,
        {nearbyDistance:40,circleSpiralSwitchover:0 }
				);
	<?php 
	 $count = 0; 
	  $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating',1);
		$allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.show',1);
		if($allowRating == 0){
			if($allowShowPreviousRating == 0)
				$ratingShow = false;
			 else
				$ratingShow = true;
		}else
			$ratingShow = true;
	if($this->paginator->getTotalItemCount()>0){ ?>
	<?php foreach($this->paginator as $item){
		$urlIframe = $item->getRichContent(true,array(),true);
	?>
	<!--var description = <?php echo json_encode($item->getDescription()); ?>;-->
	var title = <?php echo json_encode($this->htmlLink($item->getHref(),$item->getTitle() )); ?>;
	<?php 
	$user = Engine_Api::_()->getItem('user',$item->owner_id);
	$owner = $item->getOwner();
	$ratings = '';
	$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref());
	?>
	<?php 
	$likeButton = $favouriteButton = $addToplaylist = '';
	if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){
		$likeButton = '<a href="javascript:;" data-url="'.$item->getIdentity().'" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_sesvideo_video "> <i class="fa fa-thumbs-up"></i><span>'.$item->like_count.'</span></a>';
		$favouriteButton = '<a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_sesvideo_video " data-url="'.$item->getIdentity().'"><i class="fa fa-heart"></i><span>'.$item->favourite_count.'</span></a>';
		$addToplaylist = '<a href="javascript:;" class="sesbasic_icon_btn sesvideo_add_playlist" onclick="opensmoothboxurl('."'".$this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$item->video_id),'default',true)."'".')" title="'.$this->translate('Add To Playlist').'" data-url="'.$item->getIdentity().'"><i class="fa fa-plus"></i></a>';
	}
	?>
	 <?php if($ratingShow && isset($item->rating) && $item->rating > 0 ): 
	$ratings =   '<span  title="'.$this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1))).'"><i class="fa fa-star"></i>'. round($item->rating,1).'/5'.'</span>';
  endif; ?>
	var owner = <?php echo json_encode('<div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light"><span><i class="fa fa-user"></i>'.$this->translate("by").$this->htmlLink($owner->getHref(),$owner->getTitle() ).'</span></div>'); ?>;
	var stats = '<div class="sesbasic_largemap_stats sesvideo_list_stats sesbasic_clearfix"><span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span> <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span><span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span><span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span><?php echo $ratings;  ?></div>';	
	var socialshare = <?php echo json_encode('<div class="sesbasic_largemap_btns sesvideo_list_btns"><a href="http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $item->getTitle().'" onclick="socialSharingPopUp(this.href,'."'".$this->translate('Facebook')."'".');return false;" class="sesbasic_icon_btn sesbasic_icon_facebook_btn sesbutton_share"><i class="fa fa-facebook"></i></a><a href="http://twitthis.com/twit?url=' . $urlencode . '&title=' . $item->getTitle().'" onclick="socialSharingPopUp(this.href,'."'". $this->translate('Twitter')."'".');return false;" class="sesbasic_icon_btn sesbasic_icon_twitter_btn sesbutton_share"><i class="fa fa-twitter"></i></a><a href="http://pinterest.com/pin/create/button/?url='.$urlencode.'&media='.urlencode((strpos($item->getPhotoUrl(),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'].$item->getPhotoUrl() ) : $item->getPhotoUrl())).'&description='.$item->getTitle().'" onclick="socialSharingPopUp(this.href,'."'".$this->translate('Pinterest')."'".');return false;" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn sesbutton_share"><i class="fa fa-pinterest"></i></a>'.$likeButton.$favouriteButton.$addToplaylist.'</div>'); ?>;
		 var images = '<div class="image"><img src="<?php echo $item->getPhotoUrl(); ?>"  /></div>';			
		 var marker_html = '<div class="pin public marker_<?php echo $count; ?>" data-lat="<?php echo $item->lat; ?>" data-lng="<?php echo $item->lng; ?>">' +
		 	'<div class="iframe_url" style="display:none;"  allowfullscreen=""><?php echo $urlIframe; ?></div>' +
				'<div class="wrapper">' +
					'<div class="small">' +
						'<img src="<?php echo $item->getPhotoUrl('thumb.icon'); ?>" style="height:48px;width:48px;" alt="" />' +
					'</div>' +
					'<div class="large">' +
						images +
						'<div class="sesbasic_large_map_content sesbasic_clearfix">' +
							'<div class="sesbasic_large_map_content_title">'+title+'</div>' +owner+stats+socialshare+
						'</div>' +
						'<a class="icn close" href="javascript:;" title="Close"><i class="fa fa-close"></i></a>' + 
					'</div>' +
				'</div>' +
				'<span class="sesbasic_largemap_pointer"></span>' +
				'</div>';
			 		var marker = new RichMarker({
						position: new google.maps.LatLng(<?php echo $item->lat; ?>, <?php echo $item->lng; ?>),
						map: map,
						flat: true,
						draggable: false,
						scrollwheel: false,
						anchor: RichMarkerPosition.BOTTOM,
						content: marker_html
					});
			<?php	if($count == 0){ ?>
					oms.addListener('click', function(marker) {
					var id = marker.markerid;
					var iframeURL = sesJqueryObject('.marker_'+id).find('.iframe_url').html();
					var height = 164;
					var width = 294;
					previousIndex = sesJqueryObject('.marker_'+ id).parent().parent().css('z-index');
					sesJqueryObject('.marker_'+ id).parent().parent().css('z-index','9999');
						if(typeof iframeURL != 'undefined' && !sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').find('iframe').attr('src'))
						sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').html('<iframe src="'+iframeURL+'" height="'+height+'" width="'+width+'" style="overflow:hidden"  allowfullscreen="">');
						sesJqueryObject('.pin').removeClass('active').css('z-index', 10);
						sesJqueryObject('.marker_'+id).addClass('active').css('z-index', 200);
						sesJqueryObject('.marker_'+id+' .large .close').click(function(){
						sesJqueryObject(this).parent().parent().parent().parent().parent().css('z-index',previousIndex);
						sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').html('');
						sesJqueryObject('.pin').removeClass('active');
						return false;
					});
				});
			<?php } ?>
					markers.push(marker);
					marker.markerid = <?php echo $count; ?>;
					oms.addMarker(marker);
					marker.setMap(map);
					bounds.extend(marker.getPosition());
			<?php 
				$count++;
			} ?>
			map.fitBounds(bounds);
			<?php } ?>
}
var interval;
var countMarker = <?php echo $count; ?>;
function DeleteMarkers() {
        //Loop through all the markers and remove
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
				markerData = [];
};
var searchParams;
var markerArrayData ;
function callNewMarkersAjax(){
	 (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesvideo/name/<?php echo $this->widgetName; ?>",
      'data': {
        format: 'html',
				is_ajax : 1,
				searchParams:searchParams,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if($('loadingimgsesvideo-wrapper'))
						sesJqueryObject('#loadingimgsesvideo-wrapper').hide();
				DeleteMarkers();
       	if(responseHTML){
					markerArrayData = sesJqueryObject.parseJSON(responseHTML);
					if(markerArrayData.length)
						newMarkerLayout();
				}
      }
    })).send();	
}
var markerData =[];
var previousIndex=0;
function newMarkerLayout(){
	if(!markerArrayData.length)
		return ;
	var bounds = new google.maps.LatLngBounds();
	for(i=0;i<markerArrayData.length;i++){
		var images = '<div class="image"><img src="'+markerArrayData[i]['image_url']+'"  /></div>';		
		var owner = markerArrayData[i]['owner'];
		var stats = markerArrayData[i]['stats'];
		var socialshare = markerArrayData[i]['socialshare'];
		 var marker_html = '<div class="pin public marker_'+countMarker+'" data-lat="'+ markerArrayData[i]['lat']+'" data-lng="'+ markerArrayData[i]['lng']+'">' +
			 '<div class="iframe_url" style="display:none;" >'+markerArrayData[i]['iframe_url']+'</div>' +
				'<div class="wrapper">' +
					'<div class="small">' +
						'<img src="'+markerArrayData[i]['image_url']+'" style="height:48px;width:48px;" alt="" />' +
					'</div>' +
					'<div class="large">' +
						images +
						'<div class="sesbasic_large_map_content">' +
							'<div class="sesbasic_large_map_content_title">'+markerArrayData[i]['title']+'</div>' +owner+stats+socialshare+
						'</div>' +
						'<a class="icn close" href="javascript:;" title="Close"><i class="fa fa-close"></i></a>' + 
					'</div>' +
				'</div>' +
				'<span class="sesbasic_largemap_pointer"></span>' +
				'</div>';
			  markerData = new RichMarker({
						position: new google.maps.LatLng(markerArrayData[i]['lat'], markerArrayData[i]['lng']),
						map: map,
						flat: true,
						draggable: false,
						scrollwheel: false,
						id:countMarker,
						anchor: RichMarkerPosition.BOTTOM,
						content: marker_html
				});
				oms.addListener('click', function(marker) {
					var id = marker.markerid;
					var iframeURL = sesJqueryObject('.marker_'+id).find('.iframe_url').html();
					var height = 164;
					var width = 294;
					previousIndex = sesJqueryObject('.marker_'+ id).parent().parent().css('z-index');
					sesJqueryObject('.marker_'+ id).parent().parent().css('z-index','9999');
					if(typeof iframeURL != 'undefined' && !sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').find('iframe').attr('src'))
						sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').html('<iframe src="'+iframeURL+'" height="'+height+'" width="'+width+'" style="overflow:hidden"  allowfullscreen="" >');
						sesJqueryObject('.pin').removeClass('active').css('z-index', 10);
						sesJqueryObject('.marker_'+ id).addClass('active').css('z-index', 200);
						sesJqueryObject('.marker_'+ id+' .large .close').click(function(){
							sesJqueryObject(this).parent().parent().parent().parent().parent().css('z-index',previousIndex);
							sesJqueryObject('.marker_'+id).find('.wrapper').find('.large').find('.image').html('');
							sesJqueryObject('.pin').removeClass('active');
							return false;
						});
				});
				markers.push( markerData);
				markerData.setMap(map);
				bounds.extend(markerData.getPosition());
				markerData.markerid = countMarker;
				oms.addMarker(markerData);
				countMarker++;
  }
	map.fitBounds(bounds);
}
google.maps.event.addDomListener(window, 'load', initialize);
sesJqueryObject('.sesbutton_share').click(function(e){
	e.preventDefault();
});
</script>
<div id="map-canvas" class="map sesbasic_large_map sesbm sesbasic_bxs"></div>