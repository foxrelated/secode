<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: edit.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');
   ?>     
<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="list_edit_wrapper">
	<h3> <?php echo $this->translate('Edit Listing Videos'); ?></h3>
	
	<?php echo $this->translate('Edit and manage the videos of your listing below.'); ?><br /><br />
	
	<div>
		<?php echo $this->htmlLink(array('route' => 'list_video_upload', 'action' => 'index', 'listing_id' => $this->list->listing_id), $this->translate('Add New Videos'), array('class' => 'buttonlink icon_photos_new')) ?>
	</div>
	
	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="">
		<div>
			<div>
				<ul class='lists_editvideos' id="video">
					<?php if(!empty($this->count)): ?>
						<?php foreach ($this->videos as $item): ?>
							<li>
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
		              <?php
		                if ($item->photo_id)
		                  echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array());
		                else
		                  echo '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
		              ?>
	            	</div>
		            <div class="lists_editvideos_info">
		              <?php $key= $item->getGuid();
			              echo $this->form->getSubForm($key)->render($this);
		              ?>
		            </div>
		          </li>
	          <?php endforeach; ?>
					<?php else:?><br />
						<div class="tip">
							<span>
								<?php echo $this->translate('There are currently no videos in this listing. Click'); ?>
								<a href='<?php echo $this->url(array('action' => 'index', 'listing_id' => $this->list->listing_id), 'list_video_upload', true) ?>'  class=''><?php echo $this->translate('here'); ?></a>
								<?php echo $this->translate(' to add videos now!'); ?>
							</span>
						</div>
					<?php endif;?>
				</ul>
	      <?php if(!empty($this->count)): ?>
					<?php echo $this->form->button ?>
				<?php endif;?>
			</div>
		</div>
	</form>
</div>	