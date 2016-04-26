<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/list_tooltip.css');
?>

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>

<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.map.zoom', 1); ?>

<script type="text/javascript">

  var current_page = '<?php echo $this->current_page; ?>';
  var paginatePageLocations = function(page) {
		var  formElements = document.getElementById('filter_form');
		var parms = formElements.toQueryString(); 
		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&page=' + page;
		document.getElementById('page_location_loding_image').style.display ='';
    var url = en4.core.baseUrl + 'widget/index/mod/list/name/browselocation-list';
    //clearOverlays();
    gmarkers = [];
    en4.core.request.send(new Request.HTML({
      method : 'post',
			'url' : url,
			'data' : param,
      onSuccess :function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('page_location_loding_image').style.display ='none';
				document.getElementById('list_location_map_anchor').getParent().innerHTML = responseHTML;
				setMarker();
      }
    })
    );
  }
</script>
<script type="text/javascript">
  var pageAction = function(page) {
		paginatePageLocations(page);
  }
</script>

<?php if (empty($this->is_ajax)) : ?>
<div class="list_browse_location" id="list_browse_location" >
	<?php if (count($this->paginator) > 0): ?>
		<div class="list_map_container_right" id ="list_map_container_right"></div>
		<div id="list_map_container" class="list_map_container absolute" style="visibility:hidden;">
			<div class="list_map_container_topbar" id='list_map_container_topbar' style ='display:none;'>
				<a id="largemap" href="javascript:void(0);" onclick="smallLargeMap(1)" class="bold fleft">&laquo; <?php echo $this->translate('Large Map'); ?></a>
				<a id="smallmap" href="javascript:void(0);" onclick="smallLargeMap(0)" class="bold fleft"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
			</div>

			<div class="list_map_container_map_area fleft seaocore_map" id="listlocation_map">
				<div class="list_map_content" id="listlocation_browse_map_canvas" ></div>
				<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
				<?php if (!empty($siteTitle)) : ?>
				<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="list_map_container_list" id="list_content_content">
<?php endif; ?>

  <a id="list_location_map_anchor" class="pabsolute"></a>
		<?php if (count($this->paginator) > 0): ?>
			<ul class="seaocore_browse_list" id="seaocore_browse_list"><?php if (!empty($this->is_ajax)) : ?>	
				<li style="border:none"><p>
				<?php echo $this->translate(array('%s listing found.', '%s listings found.', $this->totalresults),$this->locale()->toNumber($this->totalresults)) ?>
				</p></li>
				<?php foreach ($this->paginator as $item): //print_r($this->paginator);die; ?>
				<?php if(!empty($item->location) || !empty($this->locationVariable)) : ?>
					<li>
						<div class="seaocore_browse_list_photo">
							<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location)? "marker_photo_".$item->listing_id :'un_location_list')); ?>
						</div>
		
							<div class='seaocore_browse_list_info'>
								<div class='seaocore_browse_list_info_title'>
									<span>
										<?php   if( !empty($item->closed) ): ?>
											<img alt="close" src='application/modules/List/externals/images/close.png'/>
										<?php endif;?>  
										<?php if (!empty($item->sponsored)): ?>
											<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
										<?php endif; ?>
										<?php if (!empty($item->featured)): ?>
											<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
										<?php endif; ?>
									</span>
									<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title'
										=> $item->getTitle(), 'target' => '_parent', 'class' =>!empty($item->location)? "marker_".$item->listing_id :'un_location_list')); ?>
							  </div>
								<div class='seaocore_browse_list_info_date'>
									<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.postedby', 1)):?>
										<?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
										<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
									<?php endif;?>
									<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
									<?php $itemreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('listreview'); ?>
									<?php if ($itemreviewEnabled): ?>
										<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
									<?php endif; ?>
									<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
									<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
							</div>
							<?php if((!empty($item->location) && $this->enableLocation) || (!empty($item->price) && $this->enablePrice) ): ?>
								<div class="seaocore_browse_list_info_date"><?php if(!empty($item->price) && $this->enablePrice): ?><?php echo $this->translate("Price: "); echo $this->locale()->toCurrency($item->price, $currency); ?><?php endif; ?><?php if((!empty($item->location) && $this->enableLocation) && (!empty($item->price ) && $this->enablePrice)): ?><?php echo $this->translate(", "); ?>
									<?php endif; ?>
									<?php if(!empty($item->location) && $this->enableLocation): ?>
										<?php  echo $this->translate("Location: "); echo $this->translate($item->location); ?>
											- <b>
													<?php if (!empty($this->mobile)) : ?>
														<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->listing_id, 'resouce_type' => 'list_listing', 'is_mobile' => $this->mobile), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
													<?php else: ?>
														<?php if (!empty($this->is_ajax)) : ?>
															<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
															<?php else : ?>
																<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
															<?php endif; ?>
													<?php endif; ?>
												</b>
									<?php endif; ?>
								</div>
						  <?php endif; ?>
							<?php if (!empty($item->body)): ?>
								<div class="seaocore_browse_list_info_blurb">
									<?php echo $this->viewMore($item->body) ?>
								</div>
							<?php elseif (!empty($item->description)): ?>
								<div class="seaocore_browse_list_info_blurb">
									<?php echo $this->viewMore($item->description) ?>
								</div>
							<?php endif; ?>
						</div>
					</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div class="clr list_browse_location_paging" style="margin-top:10px;">
				<?php echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "list"), array("orderby" => $this->orderby)); ?>
				<?php if( count($this->paginator) > 1 ): ?>
					<div class="fleft" id="page_location_loding_image" style="display: none;margin:5px;">
						<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
					</div>
				<?php endif; ?>
			</div>	<?php endif; ?>
		<?php else: ?>
			<div class="tip"> 
				<span><?php echo $this->translate('Nobody has created a listing with that criteria.'); ?></span>
			</div>
		<?php endif; ?>
		<?php if (empty($this->is_ajax)) : ?>	
	</div>
