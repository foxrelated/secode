<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var infoBubbles;
</script>

<script type="text/javascript">
var ADD_LOCATION_TRANSLATE="<?php echo $this->string()->escapeJavascript($this->translate("Add Location")); ?>";
var EDIT_LOCATION_TRANSLATE="<?php echo $this->string()->escapeJavascript($this->translate("Edit Location")); ?>";
var WHERE_WAS_THIS_PHOTO_TAKEN="<?php echo $this->string()->escapeJavascript($this->translate("Where was this photo taken?")); ?>";
var SAVE="<?php echo $this->string()->escapeJavascript($this->translate("Save")); ?>";
var CANCEL="<?php echo $this->string()->escapeJavascript($this->translate("Cancel")); ?>";
</script>

<?php  
	$this->headTranslate(array('Add Location', 'Edit Location', 'Where was this photo taken?', 'Save', 'Cancel', 'Where have you been?'));
?>
<?php   //GET API KEY
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
?>
 <?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/markerclusterer.js");
?>


<?php
   $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
	 $getEnableModuleEvent = $moduleCore->isModuleEnabled('event');
	 $getEnableModuleYnEvent = $moduleCore->isModuleEnabled('ynevent');
	 $getEnableModulePageEvent = $moduleCore->isModuleEnabled('sitepageevent');
	 $getEnableModuleBusinessEvent = $moduleCore->isModuleEnabled('sitebusinessevent');
   $getEnableModuleGroupEvent = $moduleCore->isModuleEnabled('sitegroupevent');
   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<?php

  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/activity_core.js');
?>

<div class="stcheckin_profile_header" id="sitetag_checkin_profile_header">
	<?php if($this->subject->user_id == $this->viewer_id) :?>
		<div class="stcheckin_profile_header_left" id="stcheckin_feeds_buttton">
			<?php
				$url = $this->url(array('action' => 'check-in', 'module' => 'sitetagcheckin', 'controller' => 'checkin', 'resource_type' => $this->subject->getType(), 'resource_id' => $this->subject->getIdentity(), 'checkin_use' => 1, 'checkin_verb' => "Post",  'checkedinto_verb' => $this->checkedinto_verb, 'tab' => $this->identity), 'default', true);
			?>
		<?php $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModulealbum = $moduleCore->isModuleEnabled('album');
    $getEnableModuleAdvalbum = $moduleCore->isModuleEnabled('advalbum');  ?>
        <?php if($getEnableModulealbum || $getEnableModuleAdvalbum):?>
					<a href="javascript:void(0);" class="buttonlink sitetagcheckin_icon_photo" id="add_photos_to_map" onclick="showPhotoStrip(1);" style="display:block;"><?php echo $this->translate("Add Photos to Map");?></a>
        <?php endif;?>
				<a href="javascript:void(0);" class="buttonlink sitetagcheckin_icon_photo" id="done_photo_adding" onclick="showAddToMap();" style="display:none;" ><?php echo $this->translate("Done Adding");?></a>
				<a href="javascript:void(0);" class="buttonlink stckeckin_icon_checkin" onclick="showCheckinLightbox();" id="stckeckin_icon_checkin">
					<?php echo $this->translate("Add Locations on Map"); ?>
				</a>
				<script type="text/javascript">
					function showCheckinLightbox() {
						Smoothbox.open('<?php echo $url; ?>');
					}
				</script>
		</div>
		<div id="background-photo-location-image" class="fleft" style="display:none;">
			<img src="./application/modules/Sitetagcheckin/externals/images/loading.gif" />
		</div>
	<?php endif;?>
	<div class="stcheckin_profile_header_right">
		<div onclick="showFeeds();" id="display_feedlinks" class="stcheckin_profile_buttons stcheckin_tip">
			<div class="stcheckin_tip_content"><?php echo $this->translate("View Feeds");?></div>
            <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0);" ></span>
			<!--<img src="./application/modules/Sitetagcheckin/externals/images/list-view.png" alt="" />-->
		</div>
		<div onclick="showMap();" id="display_maplinks" class="stcheckin_profile_buttons stcheckin_tip">
			<div class="stcheckin_tip_content"><?php echo $this->translate("View Map");?></div>
			<!--<img src="./application/modules/Sitetagcheckin/externals/images/map-view.png" alt="" />-->
            <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2);" ></span>
		</div>
	</div>	
</div>
<?php if($this->subject->user_id == $this->viewer_id) :?>
	<div id="map_add_photo">
		<div id="show-photo-strip"></div>

	</div>
<?php endif;?>


<script type="text/javascript">

