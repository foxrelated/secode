<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-location-photos.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty($this->isajax)):?>
<div id="sitetagchikin-map-scroll-area" style="overflow:hidden">
	<div id="sitetagchikin-map-scroll" class="scroll_content" style="max-height: 220px;">
<?php endif;?>
  
		<div  style="overflow-y: scroll; height:170px;">
      <?php if( !empty($this->action_count) && $this->eventlocations && !empty($this->event_count) && empty($this->isajax) && ($this->category == 1)):?>
				<div id="feed_options" class="stcheckin_map_tip_top_links">
					<a href="javascript:void(0);" class="fright" onclick="showEvents();" id="show_events"><?php echo $this->translate("Show Events");?></a>
					<span class="fright">|</span>
					<a href="javascript:void(0);" class="fright active" onclick="showUpdatesFeeds();" id="show_feeds"><?php echo $this->translate("Show Feeds");?></a>
				</div>  
      <?php endif;?>
		<?php if($this->action_count && $this->category != 5) :?>
      <?php if(empty($this->feed_type)):?>
			  <div id="show_updates_feeds">
      <?php endif;?>
        <?php if(empty($this->feed_type) || ($this->feed_type == 'checkins')):?>
					<?php	echo $this->SitetagcheckinActivityLoopSM($this->actions, array(
							'location_id' => $this->location_id,
							'location' => $this->location,
							'show_map' => 1,
							'category' => $this->category,
							'isajax' => $this->isajax,
							'sitetagcheckin_id' => 'location_photo'
						));
					?>
         <?php endif;?>
      <?php if(empty($this->feed_type)):?>
		    </div>
      <?php endif;?>
		<?php endif;?>


    <?php if(!empty($this->event_count) && $this->category == 5) :?>
       <?php $style = "display:block;"?>
    <?php elseif( empty($this->action_count) && $this->category == 5) :?>
       <?php $style = "display:block;"?>
     <?php elseif(!empty($this->event_count) &&  empty($this->action_count) && $this->category == 1) :?>
       <?php $style = "display:block;"?>
     <?php else: ?>
      <?php $style = "display:none;"?>
     <?php endif;?>

 
		<?php if(!empty($this->event_count)):?>
         <?php if(empty($this->feed_type)):?>
				   <div id="sitetagcheckin_event_content" style="<?php echo $style;?>" >
         <?php endif;?>
         <?php if(empty($this->feed_type) || ($this->feed_type == 'events')):?>
						<?php foreach( $this->eventlocations as $action ):?>
              <?php if(isset($action['occurrence_id']) && !empty($action['occurrence_id']) && is_numeric($action['occurrence_id'])) :?>  
                <?php $getItem = Engine_Api::_()->getItem('siteevent_event', $action['event_id']);?>
              <?php elseif($action['parent_type'] == 'user') :?>
                <?php $getItem = Engine_Api::_()->getItem('event', $action['event_id']);?>
              <?php elseif($action['parent_type'] == 'sitepage_page') :?>
                <?php $getItem = Engine_Api::_()->getItem('sitepageevent_event', $action['event_id']);?>
              <?php elseif($action['parent_type'] == 'sitebusiness_business'):?>
                <?php $getItem = Engine_Api::_()->getItem('sitebusinessevent_event', $action['event_id']);?>
              <?php elseif($action['parent_type'] == 'sitegroup_group'):?>
                <?php $getItem = Engine_Api::_()->getItem('sitegroupevent_event', $action['event_id']);?>
              <?php elseif($action['parent_type'] == 'sitestore_store'):?>
                <?php $getItem = Engine_Api::_()->getItem('sitestore_store', $action['event_id']);?>
              <?php endif;?>
							<div class="stcheckin_map_tip_header">
								<div class="stcheckin_map_tip_header_img">
									<?php echo $this->htmlLink($getItem->getHref(), $this->itemPhoto($getItem, 'thumb.icon','')); ?>
								</div>
								<div class="stcheckin_map_tip_header_info">
									<div class="stcheckin_map_tip_header_title">
										<?php echo $this->htmlLink($getItem->getHref(), $getItem->getTitle()); ?>
									</div>
									<div class="stcheckin_map_tip_header_stat seaocore_txt_light">
										<?php echo $this->translate(ucfirst($getItem->getShortType()));?> 
											<?php if(Engine_Api::_()->seaocore()->getCategory($getItem->getType(), $getItem)):?>
												&raquo; 
												<?php echo Engine_Api::_()->seaocore()->getCategory($getItem->getType(), $getItem);	?>
											<?php endif;?>
									</div>
									<div class="stcheckin_map_tip_header_stat seaocore_txt_light">
										<?php echo $action['location'];	?>
									</div>
								</div>	
							</div>
							<div class="stcheckin_feeds">
								<ul class="feed">
									<li>
										<?php $item = Engine_Api::_()->getItem('user', $action['event_user_id']); ?>
										<div class="feed_item_photo">
											<?php echo  $this->htmlLink($item->getHref(),
												$this->itemPhoto($item, 'thumb.icon', $item->getTitle()),  array('class'=>'sea_add_tooltip_link', 'rel'=>$item->getType().' '.$item->getIdentity())
											)  ?>
										</div>
										<div class="feed_item_body">
                      <?php $username = $this->htmlLink($item->getHref(), $item->getTitle());?>
											<?php $starttime = date("F j, Y", strtotime($action['starttime']));?>
											<?php echo $this->translate("%1$s has attended this event on %2$s.", $username, $starttime);?>
										</div>	
									</li>
								</ul>		
							</div>
						<?php endforeach;?>
          
						<?php if( $this->eventlocations->getTotalItemCount() > 1 ):?>
							<div class="sitetag_checkin_map_tip_paging">
								<?php if( $this->eventlocations->getCurrentPageNumber() > 1 ): ?>
									<div id="user_group_members_previous" class="paginator_previous">
										<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
											'onclick' => 'paginateEventsFeeds(feedEventPage - 1)',
											'class' => 'buttonlink icon_previous'
										)); ?>
									</div>
								<?php endif; ?>
								<?php if( $this->eventlocations->getCurrentPageNumber() < $this->eventlocations->count() ): ?>
									<div id="user_group_members_next" class="paginator_next">
										<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
											'onclick' => 'paginateEventsFeeds(feedEventPage + 1)',
											'class' => 'buttonlink_right icon_next'
										)); ?>
									</div>
								<?php endif; ?>
							</div>
							<div id="show-background-pagination-image-events" style="display:none"> 
								<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" /></center>
							</div>
						<?php endif; ?>
					<?php endif;?>
        <?php if(empty($this->feed_type)):?>
				  </div>
        <?php endif;?> 
			<?php endif;?>   
    </div>
    
