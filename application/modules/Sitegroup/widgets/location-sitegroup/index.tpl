<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
	$siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');
  $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
  $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);
  $sitegroup = $this->sitegroup;  
  if($postedBy) {
		$textPostedBy = $this->string()->escapeJavascript($this->translate('created by'));
		$textPostedBy.= " " . $this->htmlLink($sitegroup->getOwner()->getHref(), $this->string()->escapeJavascript($sitegroup->getOwner()->getTitle()));
	}
?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php if (!empty($this->multiple_location)) : ?>
<script type="text/javascript">
  function smallLargeMap(option, location_id) {
		if(option == '1') {
		  $('map_canvas_sitegroup_browse_'+ location_id).setStyle("height",'300px');
		  $('map_canvas_sitegroup_browse_' + location_id).setStyle("width",'550px');
			$('sitegroup_location_fields_map_wrapper_' + location_id).className='sitegroup_location_fields_map_wrapper fright seaocore_map map_wrapper_extend';	
			$('map_canvas_sitegroup_browse_' + location_id).className='sitegroup_location_fields_map_canvas map_extend';			
			document.getElementById("largemap_" + location_id).style.display = "none";
			document.getElementById("smallmap_" + location_id).style.display = "block";
		} else {
			  $('map_canvas_sitegroup_browse_'+ location_id).setStyle("height",'200px');
		  $('map_canvas_sitegroup_browse_' + location_id).setStyle("width",'200px');
			$('sitegroup_location_fields_map_wrapper_' + location_id).className='sitegroup_location_fields_map_wrapper fright seaocore_map';	
			$('map_canvas_sitegroup_browse_' + location_id).className='sitegroup_location_fields_map_canvas';			
			document.getElementById("largemap_" + location_id ).style.display = "block";
			document.getElementById("smallmap_" + location_id).style.display = "none";	
		}
		//setMapContent();
	//	google.maps.event.trigger(map, 'resize');
	}
