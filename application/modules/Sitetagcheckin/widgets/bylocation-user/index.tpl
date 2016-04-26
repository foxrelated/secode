<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
 
if($this->mapshow) {
  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
   $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
}
?>

<?php $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.zoom', 1); ?>

<script type="text/javascript">
  var mapShow = '<?php echo $this->mapshow; ?>';
  var current_page = '<?php echo $this->current_page; ?>';
  var paginatePageLocations = function(page, mapShow, switchViewPagination) {
  
		var  formElements = document.getElementById('filter_form');
		var parms = formElements.toQueryString(); 

		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&defaultView=' + switchViewPagination + '&page=' + page;

		document.getElementById('page_location_loding_image').style.display ='';
    var url = en4.core.baseUrl + 'widget/index/mod/sitetagcheckin/name/bylocation-user';
    if(mapShow == 1) {
			clearOverlays();
			gmarkers = [];
    }
    
    en4.core.request.send(new Request.HTML({
//       'url' : url,
//       'data' : {
//         'format' : 'html',
//         'page' : page,
//         'is_ajax' : 1
//       },
      method : 'post',
			'url' : url,
			'data' : param,
      onSuccess :function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('page_location_loding_image').style.display ='none';
				document.getElementById('eventlocation_location_map_anchor').getParent().innerHTML = responseHTML;
				if (mapShow == 1) {
				setMarker();
				}
				switchview(switchViewPagination);
      }
    })/*, {
      'element' : document.getElementById('eventlocation_location_map_anchor').getParent()
    }*/
    );
  }
</script>
<script type="text/javascript">
  var pageAction = function(page) {
  var mapShow = '<?php echo $this->mapshow; ?>';
		paginatePageLocations(page, mapShow, switchViewPagination);
  }
</script>

<?php if (empty($this->is_ajax)) : ?>
<div class="eventlocation_browse_location" id="eventlocation_browse_location" >
	<?php if (count($this->paginator) > 0): ?>
		<?php $mapShow = $this->mapshow; ?> 
		<?php if (!empty($mapShow)): ?>
			<div class="eventlocation_map_container_right" id ="eventlocation_map_container_right"></div>
			<div id="eventlocation_map_container" class="eventlocation_map_container absolute">
				<div class="eventlocation_map_container_topbar" id='eventlocation_map_container_topbar' style ='display:block;'>
					<a id="largemap" href="javascript:void(0);" onclick="smallLargeMap(1)" class="bold fleft">&laquo; <?php echo $this->translate('Large Map'); ?></a>
					<a id="smallmap" href="javascript:void(0);" onclick="smallLargeMap(0)" class="bold fleft"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
				</div>

				<div class="eventlocation_map_container_map_area fleft seaocore_map" id="eventlocation_map">
					<div class="eventlocation_map_content" id="eventlocation_browse_map_canvas" ></div>
					<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
					<?php if (!empty($siteTitle)) : ?>
						<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="stcheckin_adseresult_list" id="eventlocation_content_content">
