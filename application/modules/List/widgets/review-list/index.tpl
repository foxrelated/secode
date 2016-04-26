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

<a id="list_review_anchor" style="position:absolute;"></a>
<script type="text/javascript">
  var listReviewPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var paginateListReview = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'page' : page
      }
    }), {
      'element' : $('list_review_anchor').getParent()
    });
  }
</script>

<?php if ($this->viewer()->getIdentity() || $this->paginator->count() > 1): ?>
  <div class="seaocore_add">
		<?php if ($this->viewer()->getIdentity()): ?>
		<?php
			echo $this->htmlLink(array(
													'route' => 'list_extended',
													'controller' => 'review',
													'action' => 'create',
													'subject' => $this->subject()->getGuid(),
													'content_id' => $this->identity,
											), $this->translate('Post a Review'), array(
													'class' => 'smoothbox icon_lists_review buttonlink'));
			?>
		<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<?php if ($this->paginator->count() > 1): ?>
  <div>
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
      <div id="user_group_members_previous" class="paginator_previous">
			<?php
					echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
									'onclick' => 'paginateListReview(listReviewPage - 1)',
									'class' => 'buttonlink icon_previous'
					));
			?>
      </div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
    <div id="user_group_members_next" class="paginator_next">
      <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => 'paginateListReview(listReviewPage + 1)',
                    'class' => 'buttonlink_right icon_next'
            ));
      ?>
    </div>
		<?php endif; ?>
   </div>
  <?php endif; ?>
<ul class="list_profile_review">
	<?php foreach ($this->paginator as $review): ?>
	  	<li>
	    	<?php echo $this->htmlLink($review->getOwner()->getHref(), $this->itemPhoto($review->getOwner(), 'thumb.icon')) ?>
				<div class="list_profile_review_options">
		      <?php if ($this->viewer->getIdentity() == $review->owner_id): ?>
		      <?php
		                echo $this->htmlLink(array(
		                        'route' => 'list_extended',
		                        'controller' => 'review',
		                        'action' => 'edit',
		                        'subject' => $this->subject()->getGuid(),
		                        'id' => $review->review_id,
		                        'content_id' => $this->identity,
		                    ), $this->translate('Edit Review'), array(
		                        'class' => 'smoothbox buttonlink icon_lists_edit'
		                )); ?>
		      <?php endif; ?>
		      <?php if ($this->viewer->getIdentity() == $review->owner_id ||  $this->level_id == 1 ): ?>
		      <?php
		                  echo $this->htmlLink(
		                      array(
		                              'route' => 'list_extended',
		                              'controller' => 'review',
		                              'action' => 'delete',
		                              'subject' => $this->subject()->getGuid(),
		                              'id' => $review->review_id,
		                              'content_id' => $this->identity,
		                      )
		                      , $this->translate('Delete Review'), array(
		                          'class' => 'smoothbox buttonlink icon_lists_delete',
		                  )); ?>
		      <?php endif; ?>
				</div>
				<div class="list_profile_review_info">
		    	<div class="title"> <?php echo $this->translate($review->title) ?> </div>
		      <div class="list_profile_review_info_date seaocore_txt_light">
		      	
		      	 <?php echo $this->timestamp(strtotime($review->modified_date)) ?>
              -
              <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
		      </div>	
		      <div class='list_profile_review_info_desc'>
		      	<?php echo $this->viewMore($review->body) ?>
	        </div>
				</div>
	    </li>
	  <?php endforeach; ?>
</ul>
  <?php if ($this->paginator->count() > 1): ?>
   <div >
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
      <div id="user_group_members_previous" class="paginator_previous">
      <?php
                      echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                              'onclick' => 'paginateListReview(listReviewPage - 1)',
                              'class' => 'buttonlink icon_previous'
                      )); ?>
			</div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
       <div id="user_group_members_next" class="paginator_next">
      <?php  echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                                'onclick' => 'paginateListReview(listReviewPage + 1)',
                                'class' => 'buttonlink_right icon_next'
                        ));?>
				</div>
    <?php endif; ?>
   </div>
 <?php endif; ?>
  <?php else: ?>
  <?php if ($this->viewer()->getIdentity())?>
    <div class="tip">
      <span>
<?php
$show_link = $this->htmlLink(array('route' => 'list_extended', 'controller' => 'review', 'action' => 'create', 'subject' => $this->subject()->getGuid(), 'content_id' => $this->identity,),$this->translate('here'),array('class' => 'smoothbox'));
	$show_label = Zend_Registry::get('Zend_Translate')->_('No reviews have been posted for this listing yet. Click %s to post your review.');
	$show_label = sprintf($show_label, $show_link);
	echo $show_label;
?>
      </span>
   </div>
  <?php endif; ?>