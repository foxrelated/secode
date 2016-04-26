<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: related-album.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="text/javascript">
  function viewMore() {
    if ($('view_more'))
    $('view_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>"; 
    document.getElementById('view_more').style.display = 'none';
    document.getElementById('loading_image').style.display = '';
    var id = '<?php echo $this->album_id; ?>';
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sesalbum/album/related-album/album_id/' + id ,
      'data': {
        format: 'html',
        page: "<?php echo $this->page; ?>",
        viewmore: 1        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('related_results').innerHTML = document.getElementById('related_results').innerHTML + responseHTML;
        document.getElementById('view_more').destroy();
        document.getElementById('loading_image').style.display = 'none';
      }
    })).send();
    return false;
  }
var reloadSes = false;
<?php if (empty($this->viewmore)): ?>
function relatedAlbumSave(id){
	reloadSes = true;
	if(sesJqueryObject('#ses-rated-album-'+id).prop('checked'))
		var updateStatus = 'add';
	else
		var updateStatus = 'delete';
	sesJqueryObject('#ses-rated-image-'+id).show();
	sesJqueryObject('#ses-rated-album-'+id).css('display','none');
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sesalbum/album/save-related-album/album_id/<?php echo $this->album_id; ?>' ,
      'data': {
        format: 'html',
				id : id,
        data: updateStatus,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        sesJqueryObject('#ses-rated-image-'+id).hide();
				sesJqueryObject('#ses-rated-album-'+id).css('display','block');
      }
    })).send();
	return false;
}
<?php endif; ?>
</script>
<?php if (empty($this->viewmore)): ?>
	<form id="relatedFormSave">
  <div class="sesbasic_items_listing_popup">
    <div class="sesbasic_items_listing_header">
         <?php echo $this->translate("Related Albums"); ?>
      <a class="fa fa-close" href="javascript:;" onclick='smoothboxclose();' title="<?php echo $this->translate('Close') ?>"></a>
    </div>
    <div class="sesbasic_items_listing_cont sesbasic_bxs" id="related_results">
<?php endif; ?>
    <?php if (count($this->paginator) > 0) : ?>
      <?php foreach ($this->paginator as $value): ?>
        <div class="item_list item_list_box">
        	<div class="item_list_checkbox">
          	<input type="checkbox" name="relatedAlbums[]" onclick="relatedAlbumSave(<?php echo $value->album_id ?>)" id="ses-rated-album-<?php echo $value->album_id ?>" value="<?php echo $value->album_id; ?>" <?php if(!is_null($value->relatedalbum_id) && $value->relatedalbum_id != ''){ ?> checked="checked" <?php } ?> />
            <img id="ses-rated-image-<?php echo $value->album_id ?>" style="display:none" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
          </div>
          <div class="item_list_thumb">
          	<a href="<?php echo $value->getHref(); ?>" target="_blank" title="<?php echo $value->getTitle(); ?>">
            	<img src="<?php echo $value->getPhotoUrl('thumb.normalmain'); ?>" style="height:75px; width:75px;margin-right:10px;" />
            </a>
          </div>
          <div class="item_list_info">
            <div class="item_list_title">
              <?php echo $this->htmlLink($value->getHref(), $value->getTitle(), array('title' => $value->getTitle(), 'target' => '_parent')); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?> 
      <?php endif; ?>     
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1 && empty($this->viewmore)): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="sesbasic_view_more" id="view_more" onclick="viewMore();" >
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="loading_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sesbasic/externals/images/loading.gif' alt="Loading" />
          <?php echo $this->translate("Loading ...") ?>
        </div>
  <?php endif; ?>
     </div>
    </div>
</form>
<?php endif; ?>
<script type="text/javascript">
  function smoothboxclose() {
	 if(reloadSes)
		parent.window.location.reload(true);
    parent.Smoothbox.close();
  }
</script>