function filterMap(category) {
 document.getElements('.seaocheckinmapfilters').removeClass('active');
  clearOverlays();
  gmarkers = [];
  
  var lng_radius = 0.0003,         // degrees of longitude separation
        lat_to_lng = 111.23 / 71.7,  // lat to long proportion in Warsaw
        angle = 0.5,                 // starting angle, in radians
        loclen = 13,
        step = 2 * Math.PI / loclen,
        i,
        loc,
        lat_radius = lng_radius / lat_to_lng; 
  if(category == 1) {	
		document.getElementById('location_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->locations):?> 
			<?php foreach ($this->locations as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
  } 
  else if(category == 2) {
		document.getElementById('checkin_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->checkins):?>  
			<?php foreach ($this->checkins as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->checkins));?>,mapCheckin);
  }
  else if(category == 3) { 
		document.getElementById('phototag_filter').addClass('active'); //.className="seaocheckinmapfilters active";
		<?php if($this->taggedPhotos):?>
			<?php foreach ($this->taggedPhotos as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
					angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->taggedPhotos));?>,mapCheckin);
  } 
  else if(category == 4) {
		document.getElementById('updates_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->updates):?>
			<?php foreach ($this->updates as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>	
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->updates));?>,mapCheckin);
  } 
  else if(category == 5) {
		document.getElementById('eventtag_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->eventLocationsArray):?>
			<?php foreach ($this->eventLocationsArray as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>	
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->eventLocationsArray));?>,mapCheckin);
  }

	markerClusterer = new MarkerClusterer(mapCheckin, gmarkers, {
   zoomOnClick: true
	});

	google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
   	var info = new google.maps.MVCObject;
   	info.set('position', cluster.center_);
    var markers = cluster.getMarkers();
  
    for(var i = 1; i < markers.length; i++) {
      if(info.position != markers[i].position) {
        return;
      }
    }

    infoBubbles.close();
 		contentString = '<div class="sitetag_checkin_map_tip_loading"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" style="margin:10px 0;"/></center></div>';
		infoBubbles.setContent(contentString);
		infoBubbles.open(mapCheckin,info);

    var i = 0;
	  getPhotoPagination(markers[i].location_id, markerCheckin, mapCheckin, markers[i].getTitle(), category);

	});

	if(markerCheckinLocation) {
		markerCheckinLocation.setMap(null);
    markerCheckinLocation=null;
	}

}

var is_active_feed_req=false;
function showFeeds() {
  if($('mapsitetagcheckin_map'))
  $('mapsitetagcheckin_map').style.display = "none";
  if($('sitetagcheckin_autosuggest_tooltiplocations'))
  $('sitetagcheckin_autosuggest_tooltiplocations').style.display = "none";
  if($('setlocation_photo_suggest_tip'))
  $('setlocation_photo_suggest_tip').style.display = "none";

  if($('status_update_location'))
  $('status_update_location').style.display = "none";
  if($('stcheckin_feeds_buttton'))
  $('stcheckin_feeds_buttton').style.display = "none"; 
	if($('map_add_photo'))
  $('map_add_photo').style.display = "none";
  $('feed_items').innerHTML='<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" alt="" style="margin:50px 0;" /></center>';
  is_active_feed_req = true;
	en4.core.request.send(new Request.HTML({
		url : '<?php echo $this->url(array('action' => 'get-feed-items'), 'sitetagcheckin_general', true);?>',
		data : {
			format : 'html',
			subject:  en4.core.subject.guid,
      show_map: 0,
      is_ajax: 0
		},
		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      is_active_feed_req=false;
			$('feed_items').innerHTML = responseHTML;
			Smoothbox.bind($("feed_items"));
			en4.core.runonce.trigger();
		}
	}), {"force": "true"});
}

function showMap() {
  if(is_active_feed_req) return;
//   if($('sitetagcheckin_autosuggest_tooltiplocations'))
//   $('sitetagcheckin_autosuggest_tooltiplocations').style.display = "block";
//   if($('stcheckin_photo_tooltip_wrapper'))
//   $('stcheckin_photo_tooltip_wrapper').style.display = "block";
	if(markerCheckinLocation) {
		markerCheckinLocation.setMap(null);
    markerCheckinLocation=null;
	}
  if($('status_update_location'))
  $('status_update_location').style.display = "block";
  if($('mapsitetagcheckin_map'))
  $('mapsitetagcheckin_map').style.display = "block";
  if($('map_add_photo'))
  $('map_add_photo').style.display = "block";
  if($('stcheckin_feeds_buttton'))
  $('stcheckin_feeds_buttton').style.display = "block"; 
  $('feed_items').empty();
}

