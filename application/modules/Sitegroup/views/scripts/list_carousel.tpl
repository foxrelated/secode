<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $sitegroup = $this->sitegroup; ?>

<li class="seaocore_carousel_content_item_wrapper b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
  <div class="seaocore_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;">
    <center>
        <a href="<?php echo $sitegroup->getHref() ?>" class="seaocore_carousel_thumb" title="<?php echo $sitegroup->getTitle()?>">

          <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_normal.png'; $temp_url=$sitegroup->getPhotoUrl('thumb.normal'); if(!empty($temp_url)): $url=$sitegroup->getPhotoUrl('thumb.normal'); endif;?>
          
        <span style="background-image: url(<?php echo $url; ?>); "></span>
        
      </a>
    </center>
    <div class="seaocore_carousel_title">
      <?php echo $this->htmlLink($sitegroup->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitegroup->getTitle(), $this->title_truncation), array('title' => $sitegroup->getTitle())) ?>
    </div>

    <div class="seaocore_carousel_cnt clr">
      <div class="seaocore_txt_light"> 
        <a href="<?php echo $sitegroup->getCategory()->getHref() ?>"> 
          <?php echo $sitegroup->getCategory()->getTitle(true) ?>
        </a>
      </div>
      
		<?php if ($this->statistics): ?>
			<?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('likeCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>
					<?php endif; ?>
					<?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('followCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count)) ?>	
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('viewCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>
					<?php endif; ?>
					<?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics)) : ?>  - <?php endif; ?>
					<?php if(in_array('memberCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
													<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
							if ($sitegroup->member_title && $memberTitle) : ?>
							<?php echo $sitegroup->member_count . ' ' .  $sitegroup->member_title; ?>
							<?php else : ?>
							<?php echo $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
						<?php endif; ?>		<?php endif; ?>
					<?php endif; ?>		
				</div>
			<?php endif; ?>	
			<?php if(in_array('commentCount', $this->statistics) || in_array('reviewCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('commentCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?>
					<?php endif; ?>
					<?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('reviewCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')): ?>
							<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
    <?php endif; ?>
      
			<?php if(($this->sponsoredIcon && $sitegroup->sponsored) || ($this->featuredIcon && $sitegroup->featured) || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && $sitegroup->rating)): ?>
				<div class="seaocore_carousel_grid_view_list_btm b_medium">
				
					<?php if((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && $sitegroup->rating)): ?>
						<?php 
							$currentRatingValue = $sitegroup->rating;
							$difference = $currentRatingValue- (int)$currentRatingValue;
							if($difference < .5) {
								$finalRatingValue = (int)$currentRatingValue;
							}
							else {
								$finalRatingValue = (int)$currentRatingValue + .5;
							}	
						?>
						<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
							<?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
							<span class="rating_star_generic rating_star" ></span>
							<?php endfor; ?>
							<?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half" ></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
						
					<span class="fright">
						<?php if ($sitegroup->sponsored == 1 && $this->sponsoredIcon): ?>
							<i title="<?php echo $this->translate('Sponsored');?>" class="seaocore_icon seaocore_icon_sponsored"></i>
						<?php endif; ?>
						<?php if ($sitegroup->featured == 1 && $this->featuredIcon): ?>
							<i title="<?php echo $this->translate('Featured');?>" class="seaocore_icon seaocore_icon_featured"></i>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
    </div>  
  </div>
</li>