<?php endif; ?>
  	<a id="eventlocation_location_map_anchor" class="pabsolute"></a>
		<?php if (count($this->paginator) > 0): ?>
			<div id="seaocore_browse_list">
				<?php if (!empty($this->is_ajax)) : ?>
					<div class="stcheckin_adseresult_list_top">
						<div class="fleft">
							<?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalresults),$this->locale()->toNumber($this->totalresults)) ?>
						</div>
	        	<div class="fright">
							<?php  //if( $this->grid_view): ?>
								<div class="stcheckin_profile_buttons stcheckin_tip">
									<div class="stcheckin_tip_content"><?php echo $this->translate("Grid View"); ?></div>
									<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/grid-view.png' onclick="switchview(1)" align="left" alt="" />
								</div>
							<?php //endif;?>
							<?php  //if( $this->list_view): ?>
								<div class="stcheckin_profile_buttons stcheckin_tip">
									<div class="stcheckin_tip_content"><?php echo $this->translate("List View");?></div>
									<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/list-view.png' onclick="switchview(0)" align="left" alt="" />
								</div>
							<?php //endif; ?>
			  		</div>
			 		</div> 
					<div id="grid_view" style="display: block;">
						<ul class="seaocore_browse_list">
							<?php foreach ($this->paginator as $item): ?>
								<?php if(!empty($item->location) || !empty($this->locationVariable)) : ?>
									<li>
										<div class="stcheckin_adseresult_user_photo">
											<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location)? "seao_common_add_tooltip_link marker_photo_".$item->user_id :'un_location_eventlocation seao_common_add_tooltip_link', 'rel'=>'user'.' '.$item->user_id )); ?>
										</div>
										<?php $online_status = Engine_Api::_()->sitetagcheckin()->isOnline($item->user_id); ?>
										<div class='seaocore_browse_list_info'>
											<div class='seaocore_browse_list_info_title'>
												<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title'
													=> $item->getTitle(), 'target' => '_parent', 'class' =>!empty($item->location)? "seao_common_add_tooltip_link marker_".$item->user_id :'un_location_eventlocation seao_common_add_tooltip_link fleft' , 'rel'=>'user'.' '.$item->user_id)); ?>									
											</div>
											<?php $user_status = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.userstatus');
											if (!empty($user_status)) : ?>
												<div class="seaocore_browse_list_info_date">	
													<?php 
													if (!empty($online_status)) :  ?>
														<img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
														<?php echo $this->translate("Online");?>
													<?php else: ?>
														<img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />
														<?php echo $this->translate("Offline");?>
													<?php endif; ?>
												</div>	
											<?php endif; ?>
											<?php if(!empty($item->location)): ?>
												<div class="seaocore_browse_list_info_date">	
													<?php echo $this->translate("Location: "); echo $this->translate($item->location); ?>
												</div>
												<?php if (!empty($item->distance) && isset($item->distance)): ?>
													<div class="seaocore_browse_list_info_stat">
														<?php $flage = Engine_Api::_()->seaocore()->geoUserSettings('sitetagcheckin');
									           	if (!$flage): ?>
															<b><?php echo $this->translate("approximately %s miles", round($item->distance, 2)); ?></b>
														<?php else: ?>
															<b><?php $distance = (1 / 0.621371192) * $item->distance; echo $this->translate("approximately %s kilometers", round($distance, 2)); ?></b>
														<?php endif; ?>
													</div>
												<?php endif; ?>
											<?php endif; ?>
											<div class="stcheckin_adseresult_list_options">
												<?php if(!empty($item->location)): ?>
													<b>
														<?php if (!empty($this->mobile)) : ?>
															<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->seao_locationid, 'resouce_type' => 'seaocore', 'is_mobile' => $this->mobile), $this->translate("Get Directions"), array('target' => '_blank', 'class' => "buttonlink seaocore_icon_direction")) ; ?>
														<?php else: ?>
															<?php if (!empty($this->is_ajax)) : ?>
																<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->seao_locationid, 'resouce_type' => 'seaocore'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false' , 'class' => "buttonlink seaocore_icon_direction")) ; ?>
																<?php else : ?>
																	<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->seao_locationid, 'resouce_type' => 'seaocore'), $this->translate("Get Directions"), array('class' => 'smoothbox seaocore_icon_direction')) ; ?>
																<?php endif; ?>
														<?php endif; ?>
													</b>
												<?php endif; ?>
												<?php //FOR MESSAGE LINK
												$items = Engine_Api::_()->getItem('user', $item->user_id);
												$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
												if ((Engine_Api::_()->seaocore()->canSendUserMessage($items)) && (!empty($viewer_id))) : ?>
												<a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $item->user_id ?>" target="_parent" class="buttonlink" style=" background-image: url(<?php echo $this->baseUrl() ?>/application/modules/Messages/externals/images/send.png);"><?php echo $this->translate('Message'); ?></a>
												<?php endif; ?>
												<?php //Add friend link.
												$uaseFRIENFLINK = $this->userFriendshipAjax($this->user($item->user_id)); ?>
												<?php if (!empty($uaseFRIENFLINK)) : ?>
												<?php echo $uaseFRIENFLINK; ?>
												<?php endif; ?>
											</div>	
										</div>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
					<div id="image_view" style="display: none;">
						<ul class="stcheck_grid_view">
						<?php foreach ($this->paginator as $item): ?>
							<li class="item_thumb_wrapper" >
								<div class="item_thumb">
									<table>
										<tr valign="middle">
											<td align="center">
												<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location)? "marker_photo_".$item->user_id :'un_location_eventlocation')); ?>
											</td>
										</tr>
									</table>			
								</div>
								<?php $online_status = Engine_Api::_()->sitetagcheckin()->isOnline($item->user_id); ?>
								<div class='item_title'>
								  <?php $user_status = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.userstatus');
								  if (!empty($user_status)): ?>
										<span class="fright seaocore_txt_light">
											<?php 
											if (!empty($online_status)) :  ?>
												<img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
												<?php  echo $this->translate("Online");?>
											<?php else: ?>
												<img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />
												<?php  echo $this->translate("Offline");?>
											<?php endif; ?>
										</span>	
									<?php endif; ?>
									<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title'
										=> $item->getTitle(), 'target' => '_parent', 'class' =>!empty($item->location)? "seao_common_add_tooltip_link marker_".$item->user_id :'un_location_eventlocation seao_common_add_tooltip_link' , 'rel'=>'user'.' '.$item->user_id)); ?>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="clr eventlocation_browse_location_paging" style="margin-top:10px;">
						<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitetagcheckin"), array("orderby" => $this->orderby)); ?>
						<?php if( count($this->paginator) > 1 ): ?>
							<div class="fleft" id="page_location_loding_image" style="display: none;margin:5px;">
								<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
							</div>
						<?php endif; ?>
					</div>	
				<?php endif; ?>
			</div>	
		<?php else: ?>
			<div class="tip"> 
				<span><?php echo $this->translate('No members were found matching this criteria.'); ?></span>
			</div>
		<?php endif; ?>
		<?php if (empty($this->is_ajax)) : ?>
	</div>