</div>

<script type="text/javascript" >

  /* moo style */
  window.addEvent('domready',function() {
    //smallLargeMap(1);
    var Clientwidth = $('global_content').getElement(".layout_list_browselocation_list").clientWidth;

		var offsetWidth = $('list_map_container').offsetWidth;
		$('listlocation_browse_map_canvas').setStyle("height",offsetWidth);

    if (document.getElementById("smallmap"))
    document.getElementById("smallmap").style.display = "none";
    if ($('list_map_right'))
			$('list_map_right').style.display = 'none';

    <?php if($this->paginator->count()>0):?>
			<?php if( $this->enableLocation): ?>
				initialize();
			<?php endif; ?>  
    <?php endif;?>
  });
  
	if ($('seaocore_browse_list')) {

		var elementStartY = $('listlocation_map').getPosition().x ;
		var offsetWidth = $('list_map_container').offsetWidth;
		var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);


		function setMapContent () {

			if (!$('seaocore_browse_list')) {
				return;
			}
			
			var element=$("list_map_container");
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
			var elementPostionStartX = $('list_map_container').getPosition().x ;
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

		window.addEvent('scroll', function () {
			setMapContent();
		});
		
	}

  function smallLargeMap(option) {
		if(option == '1') {
		  $('listlocation_browse_map_canvas').setStyle("height",'400px');
			document.getElementById("largemap").style.display = "none";
			document.getElementById("smallmap").style.display = "block";
			if(!$('list_map_container').hasClass('list_map_container_exp'))
				$('list_map_container').addClass('list_map_container_exp');
		} else {
		$('listlocation_browse_map_canvas').setStyle("height",offsetWidth);
			document.getElementById("largemap").style.display = "block";
			document.getElementById("smallmap").style.display = "none";
			if($('list_map_container').hasClass('list_map_container_exp'))
				$('list_map_container').removeClass('list_map_container_exp');
			
		}
		setMapContent();
		google.maps.event.trigger(map, 'resize');
	}
</script>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<script type="text/javascript">
	var script = '<script type="text/javascript" src="http://google-maps-' +
			'utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble';
	if (document.location.search.indexOf('compiled') !== -1) {
		script += '-compiled';
	}
	script += '.js"><' + '/script>';
	document.write(script);