</script>
	<script type="text/javascript">
		var current_group = '<?php echo $this->current_group; ?>';
		var paginateGroupLocations = function(group) {
			document.getElementById('group_location_loding_image').style.display ='';
			var url = en4.core.baseUrl + 'widget/index/mod/sitegroup/name/location-sitegroup';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'is_ajax' : 1,
					'subject' : en4.core.subject.guid,
					'group' : group
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('group_location_loding_image').style.display ='none';
					document.getElementById('sitegroup_location_map_anchor').getParent().innerHTML = responseHTML;
					en4.core.runonce.trigger();
				}
			}));
		}

		var pageAction = function(group) {
			paginateGroupLocations(group);
		}
	</script>

	<?php if (empty($this->is_ajax)) : ?>
		<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>
	<?php endif; ?>

	<script type="text/javascript">
		var myLatlng;
		function initialize(latitude, longitude, location_id) {
			var myLatlng = new google.maps.LatLng(latitude,longitude);
		
			var myOptions = {
				zoom: 10,
				center: myLatlng,
			//  navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			var map = new google.maps.Map(document.getElementById("map_canvas_sitegroup_browse_"+location_id), myOptions);

			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: "<?php echo str_replace('"', ' ',$sitegroup->getTitle())?>"
			});

			<?php if(!empty($this->showtoptitle)):?>
				$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
			<?php else:?>
				$$('.tab_layout_sitegroup_location_sitegroup').addEvent('click', function() {
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
			<?php endif;?>

      document.getElementById("largemap_" + location_id).addEvent('click', function() {
        smallLargeMap(1,location_id);
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
      document.getElementById("smallmap_" + location_id).addEvent('click', function() {
         smallLargeMap(0,location_id);
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});

			google.maps.event.addListener(map, 'click', function() {
				//infowindow.close();
				google.maps.event.trigger(map, 'resize');
				map.setZoom(10);
				map.setCenter(myLatlng);
			});
		}
	</script>
		
	<?php foreach ($this->location as $item):  ?>
		<script type="text/javascript">
			en4.core.runonce.add(function() {
				window.addEvent('domready',function(){
					initialize('<?php echo $item->latitude ?>','<?php echo $item->longitude ?>','<?php echo $item->location_id ?>');
					});
			});
		</script>
	<?php endforeach; ?>
	
	<?php if(!empty($this->MainLocationObject)) : ?>
		<script type="text/javascript">
			en4.core.runonce.add(function() {
				window.addEvent('domready',function(){
					initialize('<?php echo $this->MainLocationObject->latitude ?>','<?php echo $this->MainLocationObject->longitude ?>','<?php echo $this->MainLocationObject->location_id ?>');
				});
			});
		</script>
	<?php endif; ?>

		
		<a id="sitegroup_location_map_anchor" class="pabsolute"></a>
		<?php if (empty($this->is_ajax)) : ?>
			<div id='id_<?php echo $this->content_id; ?>'>
		<?php endif; ?>
		
		<?php if($this->showtoptitle == 1):?>
			<div class="layout_simple_head" id="layout_map">
				<?php echo $this->translate($this->sitegroup->getTitle());?><?php echo $this->translate("'s Map");?>
			</div>
		<?php endif;?>
		
		<div class="layout_middle">
		<?php if (!empty($this->isManageAdmin)) : ?>
			<div class='clr seaocore_add '>
				<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'action' => 'add-location', 'tab' => $this->identity_temp), $this->translate('Add Location'), array('class' => 'smoothbox icon_sitegroups_map_add buttonlink fright')); ?>
			</div>
		<?php endif; ?>
		
				
		
		<?php if (!empty($this->sitegroup->location) && !empty($this->MainLocationObject)) : ?>
		<div class='profile_fields sitegroup_location_fields sitegroup_list_highlight'>
				<div class="sitegroup_location_fields_head">
					<?php if (!empty($this->MainLocationObject->locationname)) : ?>
						<?php echo $this->MainLocationObject->locationname ?>
					<?php else: ?>
						<?php echo $this->translate('Main Location'); ?>
					<?php endif; ?>
				</div>
				<div class="sitegroup_location_fields_map_wrapper fright seaocore_map" id="sitegroup_location_fields_map_wrapper_<?php echo $this->MainLocationObject->location_id ?>">
					<div class="sitegroup_location_fields_map b_dark">
						<div class="sitegroup_map_container_topbar b_dark" id='sitegroup_map_container_topbar' >
							<a id="largemap_<?php echo $this->MainLocationObject->location_id ?>" href="javascript:void(0);"  class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $this->MainLocationObject->location_id ?>" href="javascript:void(0);"  class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
						<div class="sitegroup_location_fields_map_canvas" id="map_canvas_sitegroup_browse_<?php echo $this->MainLocationObject->location_id ?>" style="width:200px;"></div>
					</div>
				</div>
					
					<ul class="sitegroup_location_fields_details">
						<li>
							<span><?php echo $this->translate('Location:'); ?> </span>
							<span><b><?php echo  $this->MainLocationObject->location; ?></b> - <span class="location_get_direction"><b>
								<?php if (!empty($this->mobile)) : ?>
								<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->MainLocationObject->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $this->MainLocationObject->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
								<?php else: ?>
								<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->MainLocationObject->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $this->MainLocationObject->location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
								<?php endif; ?>
								</b></span>
							</span>
						</li>
						<?php if(!empty($this->MainLocationObject->formatted_address)):?>
							<li>
								<span><?php echo $this->translate('Formatted Address:'); ?> </span>
								<span><?php echo $this->MainLocationObject->formatted_address; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->address)):?>
							<li>
								<span><?php echo $this->translate('Street Address:'); ?> </span>
								<span><?php echo $this->MainLocationObject->address; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->city)):?>
							<li>
								<span><?php echo $this->translate('City:'); ?></span>
								<span><?php echo $this->MainLocationObject->city; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->zipcode)):?>
							<li>
								<span><?php echo $this->translate('Zipcode:'); ?></span>
								<span><?php echo $this->MainLocationObject->zipcode; ?> </span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->state)):?>
							<li>
								<span><?php echo $this->translate('State:'); ?></span>
								<span><?php echo $this->MainLocationObject->state; ?></span>
							</li>
						<?php endif; ?>
						<?php if(!empty($this->MainLocationObject->country)):?>
							<li>
								<span><?php echo $this->translate('Country:'); ?></span>
								<span><?php echo $this->MainLocationObject->country; ?></span>
							</li>
						<?php endif; ?>
						<?php if (!empty($this->isManageAdmin)) : ?>
							<li class='sitegroup_location_fields_option clr'>
								<?php if (empty($this->is_ajax)): ?>
									<?php //echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'edit-address', 'tab' => $this->identity_temp), $this->translate('Edit Address'), array('class' => 'smoothbox icon_sitegroups_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitegroups_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('class' => 'smoothbox icon_sitegroups_map_delete buttonlink ')); ?>
								<?php else: ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitegroups_map_edit buttonlink ')); ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $this->MainLocationObject->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('onclick' => 'owner(this);return false', 'class' => 'smoothbox icon_sitegroups_map_delete buttonlink ')); ?>
								<?php endif; ?>
							</li>
						<?php endif; ?>
					</ul>
				<div class="clr"></div>
				</div>
				
		<?php endif; ?>

		<?php foreach ($this->location as $item): ?>
			<div class='profile_fields sitegroup_location_fields'>
				<h4>
					<span>
					<?php if (!empty($item->locationname)) : ?>
						<?php echo $item->locationname ?>
					<?php else: ?>
						<?php echo $this->translate('Location Information'); ?>
					<?php endif; ?>
					</span>
				</h4>
				<div class="sitegroup_location_fields_map_wrapper fright seaocore_map" id="sitegroup_location_fields_map_wrapper_<?php echo $item->location_id ?>">
					<div class="sitegroup_location_fields_map b_dark">
						<div class="sitegroup_map_container_topbar b_dark" id='sitegroup_map_container_topbar' >
							<a id="largemap_<?php echo $item->location_id ?>" href="javascript:void(0);"  class="bold fright">&laquo; <?php echo $this->translate('Large Map'); ?></a>
							<a id="smallmap_<?php echo $item->location_id ?>" href="javascript:void(0);"  class="bold fright" style="display:none"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
						</div>
						<div class="sitegroup_location_fields_map_canvas" id="map_canvas_sitegroup_browse_<?php echo $item->location_id ?>" style="width:200px;"></div>
					</div>
				</div>
				<ul class="sitegroup_location_fields_details">
					<li>
						<span><?php echo $this->translate('Location:'); ?> </span>
						<span><b><?php echo  $item->location; ?></b> - <span class="location_get_direction"><b>
							<?php if (!empty($this->mobile)) : ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $item->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
							<?php else: ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $item->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $item->location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
							<?php endif; ?>
							</b></span>
						</span>
					</li>
					<?php if(!empty($item->formatted_address)):?>
						<li>
							<span><?php echo $this->translate('Formatted Address:'); ?> </span>
							<span><?php echo $item->formatted_address; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($item->address)):?>
						<li>
							<span><?php echo $this->translate('Street Address:'); ?> </span>
							<span><?php echo $item->address; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($item->city)):?>
						<li>
							<span><?php echo $this->translate('City:'); ?></span>
							<span><?php echo $item->city; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($item->zipcode)):?>
						<li>
							<span><?php echo $this->translate('Zipcode:'); ?></span>
							<span><?php echo $item->zipcode; ?> </span>
						</li>
					<?php endif; ?>
					<?php if(!empty($item->state)):?>
						<li>
							<span><?php echo $this->translate('State:'); ?></span>
							<span><?php echo $item->state; ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($item->country)):?>
						<li>
							<span><?php echo $this->translate('Country:'); ?></span>
							<span><?php echo $item->country; ?></span>
						</li>
					<?php endif; ?>
					<?php if (!empty($this->isManageAdmin)) : ?>
						<li class='sitegroup_location_fields_option clr'>
							<?php if (empty($this->is_ajax)): ?>
								<?php //echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'edit-address', 'tab' => $this->identity_temp), $this->translate('Edit Address'), array('class' => 'smoothbox icon_sitegroups_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitegroups_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('class' => 'smoothbox icon_sitegroups_map_delete buttonlink ')); ?>
							<?php else: ?>
								<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'edit-location'), $this->translate('Edit Location'), array('class' => 'icon_sitegroups_map_edit buttonlink ')); ?>
								<?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->sitegroup->group_id, 'location_id' => $item->location_id, 'action' => 'delete-location', 'tab' => $this->identity_temp), $this->translate('Delete'), array('onclick' => 'owner(this);return false', 'class' => 'smoothbox icon_sitegroups_map_delete buttonlink ')); ?>
							<?php endif; ?>
						</li>
					<?php endif; ?>
				</ul>
			</div>	
		<?php endforeach; ?>
		
		<div class="clr sitegroup_browse_location_paging" style="margin-top:10px;">
			<?php echo $this->paginationControl($this->location, null, array("pagination/pagination.tpl", "sitegroup"), array("orderby" => $this->orderby)); ?>
			<?php if( count($this->location) > 1 ): ?>
				<div class="fleft" id="group_location_loding_image" style="display: none;margin:5px;">
				<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
				</div>
			<?php endif; ?>
		</div>
	<?php if (empty($this->is_ajax)) : ?>
	</div>
	<?php endif; ?>