</div>

<script type="text/javascript" >

  /* moo style */
  window.addEvent('domready',function() {
    //smallLargeMap(1);
    var mapShow = '<?php echo $this->mapshow; ?>';
    //var Clientwidth = $('global_content').getElement(".layout_sitetagcheckin_bylocation_user").clientWidth;

    if (mapShow) {
      if ($('eventlocation_map_container'))
				var offsetWidth = $('eventlocation_map_container').offsetWidth;
				
			if ($('eventlocation_browse_map_canvas'))
				$('eventlocation_browse_map_canvas').setStyle("height",offsetWidth);

			if (document.getElementById("smallmap"))
			document.getElementById("smallmap").style.display = "none";

			if ($('eventlocation_map_right'))
				$('eventlocation_map_right').style.display = 'none';
		}

    if (mapShow == 1) {
			<?php if($this->paginator->count() > 0):?>
					initialize();
					//switchview(<?php //echo $this->defaultView ?>);
			<?php endif;?>
			
    }
  });
  
  var mapShow = '<?php echo $this->mapshow; ?>';
  if (mapShow) {
  
		if ($('seaocore_browse_list')) {
		  if ($('eventlocation_map'))
				var elementStartY = $('eventlocation_map').getPosition().x ;
			if ($('eventlocation_map_container'))
				var offsetWidth = $('eventlocation_map_container').offsetWidth;
				
			var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);

			function setMapContent () {
				//var mapShow = '<?php //echo $this->mapshow; ?>';
				
				if (!$('seaocore_browse_list')) {
					return;
				}

				var element=$("eventlocation_map_container");
				if(element) {
				if (element.offsetHeight > $('seaocore_browse_list').offsetHeight) {
					if(!element.hasClass('absolute')) {
						element.addClass('absolute');
						element.removeClass('fixed');
					if(element.hasClass('bottom'))
						element.removeClass('bottom');
					}
					return;
				}
				
				var elementPostionStartY = $('seaocore_browse_list').getPosition().y ;
				var elementPostionStartX = $('eventlocation_map_container').getPosition().x ;
				var elementPostionEndY = elementPostionStartY + $('seaocore_browse_list').offsetHeight - element.offsetHeight;

				if( ((elementPostionEndY) < window.getScrollTop())) {
					if(element.hasClass('absolute'))
						element.removeClass('absolute');
					if(element.hasClass('fixed'))
						element.removeClass('fixed');
					if(!element.hasClass('bottom'))
						element.addClass('bottom');
				} 
				else if(((elementPostionStartY)  < window.getScrollTop())) {
					if(element.hasClass('absolute'))
						element.removeClass('absolute');
					if(!element.hasClass('fixed'))
						element.addClass('fixed');
					if(element.hasClass('bottom'))
						element.removeClass('bottom');
						element.setStyle("right",actualRightPostion);
						element.setStyle("width",offsetWidth);
				}
				else if(!element.hasClass('absolute')) {
					element.addClass('absolute');
					element.removeClass('fixed');
					if(element.hasClass('bottom'))
						element.removeClass('bottom');
				}
				}
			}

			window.addEvent('scroll', function () {
				setMapContent();
			});
			
		}

		function smallLargeMap(option) {
			if(option == '1') {
				$('eventlocation_browse_map_canvas').setStyle("height",'400px');
				document.getElementById("largemap").style.display = "none";
				document.getElementById("smallmap").style.display = "block";
				if(!$('eventlocation_map_container').hasClass('eventlocation_map_container_exp'))
					$('eventlocation_map_container').addClass('eventlocation_map_container_exp');
			} else {
			$('eventlocation_browse_map_canvas').setStyle("height",offsetWidth);
				document.getElementById("largemap").style.display = "block";
				document.getElementById("smallmap").style.display = "none";
				if($('eventlocation_map_container').hasClass('eventlocation_map_container_exp'))
					$('eventlocation_map_container').removeClass('eventlocation_map_container_exp');
				
			}
			setMapContent();
			google.maps.event.trigger(map, 'resize');
		}
	}
