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
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css');
?>

<?php if ($this->showContent): ?>
  <div id="user_review"></div>
  <?php if (empty($this->isajax)) : ?>
		<?php if(!empty($this->ratingDataTopbox)):?>
			<section class="sm-widget-block">
				<table class="sm-rating-table">
					<?php foreach($this->ratingDataTopbox as $reviewcatTopbox):?>
						<?php if (!empty($reviewcatTopbox['ratingparam_name'])): ?>
							<tr valign="middle">
								<td class="rating-title">
									<?php echo $reviewcatTopbox['ratingparam_name']; ?>
								</td>
								<td class="rating-title">
									<?php echo $this->showRatingStarSitestoreproductSM($reviewcatTopbox['avg_rating'], 'user', 'small-box',1, $reviewcatTopbox['ratingparam_name']); ?>
								</td>
							</tr>
						<?php else: ?>
              <tr valign="middle">
                <td class="rating-title">
                  <strong><?php echo $this->translate("Average User Rating"); ?></strong>
                </td>
                <td>
                  <?php echo $this->showRatingStarSitestoreproductSM($this->subject()->rating_users, 'user', 'big-star', 1); ?>
                </td>
							</tr>
						<?php endif;?>
					<?php endforeach;?>
          <tr>
            <td colspan="2">
								<?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), '<b>'. $this->locale()->toNumber($this->totalReviews). '</b>') ?>
            </td>
          </tr>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagereview.recommend', 1)):?>
            <tr>
              <td colspan="2">  
                <?php echo $this->translate("Recommended by ") .'<b>' .$this->recommend_percentage .'%</b>'. $this->translate(" members");?>
              </td>
            </tr> 
          <?php endif;?>
          <?php if (!empty($this->viewer_id) && $this->can_update && empty($this->isajax) && $this->reviewRateData): ?>
            <?php $rating_value_2 = 0; ?>	
            <?php if (!empty($this->reviewRateData)): ?>	
              <?php foreach ($this->reviewRateData as $reviewRateData): ?>
                <?php if ($reviewRateData['ratingparam_id'] == 0): ?>
                  <?php $rating_value_2 = $reviewRateData['rating']; ?>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
						<tr valign="top">
							<td class="rating-title">
								<b><?php echo $this->translate("My Rating"); ?></b>
							</td>
							<td>
								<?php echo $this->showRatingStarSitestoreproductSM($rating_value_2, 'user', 'big-star',1); ?>
                <?php $reviewMySelf = Engine_Api::_()->getItem('sitestoreproduct_review', $this->hasPosted); ?>
                <a href="<?php echo $reviewMySelf->getHref();?>" style="margin-top: 5px;display: block;" class="clr"><?php echo $this->translate("Go to your Review");?> </a>
							</td>
						</tr>
          <?php endif;?>
        </table>
			</section>
		<?php endif;?>
  <?php endif; ?>

  <div id="sitestoreproduct_user_review_content" class="sm-content-list">
    <?php if (( $this->paginator->getTotalItemCount() > 0)): ?>
			<ul data-role="listview" data-icon="arrow-r">
        <?php foreach ($this->paginator as $review): ?>
					<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->profileRatingbyCategory($review->review_id); ?>
						<?php $rating_value = 0; ?>
						<?php foreach ($ratingData as $reviewcat): ?>
							<?php if (empty($reviewcat['ratingparam_name'])): ?>
								<?php $rating_value = $reviewcat['rating'];
								break; ?>
							<?php endif; ?>
					<?php endforeach; ?>
          <li>
           <a href="<?php echo $review->getHref();?>">
             <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, 60)?></h3>
             <p><?php echo $this->showRatingStarSitestoreproductSM($rating_value, 'user', 'small-star',1); ?></p>
             <p><?php echo $this->timestamp(strtotime($review->modified_date)); ?> - 
						 <?php if (!empty($review->owner_id)): ?>
							<?php echo $this->translate('by'); ?> <strong><?php echo $review->getOwner()->getTitle() ?></strong></p>
						 <?php else: ?>
							<?php echo $this->translate('by'); ?> <strong><?php echo $review->anonymous_name; ?></strong></p>
						 <?php endif; ?>
						
					 </a>
					</li>
        <?php endforeach; ?>
      </ul>
    <?php elseif (!empty($this->rating_value)): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has written a review with that criteria.'); ?> 
        </span>
      </div>    
    <?php else: ?>
      <?php if ($this->can_create): ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("No reviews have been written for this Product yet."); ?>	
              <?php
              $show_link = $this->htmlLink(
                      array('action' => 'create', 'route' => "sitestoreproduct_review_general", 'product_id' => $this->product_id), $this->translate('here'));
              echo sprintf(Zend_Registry::get('Zend_Translate')->_('Click %s to write a review.'), $show_link);
              ?>
          </span>
        </div>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("No reviews have been written yet."); ?>	
          </span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
