<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mapInfoWindowContent.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if (!isset($this->showEventType)) {
    $this->showEventType = '';
}
?>

<div id="content">
    <div id="siteNotice">
    </div>
    <div class="siteevent_map_info_tip o_hidden">
        <div class="siteevent_map_info_tip_top o_hidden">
            <div class="fright">
                <span >
                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $this->siteevent->featured): ?>
                        <i class="siteevent_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
                    <?php endif; ?>
                </span>
                <span>
                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($this->siteevent->sponsored)): ?>
                        <i class="siteevent_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
<?php endif; ?>
                </span>
            </div>
            <div class="siteevent_map_info_tip_title">
<?php echo $this->htmlLink($this->siteevent->getHref(array('showEventType' => $this->showEventType)), $this->siteevent->getTitle()) ?>
            </div>
        </div>
        <div class="siteevent_map_info_tip_photo prelative" >
            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $this->siteevent->newlabel): ?>
                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
            <?php endif; ?>
<?php echo $this->htmlLink($this->siteevent->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($this->siteevent, 'thumb.normal')) ?>
            <?php //echo $this->htmlLink($this->siteevent->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($this->siteevent, 'sr.thumb.normal'))   ?>
        </div>
        <div class="siteevent_map_info_tip_info">
        
        <?php if($this->temp) :?>
						<?php $totalReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->totalReviews($this->siteevent->event_id, $this->subject->getIdentity()); ?>
					<?php if($totalReviews): ?>
						<div class="siteevent_listings_stats ">
						<i title="<?php echo $this->translate('As Guest'); ?>" class="siteevent_icon_strip siteevent_icon siteevent_icon_user"></i>
						<div class="o_hidden">
							<?php echo $this->translate("Score from "); ?><?php 	echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $this->siteevent->event_id, 'user_id' => $this->subject->getIdentity()), $this->translate(array('%s review', '%s reviews', $totalReviews), $this->locale()->toNumber($totalReviews))); ?>:
							<span class="clr mtop5"><?php $averageUserReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->averageUserRatings(array('user_id' => $this->subject->getIdentity(), 'event_id' => $this->siteevent->event_id));
							echo $this->ShowRatingStarSiteevent($averageUserReviews, 'user', 'small-star',null, false, false); ?></span>
						</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				
            <?php if (!empty($this->statistics)) : ?>
    <?php echo $this->eventInfo($this->siteevent, $this->statistics, array('ratingShow' => $this->ratingShow, 'ratingValue' => $this->ratingValue, 'ratingType' => $this->ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
<?php endif; ?>
        </div>

    </div>
</div>