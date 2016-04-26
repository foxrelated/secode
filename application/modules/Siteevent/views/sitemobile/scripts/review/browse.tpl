<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $count = $this->paginator->getTotalItemCount(); ?>
<?php if ($count > 0): ?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()):?>
  <div class="ui-member-list-head">
    <?php echo $this->translate(array("%s review found.", "%s reviews found.", $count), $this->locale()->toNumber($count)) ?>
  </div>
<?php endif; ?>

  <div class="sm-content-list" <?php if (Engine_Api::_()->sitemobile()->isApp() && $this->autoContentLoad == 0): ?> id="reviewlistid" <?php endif; ?>>
    <ul data-role="listview"  class="sr_reviews_listing" <?php if (Engine_Api::_()->sitemobile()->isApp() && $this->autoContentLoad == 0): ?> data-icon="angle-right" <?php else:?> data-icon="arrow-r" <?php endif; ?>>
       
        <?php foreach ($this->paginator as $review): ?>
        <li>
          <a href="<?php echo $review->getHref() ?>" >
           <?php if ($review->owner_id): ?>
              <?php echo $this->itemPhoto($review->getOwner(), 'thumb.icon', $review->getOwner()->getTitle()) ?>
           <?php else: ?>
              <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/nophoto_user_thumb_icon.png"; ?>
              <img src="<?php echo $itemphoto; ?>" />
           <?php endif; ?>

            <h3><?php echo $review->getTitle() ?></h3>
            <p><?php $ratingData = $review->getRatingData(); ?>
              <?php
              $rating_value = 0;
              foreach ($ratingData as $reviewcat):
                if (empty($reviewcat['ratingparam_name'])):
                  $rating_value = $reviewcat['rating'];
                  break;
                endif;
              endforeach;
              ?>
							<?php echo $this->showRatingStarSiteeventSM($reviewcat['rating'], $review->type, 'small-star'); ?>
            </p>

            <!--END RATING WORK-->
            <p>
						<?php echo $this->translate('For'); ?>  
              <strong><?php echo $review->getParent()->getTitle(); ?></strong>
            </p> 
            <p>
              <?php echo $this->timestamp(strtotime($review->modified_date)); ?> -
              <?php if (!empty($review->owner_id)): ?>
                <?php echo $this->translate('by'); ?> <strong><?php echo $review->getOwner()->getTitle() ?></strong> <?php
           if ($review->type == 'editor'):
            echo "(" . $this->translate('Editor') . ")";
           endif;
                ?>
                <?php ?>
              <?php else: ?>
                <?php echo $this->translate('by'); ?><strong> <?php echo $review->anonymous_name; ?> </strong>
    <?php endif; ?>
           </p>

<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?> 
    <?php if ($review->body): ?>
              <p class="feed_item_link_desc">
                <b><?php echo ($review->type == 'user' || $review->type == 'visitor') ? $this->translate("Summary:") : $this->translate("Conclusion:") ?>                 </b>
                <?php
                $truncation_limit = 100;
                $tmpBody = strip_tags($review->body);
                echo ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... " : $tmpBody );
                ?>
              </p>
    <?php endif; ?>
 <?php endif; ?>
          </a>
        </li>
  <?php endforeach; ?>
         
    </ul>
  </div>
  <?php if (($this->paginator->count() > 1) && !Engine_Api::_()->sitemobile()->isApp()): ?>
    <br />
    <?php
    echo $this->paginationControl(
            $this->paginator, null, null, array(
        'pageAsQuery' => false,
        'query' => $this->searchParams
    ));
    ?>
  <?php endif; ?>
<?php elseif ($this->searchParams): ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('Nobody has written a review with that criteria.'); ?> 
    </span>
  </div>    
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No reviews have been written yet.'); ?>
    </span>
  </div>
<?php endif; ?>

    <script type="text/javascript">

	
<?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
     <?php $current_url = $this->url(array('action' => 'browse')); ?>    
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'reviewlistid' };  
          });
         
   <?php endif; ?>    
</script>