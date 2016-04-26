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

<?php echo $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js'); ?>

<script language="JavaScript"> 
	function videoclose(id) {
		var video_object="lsit_video_object_"+id;
		var video_thumb="list_video_thumb_"+id;

		document.getElementById(video_thumb).style.display='block';
		document.getElementById(video_object).style.display="none"; 
  }
</script>

<?php if ($this->allowed_upload_video): ?>
	<div class="seaocore_add clear">
		<a href='<?php echo $this->url(array('action' => 'index', 'listing_id' => $this->list->listing_id, 'content_id' => $this->identity), 'list_video_upload', true) ?>'  class='buttonlink icon_lists_video_new'><?php echo $this->translate('Add Video'); ?></a>
	</div>
<?php endif; ?>

<?php  if(count($this->paginator) > 0):?>
	<ul class="list_video_list">
		<?php foreach ($this->paginator as $item): ?>
			<li>
				<?php $videoEmbedded=null;?>
        <?php if($this->sitevideoviewEnable): ?>
					<a id="list_video_thumb_<?php echo $item->video_id; ?>"  href="<?php echo $item->getHref(); ?>">
						<div class="video_thumb_wrapper">		
							<?php if ($item->duration): ?>
								<span class="video_length">
									<?php  
									if ($item->duration > 360)
										$duration = gmdate("H:i:s", $item->duration); else
										$duration = gmdate("i:s", $item->duration);
									if ($duration[0] == '0')
										$duration = substr($duration, 1); echo $duration;
									?>
								</span>
							<?php endif; ?> 		      
							<?php  if ($item->photo_id):
								echo   $this->itemPhoto($item, 'item_photo_video');
							else: ?>
								<img alt="" src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Video/externals/images/video.png" class="item_photo_video  thumb_normal">
							<?php endif;?>
						</div>              
					</a>               
        <?php else: ?>
					<a id="list_video_thumb_<?php echo $item->video_id; ?>"  href="javascript:void(0);" onclick="javascript:var myElement = 	$(this);myElement.style.display='none';var next = myElement.getNext(); next.style.display='block';">
						<div class="video_thumb_wrapper">		
							<?php if ($item->duration): ?>
								<span class="video_length">
									<?php  
									if ($item->duration > 360)
										$duration = gmdate("H:i:s", $item->duration); else
										$duration = gmdate("i:s", $item->duration);
									if ($duration[0] == '0')
										$duration = substr($duration, 1); echo $duration;
									?>
								</span>
							<?php endif; ?> 		      
							<?php  if ($item->photo_id):
								echo   $this->itemPhoto($item, 'thumb.video.activity');
							else: ?>
								<img alt="" src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Video/externals/images/video.png" class="item_photo_video  thumb_normal">
							<?php endif;?>
						</div>              
					</a>               
					<?php if($item->type == 1): ?>
						<?php $videoEmbedded = $item->compileYouTube($item->video_id, $item->code, false); ?>
					<?php  elseif($item->type == 2) :?>
						<?php $videoEmbedded = $item->compileVimeo($item->video_id, $item->code, false); ?>
					<?php  elseif($item->type == 3) :?>
						<?php  $video_location = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
						$view = false;
						$videoEmbedded = "
						<div id='videoFrame".$item->video_id."'></div>
						<script type='text/javascript'>
						en4.core.runonce.add(function(){\$('list_video_thumb_".$item->video_id."').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$item->video_id',{src: '".$this->baseUrl()."/externals/flowplayer/flowplayer-3.1.5.swf', width: ".($view?"480":"420").", height: ".($view?"386":"326").", wmode: 'opaque'},{config: {clip: {url: '$video_location',autoPlay: ".($view?"false":"true").", duration: '$item->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
						</script>"; ?>
					<?php endif ?>
        <?php endif; ?>
        
				<div id="lsit_video_object_<?php echo $item->video_id; ?>" style="display: none;" class="video_play">
					<?php echo $videoEmbedded ?>
					<div onclick=" videoclose('<?php echo $item->video_id ?>')" class="video_close"> 
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/close_icon.png"  title= "Close" />
					</div>
				</div>		
		
				<div class="list_video_thumb_options">
					<a class="buttonlink icon_lists_video" href="<?php echo $item->getHref();?>" class = 'buttonlink icon_video_edit' ><?php echo $this->translate('View Video'); ?></a>

          <?php if (($this->viewer_id) == ($this->list->owner_id)): ?>
						<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id), 'list_videospecific', true) ?>'  class='buttonlink icon_video_edit'><?php echo $this->translate('Edit Video'); ?></a>
					<?php endif; ?>

					<?php if (($this->viewer_id) == ($this->list->owner_id) || ($this->viewer_id) == ($item->owner_id)): ?>
						<?php echo $this->htmlLink(Array('action' => 'delete', 'route' => 'list_videospecific', 'listing_id' => $this->list->getIdentity(),'video_id' => $item->video_id), $this->translate("Delete Video"), array('class' => 'buttonlink icon_lists_delete smoothbox')); ?>
					<?php endif; ?>

          <?php if (!empty($this->viewer_id)): ?>
						<a class="buttonlink icon_lists_comment" href="<?php echo $item->getHref();?>#comment"><?php echo $this->translate('Comment on Video'); ?></a>
          <?php endif; ?>
				</div>

				<div class="video_info">	
	        <div class="video_title"><a href='<?php echo $item->getHref();?>'><?php echo $item->getTitle();?></a></div>

          <div class="video_desc">
						<?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description) > 349) echo "...";?>
          </div>

          <div class="list_video_stat seaocore_txt_light">
						<span class="video_views">
							<?php echo $this->translate('Added');?> <?php echo $this->timestamp(strtotime($item->creation_date)) ?> -
							<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count),$this->locale()->toNumber($item->comment_count)) ?> - 
							<?php echo $this->translate(array('%s like', '%s likes', $item->likes()->getLikeCount()),$this->locale()->toNumber($item->likes()->getLikeCount())) ?> - 
							<?php echo $this->translate(array('%s view', '%s views', $item->view_count),$this->locale()->toNumber($item->view_count)) ?>
						</span>
					
            <?php if($item->rating > 0):?>
							<span>
								<?php for($x=1; $x<=$item->rating; $x++): ?>
									<span class="rating_star_generic rating_star"></span>
								<?php endfor; ?>
								<?php if((round($item->rating)-$item->rating)>0):?>
									<span class="rating_star_generic rating_star_half"></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</div>          
		  	</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else:?>
	<?php if ($this->allowed_upload_video): ?>
		<div class="tip">
      <span>
      <?php echo $this->translate('You have not added any video in your listing. Click'); ?>
	  	<a href='<?php echo $this->url(array('action' => 'index', 'listing_id' => $this->list->listing_id, 'content_id' => $this->identity), 'list_video_upload', true) ?>'  class=''><?php echo $this->translate('here'); ?></a>
      <?php echo $this->translate(' to add your first video of listing.'); ?>
      </span>
		</div>
		<br />
	<?php endif; ?>
<?php endif; ?>

<div >
	<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
		<div id="user_group_members_previous" class="paginator_previous">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array( 'onclick' => 'paginateListVideo(listVideoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
		</div>
	<?php endif; ?>
	<?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
		<div id="user_group_members_next" class="paginator_next">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array( 'onclick' => 'paginateListVideo(listVideoPage + 1)', 'class' => 'buttonlink_right icon_next'));?>
		</div>
	<?php endif; ?>
</div>

<a id="list_video_anchor" style="position:absolute;"></a>

<script type="text/javascript">
  var listVideoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var paginateListVideo = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'page' : page
      }
    }), {
      'element' : $('list_video_anchor').getParent()
    });
  }
</script>