</script>
<script type="text/javascript" >
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
      zoom: <?php echo '1';?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      //  mapTypeControl: true,
      // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    map = new google.maps.Map(document.getElementById("listlocation_browse_map_canvas"),
    myOptions);

    google.maps.event.addListener(map, 'click', function() {
      <?php if( $this->enableLocation && $this->paginator->count() > 0): ?>
				infoBubbles.close();
      <?php endif; ?>
    });
    setMarker();
    
   
  }

  function setMapCenterZoomPoint(bounds, maplocation) {
    if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds &&  bounds.center_lat &&  bounds.center_lng) {
      maplocation.setCenter(new google.maps.LatLng( bounds.center_lat,  bounds.center_lng), 4);
    } else {
      maplocation.setCenter(new google.maps.LatLng(lat, lng), 4);
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
</script>

<style type="text/css">
  #listlocation_browse_map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #listlocation_browse_map_canvas > div{
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

function setMarker() {

<?php if (count($this->locations) > 0) : ?>
<?php   foreach ($this->locations as $location) : ?>
	// obtain the attribues of each marker
	var lat = <?php echo $location->latitude ?>;
	var lng =<?php echo $location->longitude  ?>;
	var point = new google.maps.LatLng(lat,lng);
	var page_id = <?php echo $this->list[$location->listing_id]->listing_id  ?>;
	<?php if(!empty ($enableBouce)):?>
	var sponsored = <?php echo $this->list[$location->listing_id]->sponsored ?>
	<?php else:?>
	var sponsored =0;
	<?php endif; ?>
	// create the marker

	<?php $page_id = $this->list[$location->listing_id]->listing_id; ?>
	var contentString = '<div id="content">'+
		'<div id="siteNotice">'+
		'</div>'+'  <ul class="lists_locationdetails"><li>'+

		'<div class="lists_locationdetails_info_title">'+
		'<a href="<?php echo $this->url(array('listing_id' => $this->list[$location->listing_id]->listing_id, 'user_id' => $this->list[$location->listing_id]->owner_id,'slug' => $this->list[$location->listing_id]->getSlug()), 'list_entry_view', true) ?>">'+"<?php echo $this->string()->escapeJavascript( $this->list[$location->listing_id]->getTitle()); ?>"+'</a>'+
		'<div class="fright">'+
		'<span >'+
					<?php if ($this->list[$location->listing_id]->featured == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
							'</span>'+
								'<span>'+
					<?php if ($this->list[$location->listing_id]->sponsored == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
					<?php endif; ?>
				'</span>'+
			'</div>'+
		'<div class="clr"></div>'+
		'</div>'+
		
		'<div class="lists_locationdetails_photo" >'+
				'<?php echo $this->htmlLink($this->list[$location->listing_id]->getHref(), $this->itemPhoto($this->list[$location->listing_id], 'thumb.normal')) ?>'+
				'</div>'+
		'<div class="lists_locationdetails_info">'+

		<?php if ($this->ratngShow): ?>
			<?php if (($this->list[$location->listing_id]->rating > 0)): ?>
					'<span class="clr">'+
					<?php for ($x = 1; $x <= $this->list[$location->listing_id]->rating; $x++): ?>
							'<span class="rating_star_generic rating_star"></span>'+
					<?php endfor; ?>
					<?php if ((round($this->list[$location->listing_id]->rating) - $this->list[$location->listing_id]->rating) > 0): ?>
							'<span class="rating_star_generic rating_star_half"></span>'+
					<?php endif; ?>
							'</span>'+
			<?php endif; ?>
		<?php endif; ?>
					'<div class="lists_locationdetails_info_date">'+
						"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?> "+
						<?php //if (!empty($this->getdirection)) : ?>
						<?php //echo  $this->htmlLink(array('route' => 'list_viewmap', 'controller' => 'index', 'action' => 'view-map', 'id' => $location->listing_id), $this->translate('Get Direction'), array('class' => 'smoothbox')) ?>
							'<?php //echo $this->htmlLink("https://maps.google.com/?daddr=".urlencode($location->location), $this->translate("Get Direction"), array('target' => 'blank')) ?>'
					  <?php //endif; ?>
					'</div>'+
					'</div>'+
					'<div class="clr"></div>'+
					' </li></ul>'+
					'</div>';
				var marker = createMarker(point,sponsored,contentString,"<?php echo str_replace('"',' ',$this->list[$location->listing_id]->getTitle()); ?>", page_id);

<?php   endforeach; ?>
$('list_map_container').style.display = 'block';
google.maps.event.trigger(map, 'resize');
<?php else: ?>
$('list_map_container').style.display = 'none';
<?php endif; ?>
//  markerClusterer = new MarkerClusterer(map, gmarkers, {
//  });
<?php if (!empty($this->locations)): ?>
	setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,map);
<?php endif; ?>

 //$$('.un_location_list').each(function(el) { 
   $$('.un_location_list').addEvent('mouseover',function(event) {
    infoBubbles.close();
    });
  //  });
}
</script>