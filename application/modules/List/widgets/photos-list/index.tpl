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

<a id="list_photo_anchor" style="position:absolute;"></a>
<script type="text/javascript">
  var listPhotoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var paginateListPhoto = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'page' : page
      }
    }), {
      'element' : $('list_photo_anchor').getParent()
    });
  }
</script>
<?php if ($this->total_images): ?>
  <?php if ($this->allowed_upload_photo): ?>
		<div class="seaocore_add">
	  	<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id,'content_id' => $this->identity), 'list_photoalbumupload', true) ?>'  class='buttonlink icon_lists_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
	  </div>
	<?php endif; ?>
	<ul class="thumbs">
		<?php foreach ($this->paginator as $image): ?>
	  	<li>
	    	<a href="<?php echo $this->url(array('listing_id' => $image->album_id, 'photo_id' => $image->photo_id), 'list_image_specific') ?>" <?php if(SEA_LIST_LIGHTBOX) :?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('listing_id' => $image->album_id, 'photo_id' => $image->photo_id), 'list_image_specific') ?>");return false;' <?php endif;?> class="thumbs_photo" title="<?php echo $image->title ?>">
	      	<span style="background-image: url(<?php echo $image->getPhotoUrl('thumb.normal'); ?>);"></span>
	      </a>
	  	</li>
	  <?php endforeach; ?>
  </ul>
<?php else: ?>
 	<?php if ($this->allowed_upload_photo): ?>
    <div class="seaocore_add">
	  	<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id), 'list_photoalbumupload', true) ?>'  class='buttonlink icon_lists_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
    </div>
    <div class="tip">
      <span>
      <?php echo $this->translate('You have not added any photo in your listing. Click'); ?>
	  	<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id), 'list_photoalbumupload', true) ?>'  class=''><?php echo $this->translate('here'); ?></a>
      <?php echo $this->translate(' to add your first photo of listing.'); ?>
      </span>
		</div>
	<?php endif; ?>
<?php endif; ?>
<?php if ($this->paginator->count() > 1): ?>
   <div >
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
       <div id="user_group_members_previous" class="paginator_previous">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateListPhoto(listPhotoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
      <div id="user_group_members_next" class="paginator_next">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateListPhoto(listPhotoPage + 1)','class' => 'buttonlink_right icon_next'));?>
        </div>
			<?php endif; ?>
   </div>
  <?php endif; ?>