</script>
<script type="text/javascript" >
  var switchViewPagination =  '<?php echo $this->defaultView;?>';
  function switchview(flage){
    if(flage == 1) {
      if($('image_view')){
        switchViewPagination = 1;
				if($('grid_view'))
					$('grid_view').style.display='none';
			$('image_view').style.display='block';
      }
    } else {
      if($('grid_view')){
      
        switchViewPagination = 0;
				$('grid_view').style.display='block';
      if($('image_view'))
				$('image_view').style.display='none';
      }
    }
  }
</script>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<script type="text/javascript">
  if(mapShow) {
		var script = '<script type="text/javascript" src="https://google-maps-' +
				'utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble';
		if (document.location.search.indexOf('compiled') !== -1) {
			script += '-compiled';
		}
		script += '.js"><' + '/script>';
		document.write(script);
	}
</script>
<script type="text/javascript" >
  var mapShow = '<?php echo $this->mapshow; ?>';
  if (mapShow) {
    //<![CDATA[
  // this variable will collect the html which will eventually be placed in the side_bar
  var side_bar_html = "";

  // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var gmarkers = [];
  var infoBubbles;
  var markerClusterer = null;
  // global "map" variable
  var map = null;
  // A function to create the marker and set up the event window function
  function createMarker(latlng, name, html,title_page, page_id) {
    var contentString = html;
    if(name ==0) {
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_page,
       // page_id : page_id,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else {
      var marker =new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_page,
        //page_id: page_id,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }

    gmarkers.push(marker); 
    google.maps.event.addListener(marker, 'click', function() {
			google.maps.event.trigger(map, 'resize');
			map.setCenter(marker.position);
			//map.setZoom(<?php //echo '5'; ?> );
      infoBubbles.open(map,marker);
      infoBubbles.setContent(contentString);
    });

    //Show tooltip on the mouse over.
	  $$('.marker_' + page_id).each(function(locationMarker) {
			locationMarker.addEvent('mouseover',function(event) {
				google.maps.event.trigger(map, 'resize');
				map.setCenter(marker.position);
				infoBubbles.open(map,marker);
				infoBubbles.setContent(contentString);
			});			
    });
    
    //Show tooltip on the mouse over.
	  $$('.marker_photo_' + page_id).each(function(locationMarker) {
			locationMarker.addEvent('mouseover',function(event) {
				google.maps.event.trigger(map, 'resize');
				map.setCenter(marker.position);
				infoBubbles.open(map,marker);
				infoBubbles.setContent(contentString);
			});
    });
  }

  function initialize() {

    // create the map
    var myOptions = {
      zoom: <?php echo $defaultZoom ?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    map = new google.maps.Map(document.getElementById("eventlocation_browse_map_canvas"),
    myOptions);

    google.maps.event.addListener(map, 'click', function() {
      <?php if( $this->paginator->count() > 0): ?>
				infoBubbles.close();
      <?php endif; ?>
    });
    setMarker();

  }
  
	function clearOverlays() {
		infoBubbles.close();
		google.maps.event.trigger(map, 'resize');

		if (gmarkers) {
			for (var i = 0; i < gmarkers.length; i++ ) {
				gmarkers[i].setMap(null);
			}
		}
    if (markerClusterer) {
		  markerClusterer.clearMarkers();
		}
	}
	
  function setMapCenterZoomPoint(bounds, maplocation) {
    if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds &&  bounds.center_lat &&  bounds.center_lng) {
      maplocation.setCenter(new google.maps.LatLng( bounds.center_lat,  bounds.center_lng), <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
    } else {
      maplocation.setCenter(new google.maps.LatLng(lat, lng), <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
    }
    if (bds) {
      maplocation.setCenter(bds.getCenter());
      maplocation.fitBounds(bds);
    }
  }
  
  infoBubbles = new InfoBubble({
		maxWidth: 400,
		maxHeight: 400,
		shadowStyle: 1,
		padding: 0,
		backgroundColor: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff');?>',
		borderRadius: 5,
		arrowSize: 10,
		borderWidth: 1,
		borderColor: '#2c2c2c',
		disableAutoPan: true,
		hideCloseButton: false,
		arrowPosition: 50,
		//backgroundClassName: 'sitetag_checkin_map_tip',
		arrowStyle: 0
	});
}
</script>

<style type="text/css">
  #eventlocation_browse_map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #eventlocation_browse_map_canvas > div{
    height: 300px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>
<?php endif; ?>

<script type="text/javascript" >
if (mapShow) {
function setMarker() {
clearOverlays();

<?php if (count($this->paginator) > 0) : ?>
<?php foreach ($this->paginator as $location) : ?>
<?php if (!empty($location->location)) : ?>
	// obtain the attribues of each marker
	var lat = '<?php echo $location->latitude ?>';
	var lng = '<?php echo $location->longitude  ?>';
	var point = new google.maps.LatLng(lat,lng);
	var page_id = '<?php echo  $location->user_id;  ?>';
	var sponsored = 0;
	// create the marker

	<?php $page_id = $location->user_id; ?>
	var contentString = '<div id="content">'+
		'<div id="siteNotice">'+
		'</div>'+'  <div class="stcheckin_map_location">'+

		'<div class="stcheckin_map_location_title">'+
		'<?php echo $this->htmlLink($location->getHref(), $this->string()->escapeJavascript($location->getTitle()), array('title'
									=> $this->string()->escapeJavascript($location->getTitle()), 'target' => '_parent')); ?>'+
		'<div class="clr"></div>'+
		'</div>'+

		'<div class="stcheckin_map_location_photo" >'+
		'<?php echo str_replace("'", "\'", $this->htmlLink($location->getHref(), $this->itemPhoto($location, 'thumb.icon'), array('title' => $this->string()->escapeJavascript($location->getTitle()), 'target' => '_parent'))); ?>'+
		'</div>'+
				'<div class="stcheckin_map_location_info">'+
					'<div class="stcheckin_map_location_stat">'+
						"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?> "+
					'</div>'+
					'</div>'+
					
		'<div class="clr"></div>'+
		' </div>'+
		'</div>';
	  var marker = createMarker(point,sponsored,contentString,"<?php echo str_replace('"',' ',$location->getTitle()); ?>", page_id);
<?php endif; ?>
<?php   endforeach; ?>
$('eventlocation_map_container').style.display = 'block';
google.maps.event.trigger(map, 'resize');
<?php else: ?>
$('eventlocation_map_container').style.display = 'none';
<?php endif; ?>
//  markerClusterer = new MarkerClusterer(map, gmarkers, {
//  });
<?php //if (!empty($this->locations)): ?>
	//setMapCenterZoomPoint(<?php //echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,map);
<?php //endif; ?>

 //$$('.un_location_eventlocation').each(function(el) { 
   $$('.un_location_eventlocation').addEvent('mouseover',function(event) {
    infoBubbles.close();
    });
  //  });
}
}
</script>
