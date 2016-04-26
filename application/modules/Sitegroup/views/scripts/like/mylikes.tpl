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

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php
	$sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
	if ($sitegroupOfferEnabled) {
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/css.php?request=/application/modules/Sitegroupoffer/externals/styles/style_sitegroupoffer.css');
	}
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');
	$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitegroup';
	$RESOURCE_TYPE = 'sitegroup_group';
	$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.sponsored', 1);
	$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admylikes', 3) && $group_communityad_integration): ?>
  <div class="layout_right" id="communityad_mylikelist">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admylikes', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_likelist'))?>
  </div>
<?php endif; ?>

<script>
  var sitegroups_likes = function(resource_id, resource_type) {
		var content_type = 'sitegroup';
		// SENDING REQUEST TO AJAX
		var request = createLikegroup(resource_id, resource_type,content_type);
		// RESPONCE FROM AJAX
		request.addEvent('complete', function(responseJSON) {
			if (responseJSON.error_mess == 0) {
				$(resource_id).style.display = 'block';
				if(responseJSON.like_id )
				{
					$('backgroundcolor_'+ resource_id).className ="sitegroup_browse_thumb sitegroup_browse_liked";
					$('sitegroup_like_'+ resource_id).value = responseJSON.like_id;
					$('sitegroup_most_likes_'+ resource_id).style.display = 'none';
					$('sitegroup_unlikes_'+ resource_id).style.display = 'block';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}
				else
				{  $('backgroundcolor_'+ resource_id).className ="sitegroup_browse_thumb";
					$('sitegroup_like_'+ resource_id).value = 0;
					$('sitegroup_most_likes_'+ resource_id).style.display = 'block';
					$('sitegroup_unlikes_'+ resource_id).style.display = 'none';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}
			}
			else {
				en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
				return;
			}
		});
	}
	// FUNCTION FOR CREATING A FEEDBACK
	var createLikegroup = function( resource_id, resource_type, content_type )
	{
		if($('sitegroup_most_likes_'+ resource_id).style.display == 'block')
			$('sitegroup_most_likes_'+ resource_id).style.display='none';
		if($('sitegroup_unlikes_'+ resource_id).style.display == 'block')
			$('sitegroup_unlikes_'+ resource_id).style.display='none';
			$(resource_id).style.display='none';
			$('show_like_button_child_'+ resource_id).style.display='block';
		if (content_type == 'sitegroup') {
			var like_id = $(content_type + '_like_'+ resource_id).value
		}
		var url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitegroup_like', true);?>';
		var request = new Request.JSON({
			url : url,
			data : {
				format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,
				'like_id' : like_id
			}
		});
		request.send();
		return request;
	}
</script>
<div class="layout_middle">
<?php if ($this->paginator->count() > 0): ?>
<?php  $latitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.latitude', 0); ?>
<?php  $longitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.longitude', 0); ?>
<?php  $defaultZoom=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.zoom', 1); ?>
 <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)):  ?>
  
	<div class="sitegroup_view_select">
    <h3 class="fleft"><?php echo $this->translate('Groups I Like'); ?></h3>
		<?php  if( $this->enableLocation  && $this->map_view): ?>
			<span class="seaocore_tab_select_wrapper fright">
				<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
        <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2)"></span>
			</span>
		<?php endif;?>
    <?php  if( $this->grid_view): ?>
		<span class="seaocore_tab_select_wrapper fright">
			<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
      <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1)"></span>
		</span>
    <?php endif;?>
     <?php  if( $this->list_view): ?>
		<span class="seaocore_tab_select_wrapper fright">
			<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
      <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0)"></span>
		</span>
    <?php endif; ?>
	</div>
 <?php endif; ?>
  <?php if( $this->list_view): ?>