function showPhotoStrip(option) {
  if($('filter'))
  $('filter').style.display = "none";
	if($('stckeckin_icon_checkin'))
	$('stckeckin_icon_checkin').style.display = "none";
  if($('done_photo_adding'))
  $('done_photo_adding').style.display ="block";
  if($('add_photos_to_map'))
  $('add_photos_to_map').style.display ="none";
	if($('background-photo-location-image') && option == 1)
	$('background-photo-location-image').style.display = "block";
	en4.core.request.send(new Request.HTML({
		url : '<?php echo $this->url(array('action' => 'get-albums-photos'), 'sitetagcheckin_general', true);?>',
		data : {
			format : 'html',
			//subject:  en4.core.subject.guid,
      show_map: 1,
      is_ajax: 0,
      photoCount: '<?php echo $this->countPhoto ?>'
		},
		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			$('show-photo-strip').innerHTML = responseHTML;
      if($('background-photo-location-image'))
      $('background-photo-location-image').style.display = "none";
			Smoothbox.bind($("show-photo-strip"));
			en4.core.runonce.trigger();
		}
	}), {"force":true});

}

function showAddToMap() {
  $('done_photo_adding').style.display ="none";

  if($('sitetagcheckin_autosuggest_tooltiplocations'))
  $('sitetagcheckin_autosuggest_tooltiplocations').style.display ="none";
  $('add_photos_to_map').style.display ="block";
  if($('filter'))
  $('filter').style.display = "block";

  if($('location_photo_image_recent'))
  $('location_photo_image_recent').style.display = "none";
  <?php if($this->profileAddress) :?>
    location.href = '<?php echo $this->url(array("id" => $this->profileAddress, "tab" => $this->identity), "user_profile");?>';
  <?php endif;?>
}

</script>

<div id="feed_items"></div>
<div id="mapsitetagcheckin_map" class="seaocheckinmaparea">
	<div class="seaocore_map">
		<div class="seaocheckinmap" id="mapsitetagcheckin_browse_map_canvas"></div>
		<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
		<?php if (!empty($siteTitle)) : ?>
			<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
		<?php endif; ?>
	</div>

	<div id="filter" class="seaocheckinmapfiltersbox">
		<table width="100%">
			<tr>
				<td id="location_filter" class="seaocheckinmapfilters " onclick="filterMap(1);">
					<span class="seaocheckinmapfilterscount"><?php echo $this->totalMapLocation;?></span>
					<span><?php echo $this->translate("All");?></span>
				</td>
			
				<td id="updates_filter" class="seaocheckinmapfilters" onclick="filterMap(4);">
					<span class="seaocheckinmapfilterscount"><?php echo $this->totalUpdates;?></span>
          <span>
            <?php echo $this->translate(array('Update', 'Updates', $this->totalUpdates),  $this->locale()->toNumber($this->totalUpdates)) ?>
          </span>
				</td>
			
				<td id="checkin_filter" class="seaocheckinmapfilters" onclick="filterMap(2);">
					<span class="seaocheckinmapfilterscount"><?php echo $this->totalCheckins;?></span>
          <span>
            <?php echo $this->translate(array('CHECKIN_1', 'CHECKINS_1', $this->totalCheckins),  $this->locale()->toNumber($this->totalCheckins)) ?>
          </span>
				</td>
			
				<td id="phototag_filter" class="seaocheckinmapfilters" onclick="filterMap(3);">
					<span class="seaocheckinmapfilterscount"><?php echo $this->totalTaggedPhotos;?></span>
          <span>
            <?php echo $this->translate(array('PHOTO_1', 'PHOTOS_1', $this->totalTaggedPhotos),  $this->locale()->toNumber($this->totalTaggedPhotos)); ?>
          </span>
				</td>

        <?php if($getEnableModuleEvent || $getEnableModuleYnEvent || $getEnableModulePageEvent || $getEnableModuleBusinessEvent || $getEnableModuleGroupEvent) :?>
					<td id="eventtag_filter" class="seaocheckinmapfilters" onclick="filterMap(5);">
						<span class="seaocheckinmapfilterscount"><?php echo $this->totalEventCount;?></span>
						<span>
							<?php echo $this->translate(array('Event Attended', 'Events Attended', $this->totalEventCount),  $this->locale()->toNumber($this->totalEventCount)); ?>
						</span>
					</td>
        <?php endif;?>
			</tr>
		</table>	
	</div>
