<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _userreview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_review_rich_content">
    <div class="siteevent_review_rich_content_title">
			<span class="fright">
					<?php echo $this->ShowRatingStarSiteevent($this->ratingValue, 'user', 'small-star'); ?>
			</span>
			<?php echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $this->event_id, 'user_id' => $this->user_id), $this->review->title); ?>
    </div>
    <div class="siteevent_review_rich_content_stats">
        <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->description , 50)) ?>
    </div>
</div>