<div id="grid_view" style="display: none;">
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitegroup): ?>
				<li>
					<div class='seaocore_browse_list_photo'>
						<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id ,$sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.normal')) ?>
					</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<span>
								<?php if( $sitegroup->closed ): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>

								<?php endif;?>
								<?php if ($sitegroup->sponsored == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
								<?php endif; ?>
								<?php if ($sitegroup->featured == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								<?php endif; ?>
							</span>
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

										<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
										<?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
										<span class="rating_star_generic rating_star" ></span>
										<?php endfor; ?>
										<?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
											<span class="rating_star_generic rating_star_half" ></span>
										<?php endif; ?>
									<?php endif; ?>
								</span>
							<?php endif; ?>
							<h3><?php  echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id,$sitegroup->getSlug()), $sitegroup->getTitle()); ?></h3>
						</div>
						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
							<?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) ?>,
							<?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>,
						    <?php if ($this->ratngShow): ?>
								<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>,
							<?php endif; ?>
							<?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>
							
						</div>
						<?php
							$user = Engine_Api::_()->user()->getUser($sitegroup->owner_id);
							$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'contact_detail');
							$availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');
							$options_create = array_intersect_key($availableLabels, array_flip($view_options));
						?>
						 <?php $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'contact');
						if(!empty($isManageAdmin)): ?>
						<div class="seaocore_browse_list_info_date">
							<?php if( isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?><?php if($sitegroup->phone):?>
								<?php echo $this->translate('Phone: '); ?><?php echo $sitegroup->phone ?><?php endif; ?><?php endif; ?><?php if( isset($options_create['email']) && $options_create['email'] == 'Email'):?><?php if($sitegroup->email):?><?php if( !empty($sitegroup->phone) && in_array("Phone",$options_create )):?>, <?php endif; ?><?php echo $this->translate('Email: '); ?><a href='mailto:<?php echo $sitegroup->email ?>'><?php echo $sitegroup->email ?></a><?php endif; ?><?php endif; ?><?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?><?php if($sitegroup->website):?><?php if( ($sitegroup->email && in_array("Email",$options_create )) || !empty($sitegroup->phone) && in_array("Phone",$options_create ) ):?>,&nbsp;<?php endif; ?><?php echo $this->translate('Website: '); ?><?php if(strstr($sitegroup->website, 'http://') || strstr($sitegroup->website, 'https://')):?><a href='<?php echo $sitegroup->website ?>' target="_blank"><?php echo $sitegroup->website ?></a><?php else:?><a href='http://<?php echo $sitegroup->website ?>' target="_blank"><?php echo $sitegroup->website ?></a><?php endif;?><?php endif; ?><?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if((!empty($sitegroup->location) && $this->enableLocation) || (!empty($sitegroup->price) && $this->enablePrice) ): ?>
							<div class="seaocore_browse_list_info_date"><?php if(!empty($sitegroup->price) && $this->enablePrice): ?><?php echo $this->translate("Price: "); echo $this->locale()->toCurrency($sitegroup->price, $currency); ?><?php endif; ?><?php if((!empty($sitegroup->location) && $this->enableLocation) && (!empty($sitegroup->price ) && $this->enablePrice)): ?><?php echo $this->translate(", "); ?>
                <?php endif; ?>
                 <?php if(!empty($sitegroup->location) && $this->enableLocation): ?>
								<?php  echo $this->translate("Location: "); echo $this->translate($sitegroup->location); ?>&nbsp;-
								<b><?php $locationId = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitegroup->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $locationId, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
                <?php endif; ?>
							</div>
						<?php endif; ?>
						<div class='seaocore_browse_list_info_blurb'>
							<?php echo $this->viewMore(nl2br($sitegroup->body),200,5000) ?>
						</div>

						<?php if(!empty($this->sitegroupOfferEnabled) && !empty($sitegroup->offer)): ?>
							<?php echo $sitegroup->getOffer(); ?>
            <?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
  <?php endif; ?>
  <?php  if( $this->grid_view):?>
	<div id="image_view" style="display: none;">
  <div class="sitegroup_img_view">
			<?php $counter=1; foreach ($this->paginator as $sitegroup): ?>
			<?php
				$likeGroup=false;
				if(!empty($viewer_id)):
				$likeGroup=Engine_Api::_()->sitegroup()->hasGroupLike($sitegroup->group_id,$viewer_id);
				endif;
			?>
      <div class="sitegroup_browse_thumb <?php if($likeGroup): ?> sitegroup_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitegroup->group_id; ?>" >
      	<div class="sitegroup_browse_thumb_list" <?php if(!empty($viewer_id)) : ?> onmouseOver="$('like_<?php echo $sitegroup->getIdentity(); ?>').style.display='block'; if($('<?php echo $sitegroup->getIdentity(); ?>').style.display=='none')$('<?php echo $sitegroup->getIdentity(); ?>').style.display='block';"  onmouseout="$('like_<?php echo $sitegroup->getIdentity(); ?>').style.display='none'; $('<?php echo $sitegroup->getIdentity(); ?>').style.display='none';" <?php endif; ?> >
					<a href="javascript:void(0);">
						<?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png'; $temp_url=$sitegroup->getPhotoUrl('thumb.profile'); if(!empty($temp_url)): $url=$sitegroup->getPhotoUrl('thumb.profile'); endif;?>
						<span style="background-image: url(<?php echo $url; ?>);"> </span>
	        </a>
					
					<?php if(!empty($viewer_id)) : ?>
            <div id="like_<?php echo $sitegroup->getIdentity() ?>" style="display:none;">
							<?php
							$RESOURCE_ID = $sitegroup->getIdentity(); ?>
							<div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
								<?php
								// Check that for this 'resurce type' & 'resource id' user liked or not.
								$check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
								if( !empty($check_availability) )
								{
									$label = 'Unlike this';
									$unlike_show = "display:block;";
									$like_show = "display:none;";
									$like_id = $check_availability[0]['like_id'];
								}
								else
								{
									$label = 'Like this';
									$unlike_show = "display:none;";
									$like_show = "display:block;";
									$like_id = 0;
								}
								//}
								?>
								<div class="sitegroup_browse_thumb_hover_color"></div>

								<div class="seaocore_like_button sitegroup_browse_thumb_hover_unlike_button" id="sitegroup_unlikes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $unlike_show;?>' >
									<a href = "javascript:void(0);" onclick = "sitegroups_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
									<i class="seaocore_like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
									</a>
								</div>

								<div class="seaocore_like_button sitegroup_browse_thumb_hover_like_button" id="sitegroup_most_likes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $like_show;?>'>
									<a href = "javascript:void(0);" onclick = "sitegroups_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
										<i class="seaocore_like_thumbup_icon"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
								</div>

								<input type ="hidden" id = "sitegroup_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />

							</div>
             </div>
							<div id = "show_like_button_child_<?php echo $RESOURCE_ID;?>" style="display:none;" >
								<div class="sitegroup_browse_thumb_hover_color"></div>
								<div class="sitegroup_browse_thumb_hover_loader">
									<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" class="mtop5" />
								</div>
							</div>
					<?php endif; ?>
					<?php if ($sitegroup->featured == 1): ?>
						<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured')?>"></span>
					<?php endif; ?>
					<div class="sitegroup_browse_title">
          	<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id,$sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(),15)); ?>
					</div>	
       </div>
			 	<?php if ($sitegroup->sponsored == 1): ?>
					<div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
							<?php echo $this->translate('SPONSORED'); ?>                 
					</div>
				<?php endif; ?>

				<div class='sitegroup_browse_thumb_info'>
					<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
						if ($sitegroup->member_title && $memberTitle) : ?>
							<div class="member_count">
									<?php echo $sitegroup->member_count . ' ' .  ucfirst($sitegroup->member_title); ?>
							</div>
						<?php else : ?>
							<div class="member_count">
								<?php echo $this->translate(array('%s '. ucfirst('member'), '%s '. ucfirst('members'), $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
							</div> 
						<?php endif; ?>
					<?php endif; ?>


					<div class='sitegroup_browse_thumb_stats seaocore_txt_light'>
							<?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
							<?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) ?>
					</div>

					<div class='sitegroup_browse_thumb_stats seaocore_txt_light'>
						<?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>,
						<?php if ($this->ratngShow): ?>
								<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>,
							<?php endif; ?>
							<?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>
							
					</div>
					<?php if(!empty($sitegroup->price) && $this->enablePrice): ?>
						<div class='sitegroup_browse_thumb_stats seaocore_txt_light'>
							<?php  echo $this->translate("Price: "); echo $this->locale()->toCurrency($sitegroup->price, $currency);?>
						</div>
					<?php  endif;?>
				</div>
      </div>
		<?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
<div id="map_canvas_view" style="display: none;">
  <div id="map_canvas"> </div>
  <?php if( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
  	<a href="javascript:void(0);" onclick="toggleBounce()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
  <?php endif;?>
</div>
<div class="clr"></div>
<?php echo $this->paginationControl($this->paginator); ?>
  <?php else: ?>
	<div class="sitegroup_view_select">
	<h3 class="fleft"><?php echo $this->translate('Groups I Like'); ?></h3>
  </div>
  <div class="tip">
  		<span>
  		<?php echo $this->translate('You have not liked any groups yet. %1$sExplore groups%2$s and Like the groups you like.', '<a href="'.$this->url(array('action' => 'index'), 'sitegroup_general').'">', '</a>'); ?>
  		
			<?php //$translategroup = "<a href=".$this->url(array('action' => 'index'), 'sitegroup_general', true).">" . $this->translate("Explore groups") . "</a>";
			//echo $this->translate("You have not liked any groups yet. %s and Like the groups you like.", $translategroup);?>
		</span>
	</div>
  <?php endif; ?>
</div>
<style type="text/css">
  #map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #map_canvas > div{
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

<script type="text/javascript" >
  function switchview(flage){
    if(flage==2){
      if($('map_canvas_view')){
      $('map_canvas_view').style.display='block';
      <?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $defaultZoom; ?>);
      map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude; ?>));
      <?php endif; ?>
      if($('grid_view'))
      $('grid_view').style.display='none';
      if($('image_view'))
      $('image_view').style.display='none';
    }
    }else if(flage==1){
      if($('image_view')){
      if($('map_canvas_view'))
      $('map_canvas_view').style.display='none';
      if($('grid_view'))
      $('grid_view').style.display='none';
      $('image_view').style.display='block';
      }
    }else{
      if($('grid_view')){
      if($('map_canvas_view'))
      $('map_canvas_view').style.display='none';
      $('grid_view').style.display='block';
      if($('image_view'))
      $('image_view').style.display='none';
      }
    }
  }

  /* moo style */
  en4.core.runonce.add(function() {
    //opacity / display fix
    $$('.sitegroup_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitegroup_tooltip li').each(function(el,i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });
      <?php if($this->paginator->count()>0):?>
    <?php if( $this->enableLocation && $this->map_view): ?>
    initialize();
    <?php endif; ?>
    //  $('grid_view').style.display='none';
      switchview(<?php echo $this->defaultView ?>);
    <?php endif;?>
  });
