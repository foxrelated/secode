<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/list_tooltip.css')
		->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');
?>

<?php if(!empty($this->category_id) || (Count($this->paginator) > 0 && empty($this->linked)) || (isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id']))): ?>
	<div class="seaocore_breadcrumbs">
		<div class="seaocore_breadcrumbs_txt">

			<?php if(!empty($this->category_id)): ?>
				<?php echo $this->translate('Browse Listings'); ?>
				<?php if ($this->category_name != ''): ?>
					<?php echo '<span class="brd-sep bold seaocore_txt_light">&raquo;</span>'; ?>
				<?php endif; ?>

				<?php
					$this->category_name = $this->translate($this->category_name);
					$this->subcategory_name = $this->translate($this->subcategory_name);
					$this->subsubcategory_name = $this->translate($this->subsubcategory_name);
				?>
				<?php if ($this->category_name != '' ) :?>
					<?php echo $this->htmlLink($this->url(array('category' => $this->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name)), 'list_general_category'), $this->translate($this->category_name)) ?>
					<?php if ($this->subcategory_name != ''):?> 
						<?php echo '<span class="brd-sep bold seaocore_txt_light">&raquo;</span>'; ?>
						<?php echo $this->htmlLink($this->url(array('category' => $this->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory' => $this->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name)), 'list_general_subcategory'), $this->translate($this->subcategory_name)) ?>
						<?php if(!empty($this->subsubcategory_name)):?>
							<?php echo '<span class="brd-sep bold seaocore_txt_light">&raquo;</span>';?>
							<?php echo $this->htmlLink($this->url(array('category' => $this->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory' => $this->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name),'subsubcategory' => $this->subsubcategory_id, 'subsubcategoryname' => $this->categoryTable->getCategorySlug($this->subsubcategory_name)), 'list_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif;?>

			<?php if(((isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id'])))): ?>
				<?php $tag_value = $this->formValues['tag']; $tag_value_id = $this->formValues['tag_id']; $browse_url = $this->url(array('action' => 'index'), 'list_general', true)."?tag=$tag_value&tag_id=$tag_value_id";?>
				<?php if($this->category_name):?><br /><?php endif; ?>
				<?php echo $this->translate("Showing Listings tagged with: ");?>
				<b><a href='<?php echo $browse_url;?>'>#<?php echo $this->formValues['tag'] ?></a>
				<?php if($this->current_url2): ?>
					<a href="<?php echo $this->url(array( 'action' => 'index'),"list_general",true)."?".$this->current_url2; ?>"><?php echo $this->translate('(x)');?></a></b>
				<?php else: ?>
					<a href="<?php echo $this->url(array( 'action' => 'index'),"list_general",true); ?>"><?php echo $this->translate('(x)');?></a></b>
				<?php endif; ?>
			<?php endif; ?>

		</div>	
	</div>
<?php endif; ?>

<?php $latitude = $this->settings->getSetting('list.map.latitude', 0); ?>
<?php $longitude = $this->settings->getSetting('list.map.longitude', 0); ?>
<?php $defaultZoom = $this->settings->getSetting('list.map.zoom', 1); ?>
<?php $enableBouce = $this->settings->getSetting('list.map.sponsored', 1); ?>

<script type="text/javascript" >

function owner(thisobj) {
	var Obj_Url = thisobj.href;
	Smoothbox.open(Obj_Url);
}
</script>

<?php if ($this->paginator->count() > 0): ?>

	<script type="text/javascript">
		var pageAction = function(page){
				
			var form;
			if($('filter_form')) {
				form=document.getElementById('filter_form');
				}else if($('filter_form_list')){
					form=$('filter_form_list');
				}
			form.elements['page'].value = page;
			
			form.submit();
		} 
	</script>

	<form id='filter_form_list' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'list_general', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

 
		<div class="advlist_view_select">
    <?php if(empty($this->is_ajax)):?>
      <div class="fleft"> 
     <?php echo $this->translate(array('%s listing found.', '%s listings found.', $this->paginator->getTotalItemCount()),$this->locale()->toNumber($this->paginator->getTotalItemCount())); ?>
      </div>
     <?php endif;?>
     <?php if ((($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)) && empty($this->is_ajax)): ?>
			<?php  if( $this->enableLocation  && $this->map_view): ?> 
				<span class="list_show_tooltip_wrapper">
					<div class="list_show_tooltip"><?php echo $this->translate("Map View"); ?></div>
					<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/map_icon.png' onclick="switchview(2)" align="left" alt="" />
				</span>
			<?php endif;?>
			<?php  if( $this->grid_view): ?>
				<span class="list_show_tooltip_wrapper">
					<div class="list_show_tooltip"><?php echo $this->translate("Grid View"); ?></div>
					<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/grid.png' onclick="switchview(1)" align="left" alt="" />
				</span>
			<?php endif;?>
			<?php  if( $this->list_view): ?>
				<span class="list_show_tooltip_wrapper">
					<div class="list_show_tooltip"><?php echo $this->translate("List View"); ?></div>
					<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/list.png' onclick="switchview(0)" align="left" alt="" />
				</span>
			<?php endif; ?>
    <?php endif; ?>
		</div>
	

	<?php if( $this->list_view): ?>
		<div id="grid_view" style="display: none;">
			<ul class="seaocore_browse_list">
				<?php foreach ($this->paginator as $list): ?>
					<li>
						<div class='seaocore_browse_list_photo'>
							<?php echo $this->htmlLink($list->getHref(), $this->itemPhoto($list, 'thumb.normal')) ?>
						</div>
        
						<div class='seaocore_browse_list_info'>
							<div class='seaocore_browse_list_info_title'>
								<span>
									<?php if( $list->closed ): ?>
										<img alt="close" src='<?php echo $this->layout()->staticBaseUrl?>application/modules/List/externals/images/close.png'/>
									<?php endif;?>  
									<?php if ($list->sponsored == 1): ?>
										<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
									<?php endif; ?>
									<?php if ($list->featured == 1): ?>
										<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
									<?php endif; ?>
								</span>
								<span class="list_rating_star" title="<?php echo $list->rating.$this->translate(' rating'); ?>">
									<?php if (($list->rating > 0) && $this->ratngShow): ?>
										<?php for ($x = 1; $x <= $list->rating; $x++): ?>
											<span class="rating_star_generic rating_star" ></span>
										<?php endfor; ?>
										<?php if ((round($list->rating) - $list->rating) > 0): ?>
											<span class="rating_star_generic rating_star_half" ></span>
										<?php endif; ?>
									<?php endif; ?>
								</span>
								<h3>
									<?php if(!empty($this->list_generic)){ echo $this->htmlLink($list->getHref(), $list->getTitle()); }else { exit(); } ?>
								</h3>
								<div class="clear"></div>
							</div>

							<div class='seaocore_browse_list_info_date'>
								<?php echo $this->timestamp(strtotime($list->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
								<?php echo $this->htmlLink($list->getOwner()->getHref(), $list->getOwner()->getTitle()) ?>
                
                <?php if(!empty($this->statistics)): ?>,
                
                  <?php 

                    $statistics = '';

                    if(in_array('commentCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s comment', '%s comments', $list->comment_count), $this->locale()->toNumber($list->comment_count)).', ';
                    }

                    if(in_array('reviewCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s review', '%s reviews', $list->review_count), $this->locale()->toNumber($list->review_count)).', ';
                    }

                    if(in_array('viewCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s view', '%s views', $list->view_count), $this->locale()->toNumber($list->view_count)).', ';
                    }

                    if(in_array('likeCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s like', '%s likes', $list->like_count), $this->locale()->toNumber($list->like_count)).', ';
                    }                 

                    $statistics = trim($statistics);
                    $statistics = rtrim($statistics, ',');

                  ?>

                  <?php echo $statistics; ?>
                <?php endif; ?>
							</div>
           
							<?php if(!empty($list->location)  &&  Engine_Api::_()->authorization()->isAllowed($list, $this->viewer(), 'view')): ?>
								<div class='seaocore_browse_list_info_date'>
									<?php  echo $this->translate($list->location); ?>&nbsp;-
										<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $list->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?></b>
								</div>
							<?php endif; ?>
							<div class='seaocore_browse_list_info_blurb'>
								<?php echo $this->viewMore($list->body) ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if( $this->grid_view):?>
		<div id="image_view" style="display: none;">
			<div class="list_img_view">
				<?php $counter=1; foreach ($this->paginator as $list): ?>
					<div class="list_thumb">
						<ul class="jq-list_tooltip">
							<li>
								<?php if(empty($this->list_generic)){exit();} ?>
								<a href="<?php echo $list->getHref() ?>">
									<?php $url = $this->layout()->staticBaseUrl . 'application/modules/List/externals/images/nophoto_list_thumb_normal.png'; $temp_url=$list->getPhotoUrl('thumb.normal'); if(!empty($temp_url)): $url=$list->getPhotoUrl('thumb.normal'); endif;?>
									<span style="background-image: url(<?php echo $url; ?>);"></span>
								</a>
								<?php echo $this->htmlLink($list->getHref(),  Engine_Api::_()->seaocore()->seaocoreTruncateText($list->getTitle(),23)) ?>
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
															<span class="rating_star_generic rating_star"></span>
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
                          <?php if(!empty($this->statistics)): ?>

                            <?php 

                              $statistics = '';

                              if(in_array('commentCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s comment', '%s comments', $list->comment_count), $this->locale()->toNumber($list->comment_count)).', ';
                              }

                              if(in_array('reviewCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s review', '%s reviews', $list->review_count), $this->locale()->toNumber($list->review_count)).', ';
                              }

                              if(in_array('viewCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s view', '%s views', $list->view_count), $this->locale()->toNumber($list->view_count)).', ';
                              }

                              if(in_array('likeCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s like', '%s likes', $list->like_count), $this->locale()->toNumber($list->like_count)).', ';
                              }                 

                              $statistics = trim($statistics);
                              $statistics = rtrim($statistics, ',');

                            ?>

                            <?php echo $statistics; ?>
                          <?php endif; ?>
												</div>
												<?php if(!empty($list->location) &&  Engine_Api::_()->authorization()->isAllowed($list, $this->viewer(), 'view')): ?>
													<div class='lists_tooltip_info_date'>
														<i> <?php  echo $this->translate($list->location); ?> </i>&nbsp;-
															<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $list->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?></b>
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
		</div>
	<?php endif; ?>

 <?php if( $this->map_view):?>
   <div id="map_canvas_view" style="display: none;">
    <div id="list_browse_map_canvas" style="width: 550px; height: 550px"> </div>
    <div class="clear mtop_10"></div>
    <?php if( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
     <a href="javascript:void(0);" onclick="toggleBounce()" class="floatL mbot_10"> <?php echo $this->translate('Stop Bounce'); ?></a>
    <?php endif;?>  
   </div>
 <?php endif;?>

	  <div class="clr" id="scroll_bar_height"></div>
  <?php if (empty($this->is_ajax)) : ?>
    <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => '',
          'class' => 'buttonlink icon_viewmore'
      ))
      ?>
    </div>
    <div class="seaocore_view_more" id="loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <div id="hideResponse_div"> </div>
  <?php endif; ?>
  <?php elseif ($this->search):  ?>
		<div class="tip clr" style="margin-top:15px;">
			<span> <?php echo $this->translate('Nobody has posted a listing with that criteria. Be the first to %1$spost%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'list_general') . '">', '</a>'); ?>
			</span> 
		</div>
  <?php else: ?>
		<?php if(empty($this->list_generic)){exit();} ?>
			<div class="tip"> 
				<span> 
					<?php echo $this->translate('No Listings have been posted yet.'); ?>
					<?php if ($this->can_create): ?>
						<?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'list_general') . '">', '</a>'); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>

<?php if(empty($this->is_ajax)):?>
   <script type="text/javascript" >

   function switchview(flage){
    if(flage==2){
     if($('map_canvas_view')){
     $('map_canvas_view').style.display='block';
    <?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
     google.maps.event.trigger(map, 'resize');
     map.setZoom(<?php echo $defaultZoom?>);
     map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude?>));
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
   en4.core.runonce.add(function() {
    <?php if($this->paginator->count()>0):?>
      switchview(<?php echo $this->defaultView ?>);
     <?php endif;?>
    });
   </script>
<?php endif;?>

	<?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0 && empty($this->is_ajax)): ?>
	<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>

	<script type="text/javascript">
		//<![CDATA[
		// this variable will collect the html which will eventually be placed in the side_bar
		var side_bar_html = "";
  var gmarkers = [];
  var map = null;
  var infowindow = [];

		function initialize() {

			// create the map
			var myOptions = {
				zoom: <?php echo $defaultZoom?>,
				center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude?>),
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			map = new google.maps.Map(document.getElementById("list_browse_map_canvas"),
			myOptions);

			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
				google.maps.event.trigger(map, 'resize');
			});
   infowindow = new google.maps.InfoWindow(
		{
			size: new google.maps.Size(250,50)
		});
		}
  
  		// A function to create the marker and set up the event window function
		function createMarker(latlng, name, html) {
			var contentString = html;
			if(name ==0){
				var marker = new google.maps.Marker({
					position: latlng,
					map: map,
					animation: google.maps.Animation.DROP,
					zIndex: Math.round(latlng.lat()*-100000)<<5
				});
			}
			else{
				var marker =new google.maps.Marker({
					position: latlng,
					map: map,
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

		function toggleBounce() {
			for(var i=0; i<gmarkers.length;i++){
				if (gmarkers[i].getAnimation() != null) {
					gmarkers[i].setAnimation(null);
				}
			}
		}
  
 en4.core.runonce.add(function() {
    initialize();
  });
	</script>
<?php endif;?>

 <script type="text/javascript">
   
      en4.core.runonce.add(function() {

      /* moo style */

     //opacity / display fix
     $$('.list_tooltip').setStyles({
      opacity: 0,
      display: 'block'
     });
     //put the effect in place
     $$('.jq-list_tooltip li').each(function(el,i) {
      el.addEvents({
       'mouseenter': function() {
        el.getElement('div').fade('in');
       },
       'mouseleave': function() {
        el.getElement('div').fade('out');
       }
      });
     });
    
	<?php foreach ($this->locations as $location) : ?>
		<?php if( Engine_Api::_()->authorization()->isAllowed($this->list[$location->listing_id], $this->viewer(), 'view')):?>
			// obtain the attribues of each marker
			var lat = <?php echo $location->latitude ?>;
			var lng =<?php echo $location->longitude  ?>;
			var point = new google.maps.LatLng(lat,lng);
			<?php if(!empty ($enableBouce)):?>
			var sponsored = <?php echo $this->list[$location->listing_id]->sponsored ?>
				<?php else:?>
				var sponsored =0;
			<?php endif; ?>

      <?php $statistics = ''; if(!empty($this->statistics)): ?> <?php 
                  
        //$statistics = "";

        if(in_array("commentCount", $this->statistics)) {
          $statistics .= $this->translate(array("%s comment", "%s comments", $this->list[$location->listing_id]->comment_count), $this->locale()->toNumber($this->list[$location->listing_id]->comment_count)).", ";
        }

        if(in_array("reviewCount", $this->statistics)) {
          $statistics .= $this->translate(array("%s review", "%s reviews", $this->list[$location->listing_id]->review_count), $this->locale()->toNumber($this->list[$location->listing_id]->review_count)).", ";
        }

        if(in_array("viewCount", $this->statistics)) {
          $statistics .= $this->translate(array("%s view", "%s views", $this->list[$location->listing_id]->view_count), $this->locale()->toNumber($this->list[$location->listing_id]->view_count)).", ";
        }

        if(in_array("likeCount", $this->statistics)) {
          $statistics .= $this->translate(array("%s like", "%s likes", $this->list[$location->listing_id]->like_count), $this->locale()->toNumber($this->list[$location->listing_id]->like_count)).", ";
        }                 

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ",");

      ?>

      <?php endif;?>


			var contentString = '<div id="content">'+
				'<div id="siteNotice">'+
				'</div>'+'  <ul class="lists_locationdetails"><li>'+

				'<div class="lists_locationdetails_info_title">'+
					'<a href="<?php echo $this->url(array('listing_id' => $this->list[$location->listing_id]->listing_id, 'user_id' => $this->list[$location->listing_id]->owner_id,'slug' => $this->list[$location->listing_id]->getSlug()), 'list_entry_view', true) ?>">'+"<?php echo $this->string()->escapeJavascript( $this->list[$location->listing_id]->getTitle()); ?>"+'</a>'+

				'<div class="floatR">'+
				'<span >'+
								<?php if ($this->list[$location->listing_id]->featured == 1): ?>
										'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
										'</span>'+
											'<span>'+
								<?php if ($this->list[$location->listing_id]->sponsored == 1): ?>
										'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
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
						'<?php echo $this->timestamp(strtotime($this->list[$location->listing_id]->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by')); ?> '+
						'<?php echo $this->htmlLink($this->list[$location->listing_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->list[$location->listing_id]->getOwner()->getTitle())) ?>'+
						'</div>'+ '<div class="lists_locationdetails_info_date">' +
								"<?php echo $this->string()->escapeJavascript($statistics); ?>"
						+ '</div>' +

						'<div class="lists_locationdetails_info_date">'+
							"<i><b>"+"<?php echo $this->string()->escapeJavascript( $location->location); ?>"+ "</b></i>"+
						'</div>'+
						'</div>'+
						'<div class="clear"></div>'+
						' </li></ul>'+
						'</div>';

					var marker = createMarker(point,sponsored,contentString);
				<?php endif; ?>
			<?php   endforeach; ?>
     });
   
 </script>
 
 <?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMoreListing()
    {
      var viewType = 2;
      if($('grid_view')) {
        if($('grid_view').style.display== 'block')
          viewType = 0;
      }
      if($('image_view')) {
      if($('image_view').style.display== 'block')
        viewType = 1;
      }
      
      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': en4.core.baseUrl + 'widget/index/mod/list/name/listings-list',
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            isajax: 1,
            show_content: '<?php echo $this->showContent;?>',
            view_type: viewType,
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            if($('grid_view')) {
              $('grid_view').getElement('.seaocore_browse_list').innerHTML = $('grid_view').getElement('.seaocore_browse_list').innerHTML + $('hideResponse_div').getElement('.seaocore_browse_list').innerHTML;
            }
            if($('image_view')) {
              $('image_view').getElement('.list_img_view').innerHTML = $('image_view').getElement('.list_img_view').innerHTML + $('hideResponse_div').getElement('.list_img_view').innerHTML;
            }
            $('loding_image').style.display = 'none';
            switchview(viewType);
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

<?php if ($this->showContent == 3): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php elseif ($this->showContent == 2): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php else: ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'none';
    });
  </script>
  <?php
  echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "list"), array("orderby" => $this->orderby, "query" => $this->formValues));
  ?>
<?php endif; ?>

<script type="text/javascript">

  function getNextPage() {
    return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
  }

  function hideViewMoreLink(showContent) {

    if (showContent == 3) {
      $('seaocore_view_more').style.display = 'none';
      var totalCount = '<?php echo $this->paginator->count(); ?>';
      var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

      function doOnScrollLoadPage()
      {
        if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
          var elementPostionY = $('scroll_bar_height').offsetTop;
        } else {
          var elementPostionY = $('scroll_bar_height').y;
        }
        if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
          if ((totalCount != currentPageNumber) && (totalCount != 0))
            viewMoreListing();
        }
      }
      
      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMoreListing();
      });
    }
  }
</script>
