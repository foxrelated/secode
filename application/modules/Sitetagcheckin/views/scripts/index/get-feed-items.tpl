<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitetagcheckin
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: get-feed-items.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div id= "sitetagcheckin_feeds" class="stcheckin_feeds">

	<?php if($this->actions && $this->actions->getTotalItemCount() <= 0):?>
			<div class="tip">
				<span>
					<?php echo $this->translate("There are currently no feeds for check-ins here.");?>
				</span>
			</div>
		<?php return;?>
  <?php endif;?>

	<?php	echo $this->SitetagcheckinActivityLoop($this->actions, array(
			'show_map' => 0,
      'is_ajax' => $this->is_ajax,
      'sitetagcheckin_id' => 'feed_item'
		));
	?>

	<script type="text/javascript">

	 var feedPage = 1;
   <?php if($this->actions && $this->actions->getCurrentPageNumber()) :?>
      feedPage = <?php echo sprintf('%d', $this->actions->getCurrentPageNumber()) ?>;
		<?php endif;?>

		var paginateMapFeeds = function(page) 
		{
      $('show-background-pagination-image').style.display = "block";
			en4.core.request.send(new Request.HTML({
				'url' : '<?php echo $this->url(array('action' => 'get-feed-items'), 'sitetagcheckin_general', true);?>',
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'is_ajax' : '1',
					'page' : page,
          'show_map' : '0',
          'content_feeds' : '<?php echo $this->content_feeds?>'
				},
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
           $('sitetagcheckin_feeds').innerHTML=responseHTML;
           location.hash = 'display_maplinks';
					 Smoothbox.bind($("feed_items"));
					 en4.core.runonce.trigger();
        }
			}), {
				'element' : $('sitetagcheckin_feeds')
			}, {"force":true});
		}
	</script>

	<?php if( $this->actions && $this->actions->getTotalItemCount() > 1 ):?>
		<div>
			<?php if( $this->actions->getCurrentPageNumber() > 1 ): ?>
				<div id="sitetagcheckin_members_previous" class="paginator_previous">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
						'onclick' => 'paginateMapFeeds(feedPage - 1)',
						'class' => 'buttonlink icon_previous'
					)); ?>
				</div>
			<?php endif; ?>
			<?php if( $this->actions->getCurrentPageNumber() < $this->actions->count() ): ?>
				<div id="sitetagcheckin_members_next" class="paginator_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
						'onclick' => 'paginateMapFeeds(feedPage + 1)',
						'class' => 'buttonlink_right icon_next'
					)); ?>
				</div>
			<?php endif; ?>
		</div>
		<div id="show-background-pagination-image" style="display:none"> 
			<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" /></center>
		</div>
	<?php endif; ?>

</div>