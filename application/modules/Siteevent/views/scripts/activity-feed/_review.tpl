<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _review.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_review_rich_content">
    <div class="siteevent_review_rich_content_title">
        <span class="fright">
            <?php echo $this->ShowRatingStarSiteevent($this->ratingValue, $this->review->type, 'small-star'); ?>
        </span>
        <?php echo $this->htmlLink($this->review->getHref(), $this->review->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->review->getType() . ' ' . $this->review->getIdentity())) ?>
    </div>
    <div class="siteevent_review_rich_content_stats">
        <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->body, 50)) ?>
    </div>
</div>