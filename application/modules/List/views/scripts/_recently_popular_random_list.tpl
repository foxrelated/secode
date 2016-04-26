<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _recently_popular_random_list.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core');?>

<?php $enableBouce = $settings->getSetting('list.map.sponsored', 1); ?>
<?php  $latitude = $settings->getSetting('list.map.latitude', 0); ?>
<?php  $longitude = $settings->getSetting('list.map.longitude', 0); ?>
<?php  $defaultZoom = $settings->getSetting('list.map.zoom', 1); ?>
<?php if( $this->list_view): ?>
<div id="rgrid_view">
 <?php $list_entry = Zend_Registry::isRegistered('list_entry') ?>
	<?php if (count($this->listings)): ?>
		<?php $counter='1';
				$limit = $this->active_tab_list;
		?>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->listings as $list): ?>
				<?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
				<li>
					<div class='seaocore_browse_list_photo'>
						<?php  echo $this->htmlLink($list->getHref(), $this->itemPhoto($list, 'thumb.normal'));  ?>
					</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<span>
								<?php if ($list->sponsored == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
								<?php endif; ?>
								<?php if ($list->featured == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								<?php endif; ?>
							</span>
							<div class="seaocore_title">
								<?php echo $this->htmlLink($list->getHref(), $list->getTitle()) ?>
							</div>
						</div>
						<?php if (($list->rating > 0) && $this->ratngShow): ?>
							<span class="clear" title="<?php echo $list->rating.$this->translate(' rating'); ?>">
								<?php for ($x = 1; $x <= $list->rating; $x++): ?>
									<span class="rating_star_generic rating_star" ></span>
								<?php endfor; ?>
								<?php if ((round($list->rating) - $list->rating) > 0): ?>
									<span class="rating_star_generic rating_star_half" ></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->timestamp(strtotime($list->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
							<?php echo $this->htmlLink($list->getOwner()->getHref(), $list->getOwner()->getTitle()) ?>
							</div>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->translate(array('%s comment', '%s comments', $list->comment_count), $this->locale()->toNumber($list->comment_count)) ?>,
							<?php echo $this->translate(array('%s review', '%s reviews', $list->review_count), $this->locale()->toNumber($list->review_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $list->view_count), $this->locale()->toNumber($list->view_count)) ?>,
							<?php echo $this->translate(array('%s like', '%s likes', $list->like_count), $this->locale()->toNumber($list->like_count)) ?>
						</div>
						<?php	if(!empty($list->location) && $this->enableLocation &&  Engine_Api::_()->authorization()->isAllowed($list, $this->viewer(), 'view')): ?>
						<div class='seaocore_browse_list_info_date'>
						<?php echo $this->translate($list->location); ?>
						&nbsp;-
								<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $list->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
						</div>
						<?php endif; ?>
						
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php  if( $this->grid_view):?>
<div id="rimage_view" style="display: none;">
	<?php if (count($this->listings)): ?>
	
	  <?php $counter=1;
	  			$total_list = count($this->listings);
					$limit =  $this->active_tab_image;
		?>
		<div class="list_img_view">
			<?php foreach ($this->listings as $list): ?>
        <?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
				<div class="list_thumb" >
					<ul class="jq-list_tooltip">
						<li>
				      <a href="<?php echo $list->getHref() ?>">
				      <?php $url = $this->layout()->staticBaseUrl . 'application/modules/List/externals/images/nophoto_list_thumb_normal.png'; $temp_url=$list->getPhotoUrl('thumb.normal'); if(!empty($temp_url)): $url=$list->getPhotoUrl('thumb.normal'); endif;?>
				      	<span style="background-image: url(<?php echo $url; ?>);"></span>
				      </a>
              	<?php echo $this->htmlLink($list->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($list->getTitle(),23 )) ?>
				    	<div class="list_tooltip">
								<div class="list_tooltip_content_outer">
									<div class="list_tooltip_content_inner">
										<div class="list_tooltip_arrow">
											<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/tooltip_arrow.png' alt="" />
										</div>
				    		  	<div class='lists_tooltip_info'>
				    					<div class="title">
				          			<?php echo $this->htmlLink($list->getHref(), $list->getTitle()) ?>
						            <span>
							            <?php if ($list->featured == 1): ?>
								            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							            <?php endif; ?>
						            </span>
						             <span>
							            <?php if ($list->sponsored == 1): ?>
								            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						            <?php endif; ?>
						            </span>
				        			</div>
				          		<?php if (($list->rating > 0) && $this->ratngShow): ?>
				          			<span class="clear" title="<?php echo $list->rating.$this->translate(' rating'); ?>">
							            <?php for ($x = 1; $x <= $list->rating; $x++): ?>
							            <span class="rating_star_generic rating_star" ></span>
							            <?php endfor; ?>
							            <?php if ((round($list->rating) - $list->rating) > 0): ?>
							            <span class="rating_star_generic rating_star_half" ></span>
							            <?php endif; ?>
							          </span>
					            <?php endif; ?>
											<div class='lists_tooltip_info_date clear'>
								      	<?php echo $this->timestamp(strtotime($list->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                        <?php echo $this->htmlLink($list->getOwner()->getHref(), $list->getOwner()->getTitle()) ?>
            	        </div>
								      <div class='lists_tooltip_info_date'>
								      	<?php echo $this->translate(array('%s comment', '%s comments', $list->comment_count), $this->locale()->toNumber($list->comment_count)) ?>,
												<?php echo $this->translate(array('%s review', '%s reviews', $list->review_count), $this->locale()->toNumber($list->review_count)) ?>,
								        <?php echo $this->translate(array('%s view', '%s views', $list->view_count), $this->locale()->toNumber($list->view_count)) ?>,
                           <?php echo $this->translate(array('%s like', '%s likes', $list->like_count), $this->locale()->toNumber($list->like_count)) ?>
									    </div>
                      <?php if(!empty($list->location) &&  Engine_Api::_()->authorization()->isAllowed($list, $this->viewer(), 'view')): ?>
                      <div class='lists_tooltip_info_date'>
                       <?php  echo $this->translate($list->location); ?>&nbsp;-
												<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $list->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
                      </div>
                     <?php endif; ?>
										</div>
				      		</div>
								</div>
				      </div>
		      	</li>
		      </ul>
			  </div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php endif; ?>
<div id="rmap_canvas_view" style="display: none;">
  <div id="rmap_canvas" class="list_rmap_canvas"> </div>
    <?php if( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
  	<a href="javascript:void(0);" onclick="rtoggleBounce()" class="floatL mtop_10"> <?php echo $this->translate('Stop Bounce'); ?></a>
    <br />
	<?php endif;?>
</div>


<?php if( $this->enableLocation && $this->map_view): ?>
	<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>

	<script type="text/javascript">
   // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var rgmarkers = [];

  // global "map" variable
  var rmap = null;
  // A function to create the marker and set up the event window function
  function rcreateMarker(latlng, name, html,title_list) {
    var contentString = html;
    if(name ==0){
      var marker = new google.maps.Marker({
        position: latlng,
        map: rmap,
				title:title_list,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else{
      var marker =new google.maps.Marker({
        position: latlng,
        map: rmap,
				title:title_list,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }
    rgmarkers.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.setContent(contentString);
		google.maps.event.trigger(rmap, 'resize');

      infowindow.open(rmap,marker);

    });
  }

  function rinitialize() {
    // create the map
    var myOptions = {
      zoom: <?php echo $defaultZoom?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude?>),
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    rmap = new google.maps.Map(document.getElementById("rmap_canvas"),
    myOptions);

    google.maps.event.addListener(rmap, 'click', function() {
      infowindow.close();
		google.maps.event.trigger(rmap, 'resize');

    });

 <?php   foreach ($this->locations as $location) : ?>
   <?php if(Engine_Api::_()->authorization()->isAllowed($this->list[$location->listing_id], $this->viewer(), 'view')):?>
     // obtain the attribues of each marker
     var lat = <?php echo $location->latitude ?>;
     var lng =<?php echo $location->longitude  ?>;
     var point = new google.maps.LatLng(lat,lng);
      <?php if(!empty ($enableBouce)):?>
     var sponsored = <?php echo $this->list[$location->listing_id]->sponsored ?>
      <?php else:?>
      var sponsored =0;
     <?php endif; ?>
     // create the marker
		 <?php $listing_id = $this->list[$location->listing_id]->listing_id; ?>
     var contentString = '<div id="content">'+
       '<div id="siteNotice">'+
       '</div>'+'  <ul class="lists_locationdetails"><li>'+
       '<div class="lists_locationdetails_info_title">'+

      '<a href="<?php echo $this->url(array('listing_id' => $this->list[$location->listing_id]->listing_id, 'user_id' => $this->list[$location->listing_id]->owner_id,'slug' => $this->list[$location->listing_id]->getSlug()), 'list_entry_view', true) ?>">'+"<?php echo $this->translate(str_replace('"', " ", $this->list[$location->listing_id]->getTitle())); ?>"+'</a>'+

				'<div class="floatR">'+
       '<span >'+
              <?php if ($this->list[$location->listing_id]->featured == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>'+	            <?php endif; ?>
                  '</span>'+
                    '<span>'+
              <?php if ($this->list[$location->listing_id]->sponsored == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>'+
              <?php endif; ?>
            '</span>'+
	        '</div>'+
        '<div class="clear"></div>'+
        '</div>'+
       '<div class="lists_locationdetails_photo" >'+
       '<?php echo $this->htmlLink($this->list[$location->listing_id]->getHref(), $this->itemPhoto($this->list[$location->listing_id], 'thumb.normal')) ?>'+
       '</div>'+
       '<div class="lists_locationdetails_info">'+
        <?php if (($this->list[$location->listing_id]->rating > 0) && $this->ratngShow): ?>
            '<span class="clear">'+
            <?php for ($x = 1; $x <= $this->list[$location->listing_id]->rating; $x++): ?>
                '<span class="rating_star_generic rating_star"></span>'+
            <?php endfor; ?>
            <?php if ((round($this->list[$location->listing_id]->rating) - $this->list[$location->listing_id]->rating) > 0): ?>
                '<span class="rating_star_generic rating_star_half"></span>'+
            <?php endif; ?>
                '</span>'+
        <?php endif; ?>

            '<div class="lists_locationdetails_info_date">'+
              '<?php echo $this->timestamp(strtotime($this->list[$location->listing_id]->creation_date)) ?> - <?php echo $this->translate('posted by'); ?> '+
              '<?php echo $this->htmlLink($this->list[$location->listing_id]->getOwner()->getHref(), $this->list[$location->listing_id]->getOwner()->getTitle()) ?>'+
              '</div>'+

              '<div class="lists_locationdetails_info_date">'+
              '<?php echo $this->translate(array('%s comment', '%s comments', $this->list[$location->listing_id]->comment_count), $this->locale()->toNumber($this->list[$location->listing_id]->comment_count)) ?>,&nbsp;'+
              '<?php echo $this->translate(array('%s review', '%s reviews', $this->list[$location->listing_id]->review_count), $this->locale()->toNumber($this->list[$location->listing_id]->review_count)) ?>,&nbsp;'+
              '<?php echo $this->translate(array('%s view', '%s views', $this->list[$location->listing_id]->view_count), $this->locale()->toNumber($this->list[$location->listing_id]->view_count)) ?>,&nbsp;'+
              '<?php echo $this->translate(array('%s like', '%s likes', $this->list[$location->listing_id]->like_count), $this->locale()->toNumber($this->list[$location->listing_id]->like_count)) ?>'+
              '</div>'+
							'<div class="lists_locationdetails_info_date">'+
								"<i><b>"+"<?php echo str_replace('"', " ",$location->location); ?>"+ "</b></i>"+
							'</div>'+
              '</div>'+
              '<div class="clear"></div>'+
              ' </li></ul>'+


              '</div>';

            var marker = rcreateMarker(point,sponsored,contentString, "<?php echo str_replace('"',' ',$this->list[$location->listing_id]->getTitle())?>");
           <?php endif; ?>
       <?php   endforeach; ?>



        }


        var infowindow = new google.maps.InfoWindow(
        {
          size: new google.maps.Size(250,50)
        });

        function rtoggleBounce() {
          for(var i=0; i<rgmarkers.length;i++){
            if (rgmarkers[i].getAnimation() != null) {
              rgmarkers[i].setAnimation(null);
            }
          }
        }
        //]]>
</script>
<?php endif;?>
