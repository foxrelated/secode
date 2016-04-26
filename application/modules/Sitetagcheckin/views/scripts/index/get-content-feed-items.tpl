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
  
  <?php if(empty($this->actions)):?>
   	<div class="tip">
      <span>
         <?php echo $this->translate("There are no feed to display.");?>
      </span>
    </div>
  <?php return;?>
  <?php endif;?>

	<?php	echo $this->sitetagcheckinActivityLoop($this->actions, array(
			'show_map' => 0,
      'is_ajax' => $this->is_ajax,
      'sitetagcheckin_id' => 'feed_item'
		));
	?>

	<script type="text/javascript">
		var feedPage = <?php echo sprintf('%d', $this->actions->getCurrentPageNumber()) ?>;
		var paginateMapFeeds = function(page) 
		{
			var url = '<?php echo $this->url(array('action' => 'get-feed-items'), 'sitetagcheckin_general', true);?>';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'is_ajax' : '1',
					'page' : page,
          'show_map' : '0'
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

	<?php if( $this->actions->getTotalItemCount() > 1 ):?>
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
	<?php endif; ?>

</div>