</div>

<?php else: ?>
<script type="text/javascript">
	var group_communityads;
			var contentinformtion;
			var group_showtitle;
	if(contentinformtion == 0) {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}		
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
		}	
		
		if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
		}	
	}
</script>

<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>


<script type="text/javascript">
  var myLatlng;
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?> ,
      center: myLatlng,
    //  navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas_sitegroup_browse"), myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+'<ul class="sitegroups_locationdetails"><li>'+
			'<div class="sitegroups_locationdetails_info_title">'+
	    	"<?php echo $this->string()->escapeJavascript($sitegroup->getTitle())?>"+
        '<div class="fright">'+
          '<span >'+
            <?php if ($sitegroup->featured == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
          '</span>'+
          '<span>'+
            <?php if ($sitegroup->sponsored == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
          	<?php endif; ?>
          '</span>'+
        '</div>'+
        '<div class="clr"></div>'+
      '</div>'+
      '<div class="sitegroups_locationdetails_photo" >'+
    		'<?php echo  $this->itemPhoto($sitegroup, 'thumb.normal') ?>'+
	    '</div>'+ 
      '<div class="sitegroups_locationdetails_info">'+
				<?php if ($this->ratngShow): ?>
					<?php if (($sitegroup->rating > 0)): ?>

						<?php 
							$currentRatingValue = $sitegroup->rating;
							$difference = $currentRatingValue- (int)$currentRatingValue;
							if($difference < .5) {
								$finalRatingValue = (int)$currentRatingValue;
							}
							else {
								$finalRatingValue = (int)$currentRatingValue + .5;
							}	
						?>

						'<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">'+
							<?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
								'<span class="rating_star_generic rating_star" ></span>'+
							<?php endfor; ?>
							<?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
								'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
						'</span>'+
					<?php endif; ?>
				<?php endif; ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?>
          '<div class="sitegroups_locationdetails_info_date">'+
            '<?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('created by')); ?> '+
            '<?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $this->string()->escapeJavascript($sitegroup->getOwner()->getTitle())) ?>'+
          '</div>'+
        <?php endif; ?>
        '<div class="sitegroups_locationdetails_info_date">'+
	      	'<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count))) ?>,&nbsp;'+
	        '<?php echo $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count))) ?>'+
		    '</div>'+
				'<div class="sitegroups_locationdetails_info_date">'+
				<?php if (!empty($sitegroup->phone)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $sitegroup->phone ?><br />"+
				<?php endif; ?>
				<?php if (!empty($sitegroup->email)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Email: ")) . $sitegroup->email ?><br />"+
				<?php endif; ?>
				<?php if (!empty($sitegroup->website)): ?>
				"<?php  echo  $this->string()->escapeJavascript($this->translate("Website: ")) .$sitegroup->website ?>"+
				<?php endif; ?>
				'</div>'+
        <?php if($sitegroup->price && $this->enablePrice): ?>
                '<div class="sitegroups_locationdetails_info_date">'+
								"<i><b>"+"<?php echo  $this->locale()->toCurrency($sitegroup->price, $currency) ?>"+ "</b></i>"+
							'</div>'+
        <?php endif; ?>
			'<div class="sitegroups_locationdetails_info_date">'+
				"<i><b>"+"<?php echo $this->string()->escapeJavascript( $this->location->location); ?>"+ "</b></i>"+
	      '</div>'+
        
 		  '</div>'+
 		  '<div class="clr"></div>'+
	 ' </li></ul>'+


      '</div>';
   
    var infowindow = new google.maps.InfoWindow({
      content: contentString ,
      size: new google.maps.Size(250,50)

    });

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ',$sitegroup->getTitle())?>"
    });
    google.maps.event.addListener(marker, 'click', function() {
       
      infowindow.open(map,marker);
    });

    <?php if(!empty($this->showtoptitle)):?>
      $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
    <?php else:?>
      $$('.tab_layout_sitegroup_location_sitegroup').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
    <?php endif;?>

    google.maps.event.addListener(map, 'click', function() {
      
      infowindow.close();
			google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
  }
</script>

<div id='id_<?php echo $this->content_id; ?>'>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_map">
      <?php echo $this->translate($this->sitegroup->getTitle());?><?php echo $this->translate("'s Map");?>
		</div>
	<?php endif;?>

	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adlocationwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
			<div class="layout_right" id="communityad_location">
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adlocationwidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'group_location'))?>
			</div>
	
		<div class="layout_middle">
	<?php endif;?>
			<div class='profile_fields'>
			  <ul class="sitegroup_profile_location">
			    <li class="seaocore_map">
			  		<div id="map_canvas_sitegroup_browse"></div>
			  			<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
							<?php if (!empty($siteTitle)) : ?>
							<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
							<?php endif; ?>
			    </li>
			  </ul>
			  <h4>
			    <span><?php echo $this->translate('Location Information') ?></span>
			  </h4>
			  <ul>
			    <li>
		        <span><?php echo $this->translate('Location:'); ?> </span>
		        <span><b><?php echo  $this->location->location; ?></b> - <b>
              <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->group_id, 'resouce_type' => 'sitegroup_group'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
		        </b></span>
			    </li>
			    <?php if(!empty($this->location->formatted_address)):?>
				    <li>
				      <span><?php echo $this->translate('Formatted Address:'); ?> </span>
				      <span><?php echo $this->location->formatted_address; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->address)):?>
				    <li>
				      <span><?php echo $this->translate('Street Address:'); ?> </span>
				      <span><?php echo $this->location->address; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->city)):?>
				    <li>
				      <span><?php echo $this->translate('City:'); ?></span>
				      <span><?php echo $this->location->city; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->zipcode)):?>
				    <li>
				      <span><?php echo $this->translate('Zipcode:'); ?></span>
				      <span><?php echo $this->location->zipcode; ?> </span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->state)):?>
				    <li>
				      <span><?php echo $this->translate('State:'); ?></span>
				      <span><?php echo $this->location->state; ?></span>
				    </li>
			    <?php endif; ?>
			    <?php if(!empty($this->location->country)):?>
						<li>
						  <span><?php echo $this->translate('Country:'); ?></span>
						  <span><?php echo $this->location->country; ?></span>
						</li>
			    <?php endif; ?>
			  </ul>
			</div>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adlocationwidget', 3)  && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
		</div>
	<?php endif; ?>
