<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
  $this->headLink()
				->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>
<?php if(empty($this->is_ajax)):?>
	<div id='editorReviewCommentContent' class="o_hidden">
<?php endif;?>

<?php  if(count($this->replies) > 0):?>
  <?php if(empty($this->is_ajax)):?>
    <ul class="sr_sitestoreproduct_editor_profile_content o_hidden" id="sr_sitestoreproduct_editor_profile_content">
  <?php endif;?>
  <?php foreach ($this->replies as $reply): ?>
   <?php $parentItem = $reply->getParent();?>
    <li>
      <div class="sr_sitestoreproduct_editor_profile_content_comment">"<?php echo $this->smileyToEmoticons($this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($reply->body, $this->truncation))) ?>"</div>
      <div class="clr seaocore_txt_light sr_sitestoreproduct_editor_profile_content_comment_stat">
         <?php if(!empty($reply->parent_comment_id)):?>
          <?php echo $this->translate("Replied On");?>
         <?php else:?>
           <?php echo $this->translate("Commented On");?>
         <?php endif;?>
        <?php echo $this->htmlLink($parentItem->getHref(). '#comments_'. $parentItem->getGuid(). '_0', $parentItem->getTitle());?>,
        <?php echo $this->timestamp(strtotime($reply->creation_date)) ?>
      </div>
    </li>
  <?php endforeach;?>
  <?php if(empty($this->is_ajax)):?>
    </ul>
  <?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("This Editor has not posted any comments yet!"); ?>
    </span> 
  </div>
<?php endif; ?>

<?php if(empty($this->is_ajax)):?>
	</div>
<?php endif;?>

<?php if(empty($this->is_ajax)):?>
	<div class="seaocore_view_more" id="profile_view_more" onclick="viewMoreReply()">
		<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
			'id' => 'feed_viewmore_link',
			'class' => 'icon_viewmore'
		)) ?>
	</div>
	<div class="seaocore_loading" id="loding_image" style="display: none;">
		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
		<?php echo $this->translate("Loading ...") ?>
	</div>
<?php endif;?>

<script type="text/javascript">
 	
	function viewMoreReply() {
		$('profile_view_more').style.display ='none';
		$('loding_image').style.display ='';
		en4.core.request.send(new Request.HTML({
			'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/editor-replies-sitestoreproduct',
			'data' : {
				format : 'html',
				is_ajax : 1,
				page: getNextPage(),
        subject: en4.core.subject.guid,
        itemCount: '<?php echo $this->itemCount?>',
        onlyProducttypeEditor: '<?php echo $this->onlyProducttypeEditor?>'
			},
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('loding_image').style.display ='none';
				Elements.from(responseHTML).inject($('sr_sitestoreproduct_editor_profile_content'));
      }
		}));
		return false;
	}
  
  en4.core.runonce.add(function() {
    hideViewMoreLink();
  });

	function hideViewMoreLink(){
		$('profile_view_more').style.display = '<?php echo ( $this->replies->count() == $this->replies->getCurrentPageNumber() || $this->replyCount == 0 ? 'none' : '' ) ?>';
	}

	function getNextPage(){
		return <?php echo sprintf('%d', $this->page + 1) ?>
	}
</script>
