<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>
<?php if ($this->total_images): ?>
	 <?php if ($this->allowed_upload_photo && Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
		<div class="profile-content-top-button" data-role="controlgroup" data-type="horizontal">
			<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id,'content_id' => $this->identity), "siteevent_photoalbumupload", true) ?>'  class='buttonlink icon_siteevents_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
		</div>
	<?php endif; ?>

		<ul class="thumbs thumbs_nocaptions" id="profile_siteeventalbums">
			<?php foreach ($this->paginator as $album): ?>
				<li>
					<a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
					</a> 
				</li>
			<?php endforeach; ?>   
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_siteeventalbums', array('itemCount' => $this->itemCount));
			?>
		<?php endif; ?>
<?php else: ?>
	<div class="tip">
		<span>
				<?php echo $this->translate('You have not added any photo.');?>
		</span>
	</div>
	<div class="tip">
		<span>
			 <?php $url = $this->url(array('event_id' => $this->siteevent->event_id), "siteevent_photoalbumupload", true); ?>
       <?php echo $this->translate('You have not added any photo in your event. %1$sClick here%2$s to add your first photo.', "<a href='$url'>", "</a>"); ?>
		</span>
	</div>
<?php endif; ?>

<style type="text/css">

.layout_siteevent_photos_siteevent > h3 {
	display:none;
}

</style>