</div>

<style type="text/css">
  #map_canvas_sitegroup_browse {
    width: 100%;
    height: 500px;
  
  }
  #map_canvas_sitegroup_browse > div{
    height: 500px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>
<script type="text/javascript">
	window.addEvent('domready',function(){
		initialize();
	});
</script>
<?php endif; ?>

<?php if (empty($this->is_ajax)) : ?>
	<script type="text/javascript">
		var group_communityad_integration = '<?php echo $group_communityad_integration; ?>';
		var adwithoutpackage = '<?php echo Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup) ?>';
		var location_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adlocationwidget', 3);?>';
		$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
		{ 
			if(group_showtitle != 0) {
				if($('profile_status')) {
					$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Map');?></h2>";	
				}
				if($('layout_map')) {
					$('layout_map').style.display = 'block';
				}	    	
			}   
			hideWidgetsForModule('sitegrouplocation');
			if($('id_' + <?php echo $this->content_id ?>))
			$('id_' + <?php echo $this->content_id ?>).style.display = "block";
			if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
				$('global_content').getElement('.'+ prev_tab_class).style.display = 'none';
        //scrollToTopForGroup($('id_<?php echo $this->content_id; ?>'));
			}
			prev_tab_id = '<?php echo $this->content_id; ?>';	
			prev_tab_class = 'layout_sitegroup_location_sitegroup';

			if(group_showtitle == 1 && group_communityads == 1 && location_ads_display != 0 && group_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForGroup();  	
			} else if(group_showtitle == 0 && group_communityads == 1 && location_ads_display != 0 && group_communityad_integration != 0 && adwithoutpackage != 0) {
					setLeftLayoutForGroup(1); 
			}
			
			if(group_communityads == 1 && location_ads_display == 0) {
				setLeftLayoutForGroup();  	
			}
      if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitegroup_location_sitegroup')==null)){
      scrollToTopForGroup($("global_content").getElement(".layout_sitegroup_location_sitegroup"));
    }
		});
	</script>
<?php endif; ?>