</div>
<div class="clr"></div>
<script type="text/javascript">

  //ARRAYS TO HOLD COPIES OF THE MARKERS AND HTML USED BY THE SIDE_BAR
  //BECAUSE THE FUNCTION CLOSURE TRICK DOESNT WORK THERE
  var gmarkers = [];

  var markerClusterer = null;
  //GLOBAL "MAP" VARIABLE
  var mapCheckin = null;
  var markerCheckin=null;
  var markerCheckinLocation=null;
  //A FUNCTION TO CREATE THE MARKER AND SET UP THE EVENT WINDOW FUNCTION
  function createMarker(latlng, location, count, location_id, category) {
		var image = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chco=FFFFFF,008CFF,000000&chld='+count + '|008CFF|000000';
		var markerCheckin = new google.maps.Marker({
			position: latlng,
			map: mapCheckin,
			title: location,
			icon: image,
      count: count,
      location_id: location_id
		});
		gmarkers.push(markerCheckin);
		google.maps.event.addListener(markerCheckin, 'click', function() {
			//google.maps.event.trigger(mapCheckin, 'resize');
			mapCheckin.setCenter(markerCheckin.position);
			contentString = '<div class="sitetag_checkin_map_tip_loading"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" style="margin:10px 0;"/></center></div>';
			infoBubbles.setContent(contentString);
			infoBubbles.open(mapCheckin,markerCheckin);
			getPhotoPagination(location_id, markerCheckin, mapCheckin, location, category);
		});

  }

	function getPhotoPagination(location_id,markerCheckin,mapCheckin,location, category) {
    var request = new Request.HTML({
			url : '<?php echo $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true);?>',
			data : {
				format : 'html',
				location_id : location_id,
				location : location,
        subject:  en4.core.subject.guid,
        category: category,
        isajax:0
			},
      evalScripts: true,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				google.maps.event.trigger(mapCheckin, 'resize');
				contentString ="<div class='sitetag_checkin_map_tip_content'>" +responseHTML +"</div>";
				infoBubbles.open(mapCheckin,markerCheckin);
				infoBubbles.setContent(contentString);
				(function() {
				  Smoothbox.bind($("sitetagcheckin_feed_items"));
          en4.core.runonce.trigger();
        }).delay(500);
			}
		});
    request.send();
	}

	function clearOverlays() {
		infoBubbles.close();
		google.maps.event.trigger(mapCheckin, 'resize');

		if (gmarkers) {
			for (var i = 0; i < gmarkers.length; i++ ) {
				gmarkers[i].setMap(null);
			}
		}

    if (markerClusterer) {
		  markerClusterer.clearMarkers();
		}
	}

function initializeMap() {
            var mylatlng = new google.maps.LatLng(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.latitude', 0); ?>,<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.longitude', 0); ?>);

            //CREATE THE MAP
            var myOptions = {
                zoom: <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>,
                center: mylatlng,
                navigationControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            mapCheckin = new google.maps.Map(document.getElementById("mapsitetagcheckin_browse_map_canvas"),
            myOptions);

            google.maps.event.addListener(mapCheckin, 'click', function() {
                infoBubbles.close();
            });
            $('display_feedlinks').addEvent('click', function() {
        infoBubbles.close();
      });
            $$('.tab_layout_sitetagcheckin_map_sitetagcheckin').addEvent('click', function() {
                showMap();
                infoBubbles.close();
initializeMap();
               // google.maps.event.trigger(mapCheckin, 'resize');
//                 setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                showMap();
            });
            
            $$('.tab_button_<?php echo $this->identity?>').addEvent('click', function() {
                showMap();
                infoBubbles.close();
                google.maps.event.trigger(mapCheckin, 'resize');
//                 setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                showMap();
            });
            
            if($('tab-<?php echo $this->identity?>')) {
            $('tab-<?php echo $this->identity?>').addEvent('click', function() {
                showMap();
                infoBubbles.close();
                google.maps.event.trigger(mapCheckin, 'resize');
//                 setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                showMap();
            });
            }
            

            $$('.application').each(function(element) {
                if (element.href == "javascript:tl_manager.fireTab('<?php echo $this->identity?>')") {
                    element.addEvent('click', function() {
                        (function(){
                            showMap();
                            infoBubbles.close();
                            google.maps.event.trigger(mapCheckin, 'resize');
                            setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                            showMap();
                        }).delay(50);
                    });
                }	
            });

         filterMap('<?php echo $this->show_map_photo;?>');
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
		backgroundClassName: 'sitetag_checkin_map_tip',
		arrowStyle: 0
	});

  window.addEvent('domready',function(){
    initializeMap();
  });

  function setMapCenterZoomPoint(bounds, mapcheckin) {
    if(bounds == '')
    return;
    if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds &&  bounds.center_lat &&  bounds.center_lng) {
      mapcheckin.setCenter(new google.maps.LatLng( bounds.center_lat,  bounds.center_lng), <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
    } else {
      mapcheckin.setCenter(new google.maps.LatLng(lat, lng), <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
    }
    if (bds) {
      mapcheckin.setCenter(bds.getCenter());
      mapcheckin.fitBounds(bds);
    }
  }

	var sitetagcheckin_location_flag = false;
	document.body.addEvent('click', function(event) {
    var el = $(event.target); 
		if(el.get('for')=='location_stcheckin_photo_tooltip'){
			sitetagcheckin_location_flag = false;
			return;
		}
		if(sitetagcheckin_location_flag == false) {
			if($('setlocation_photo_suggest_tip'))
			  $('setlocation_photo_suggest_tip').destroy(); 
		}
    sitetagcheckin_location_flag = false;
	});

</script>