</script>

<?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>

<script type="text/javascript">
  //<![CDATA[
  // this variable will collect the html which will eventually be placed in the side_bar
  var side_bar_html = "";

  // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var gmarkers = [];

  // global "map" variable
  var map = null;
  // A function to create the marker and set up the event window function
  function createMarker(latlng, name, html,title_group) {
    var contentString = html;
    if(name ==0){
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_group,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else{
      var marker =new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_group,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }
    gmarkers.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.setContent(contentString);
			google.maps.event.trigger(map, 'resize');

      infowindow.open(map,marker);

    });
  }

  function initialize() {

    // create the map
    var myOptions = {
      zoom: <?php echo $defaultZoom;?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      //  mapTypeControl: true,
      // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"),
    myOptions);

    google.maps.event.addListener(map, 'click', function() {

      infowindow.close();
			google.maps.event.trigger(map, 'resize');
    });

	<?php   foreach ($this->locations as $location) : ?>
     // obtain the attribues of each marker
     var lat = <?php echo $location->latitude ?>;
     var lng =<?php echo $location->longitude  ?>;
     var point = new google.maps.LatLng(lat,lng);
     <?php if(!empty ($enableBouce)):?>
     var sponsored = <?php echo $this->sitegroup[$location->group_id]->sponsored ?>
      <?php else:?>
     var sponsored =0;
     <?php endif; ?>
     // create the marker

		 <?php $group_id = $this->sitegroup[$location->group_id]->group_id; ?>
     var contentString = '<div id="content">'+
       '<div id="siteNotice">'+
       '</div>'+'  <ul class="sitegroups_locationdetails"><li>'+

       '<div class="sitegroups_locationdetails_info_title">'+
				'<a href="<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view', true) ?>">'+"<?php echo  $this->string()->escapeJavascript($this->sitegroup[$location->group_id]->getTitle()); ?>"+'</a>'+

       '<div class="fright">'+
       '<span >'+
              <?php if ($this->sitegroup[$location->group_id]->featured == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
                  '</span>'+
                    '<span>'+
              <?php if ($this->sitegroup[$location->group_id]->sponsored == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
              <?php endif; ?>
		        '</span>'+
		      '</div>'+
	      '<div class="clr"></div>'+
	      '</div>'+

       '<div class="sitegroups_locationdetails_photo" >'+
       '<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup[$location->group_id]->group_id, $this->sitegroup[$location->group_id]->owner_id,$this->sitegroup[$location->group_id]->getSlug()), $this->itemPhoto($this->sitegroup[$location->group_id], 'thumb.normal')) ?>'+
       '</div>'+
       '<div class="sitegroups_locationdetails_info">'+

				<?php if ($this->ratngShow): ?>
					<?php if (($this->sitegroup[$location->group_id]->rating > 0)): ?>
							'<span class="clr">'+
							<?php for ($x = 1; $x <= $this->sitegroup[$location->group_id]->rating; $x++): ?>
									'<span class="rating_star_generic rating_star"></span>'+
							<?php endfor; ?>
							<?php if ((round($this->sitegroup[$location->group_id]->rating) - $this->sitegroup[$location->group_id]->rating) > 0): ?>
									'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
									'</span>'+
					<?php endif; ?>
				<?php endif; ?>

            '<div class="sitegroups_locationdetails_info_date">'+
              '<?php echo $this->timestamp(strtotime($this->sitegroup[$location->group_id]->creation_date)) ?> - <?php echo  $this->string()->escapeJavascript($this->translate('posted by123')); ?> '+
              '<?php echo $this->htmlLink($this->sitegroup[$location->group_id]->getOwner()->getHref(),  $this->string()->escapeJavascript($this->sitegroup[$location->group_id]->getOwner()->getTitle())) ?>'+
              '</div>'+

              '<div class="sitegroups_locationdetails_info_date">'+
              '<?php echo  $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitegroup[$location->group_id]->like_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->like_count))) ?>,&nbsp;'+
							<?php if ($this->ratngShow):?>
								'<?php echo  $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitegroup[$location->group_id]->review_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->review_count))) ?>,&nbsp;'+
							<?php endif; ?>
              '<?php echo  $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitegroup[$location->group_id]->comment_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->comment_count))) ?>,&nbsp;'+
              '<?php echo  $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitegroup[$location->group_id]->view_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->view_count))) ?>'+
              
              '</div>'+
              						'<div class="sitegroups_locationdetails_info_date">'+
								<?php if (!empty($this->sitegroup[$location->group_id]->phone)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitegroup[$location->group_id]->phone ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitegroup[$location->group_id]->email)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitegroup[$location->group_id]->email ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitegroup[$location->group_id]->website)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Website: ")) .$this->sitegroup[$location->group_id]->website ?>"+
								<?php endif; ?>
             '</div>'+
              <?php if($this->sitegroup[$location->group_id]->price && $this->enablePrice): ?>
                '<div class="sitegroups_locationdetails_info_date">'+
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Price: ")); echo  $this->locale()->toCurrency($this->sitegroup[$location->group_id]->price, $currency) ?>"+
							'</div>'+
              <?php endif; ?>
							'<div class="sitegroups_locationdetails_info_date">'+
								"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?>"+
							'</div>'+
              '</div>'+
              '<div class="clr"></div>'+
              ' </li></ul>'+


              '</div>';

            var marker = createMarker(point,sponsored,contentString,"<?php echo str_replace('"',' ',$this->sitegroup[$location->group_id]->getTitle()); ?>");
      <?php   endforeach; ?>

        }

        var infowindow = new google.maps.InfoWindow(
        {
          size: new google.maps.Size(250,50)
        });

        function toggleBounce() {
          for(var i=0; i<gmarkers.length;i++){
            if (gmarkers[i].getAnimation() != null) {
              gmarkers[i].setAnimation(null);
            }
          }
        }
</script>
<?php endif;?>