<?php if(empty($this->isajax)):?>
  </div>
</div>
<?php endif;?>

<?php if(empty($this->isajax)):?>
	<script type="text/javascript">
		
	</script>
<?php endif;?>

<?php if($this->show_map == 1) :?>
	<script type="text/javascript">
		<?php if(!empty($this->eventlocations)):?>
			var feedEventPage = <?php echo sprintf('%d', $this->eventlocations->getCurrentPageNumber()) ?>;
    <?php endif;?>
		var paginateEventsFeeds = function(page) 
		{
			$('#show-background-pagination-image-events').css('display', 'block')
			var url = '<?php echo $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true);?>';
      
      
      $.ajax({
        url : url,
        type: "GET", 
        dataType: "html", 
       'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'isajax' : '1',
					'page' : page,
					'show_map' : 1,
					'location_id' : '<?php echo $this->location_id;?>',
					'location' : '<?php echo $this->location;?>',
					'category': '<?php echo $this->category;?>',
					'feed_type': 'events'
				},   
        success : function(responseHTML, textStatus, xhr) {
         $('#sitetagcheckin_event_content').html(responseHTML);
         sm4.core.dloader.refreshPage();
        }
      });    
		}
	</script>

<script type="text/javascript">

function showEvents() {
  if($('#show_events'))
  document.getElementById('show_events').className="fright active";
  if($('#show_feeds'))
  document.getElementById('show_feeds').className="fright";
  if($('#sitetagcheckin_event_content'))
	$('#sitetagcheckin_event_content').css('display', 'block');
  if($('#show_updates_feeds'))
	$('#show_updates_feeds').css('display', 'none');
}

function showUpdatesFeeds() {
  if($('#show_feeds'))
  document.getElementById('show_feeds').className="fright active";
  if($('#show_events'))
  document.getElementById('show_events').className="fright";
  if($('#sitetagcheckin_event_content'))
	$('#sitetagcheckin_event_content').css('display', 'none');
  if($('#show_updates_feeds'))
	$('#show_updates_feeds').css('display', 'block');
}

</script>

<?php endif;?>



