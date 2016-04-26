<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="heading" id="bread_crum"></div>
<?php $categoryRouteName = Engine_Api::_()->siteevent()->getCategoryHomeRoute(); ?>
<?php $count = $this->paginator->getTotalItemCount(); ?>
<?php if ($count > 0): ?>
    <?php
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css')
            ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
    ?>
    <div class="siteevent_browse_lists_view_options mbot15 b_medium">
        <div> 
            <?php echo $this->translate(array("%s review found.", "%s reviews found.", $count), $this->locale()->toNumber($count)) ?>
        </div>
    </div>
    <ul class="siteevent_reviews_event">
        <?php foreach ($this->paginator as $review): ?>
            <li>
                <div class="siteevent_reviews_event_photo">
                    <?php if ($review->owner_id): ?>
                        <?php echo $this->htmlLink($review->getOwner($review->type)->getHref(), $this->itemPhoto($review->getOwner(), 'thumb.icon', $review->getOwner()->getTitle()), array('class' => "thumb_icon")) ?>
                    <?php else: ?>
                        <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/nophoto_user_thumb_icon.png"; ?>
                        <img src="<?php echo $itemphoto; ?>" />
                    <?php endif; ?>
                </div>
                <div class="siteevent_reviews_event_info">
                    <div class="siteevent_reviews_event_title">
                        <div class="siteevent_ur_show_rating_star">
                            <?php $ratingData = $review->getRatingData(); ?>
                            <?php
                            $rating_value = 0;
                            foreach ($ratingData as $reviewcat):
                                if (empty($reviewcat['ratingparam_name'])):
                                    $rating_value = $reviewcat['rating'];
                                    break;
                                endif;
                            endforeach;
                            ?>
                            <span class="fright">
                                <span class="fleft">
                                    <?php echo $this->ShowRatingStarSiteevent($rating_value, $review->type, 'big-star'); ?>
                                </span>
                                <?php if (count($ratingData) > 1): ?>
                                    <i class="fright arrow_btm"></i>
                                <?php endif; ?>
                            </span>
                            <?php if (count($ratingData) > 1): ?>
                                <div class="siteevent_ur_show_rating  br_body_bg b_medium">
                                    <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">
                                        <?php foreach ($ratingData as $reviewcat): ?>
                                            <div class="o_hidden">
                                                <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                    <div class="parameter_title">
                                                        <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                    </div>
                                                    <div class="parameter_value">
                                                        <?php echo $this->ShowRatingStarSiteevent($reviewcat['rating'], $review->type, 'small-box', $reviewcat['ratingparam_name']); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="parameter_title">
                                                        <?php echo $this->translate("Overall Rating"); ?>
                                                    </div>	
                                                    <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                        <?php echo $this->ShowRatingStarSiteevent($reviewcat['rating'], $review->type, 'big-star'); ?>
                                                    </div>
                                                <?php endif; ?> 
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?> 
                        </div>
                        <?php if ($review->featured): ?>
                            <i class="siteevent_icon seaocore_icon_featured fright" title="<?php echo $this->translate('Featured'); ?>"></i> 
                        <?php endif; ?>	
                        <?php echo $this->htmlLink($review->getHref(), $review->getTitle(), array()) ?>
                    </div>

                    <div class="siteevent_reviews_event_stat seaocore_txt_light">
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1) && $review->recommend && ($review->type == 'user' || $review->type == 'visitor')): ?>
                            <span class="siteevent_reviews_event_recommended">
                                <span> <?php echo$this->translate('Recommended'); ?> </span>
                                <span class='siteevent_icon_tick siteevent_icon'></span>
                            </span>
                        <?php endif; ?>
                        <?php echo $this->timestamp(strtotime($review->modified_date)); ?> - 
                        <?php if (!empty($review->owner_id)): ?>
                            <?php echo $this->translate('by'); ?> <?php echo $this->htmlLink($review->getOwner($review->type)->getHref(), $review->getOwner()->getTitle()) ?> <?php
                            if ($review->type == 'editor'):
                                echo "(" . $this->translate('Editor') . ")";
                            endif;
                            ?>
                        <?php endif; ?>
                    </div> 

                    <div class="o_hidden">
                            <?php $siteevent = $review->getParent() ?>
                        <div class="siteevent_reviews_event_photo">
                            <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.icon', $siteevent->getTitle()), array('class' => "thumb_icon")) ?>
                        </div>
                        <div class="siteevent_reviews_event_info">
                            <div class="siteevent_reviews_event_stat seaocore_txt_light">
                                <?php echo $this->translate('For'); ?>  
                                    <?php echo $this->htmlLink($siteevent->getHref(), $siteevent->getTitle()) ?><br />
                                <a href="<?php echo $this->url(array('category_id' => $siteevent->category_id, 'categoryname' => $siteevent->getCategory()->getCategorySlug()), $categoryRouteName); ?>"> 
        <?php echo $this->translate($siteevent->getCategory()->getTitle(true)) ?>
                                </a>
                            </div>
                        </div> 
                    </div> 

                    <div class="clr"></div>
                    <?php if ($review->pros): ?>
                        <div class="siteevent_reviews_event_proscons">
                            <b><?php echo ($review->type == 'user' || $review->type == 'visitor') ? $this->translate("Pros:") : $this->translate("The Good:") ?></b>
                        <?php echo $this->viewMore($review->pros) ?> 
                        </div>
                    <?php endif; ?>
                    <?php if ($review->cons): ?>
                        <div class="siteevent_reviews_event_proscons"> 
                            <b><?php echo ($review->type == 'user' || $review->type == 'visitor') ? $this->translate("Cons:") : $this->translate("The Bad:") ?></b>
                        <?php echo $this->viewMore($review->cons) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($review->body): ?>
                        <div class="feed_item_link_desc">
                            <b><?php echo ($review->type == 'user' || $review->type == 'visitor') ? $this->translate("Summary:") : $this->translate("Conclusion:") ?></b>
                            <?php
                            $truncation_limit = 300;
                            $tmpBody = strip_tags($review->body);
                            echo ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... " . $this->htmlLink($review->getHref(), $this->translate('Read complete review'), array('title' => '')) : $tmpBody );
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="siteevent_reviews_event_option b_medium">
                        <ul>
                            <li> 
                                <div> 
                                    <div id="review_helpful_<?php echo $review->review_id; ?>" style="display:block;">
                                        <span><?php echo $this->translate("Was this review helpful?"); ?></span> 
                                        <a href="javascript:void(0)" onclick="reviewHelpful(1, '<?php echo $review->review_id; ?>');" title="<?php echo $this->translate('Yes'); ?>"><i class="thumbup"></i></a>
        <?php echo $review->getCountHelpful(1) ?>

                                        <a href="javascript:void(0)" onclick="reviewHelpful(2, '<?php echo $review->review_id; ?>');" title="<?php echo $this->translate('No'); ?>"><i class="thumbdown"></i> </a>
                                    <?php echo $review->getCountHelpful(2) ?>
                                    </div>
        <?php if ($this->viewer_id): ?>
                                        <div id="review_helpful_message_<?php echo $review->review_id; ?>" style="display:none;">
                                            <i class="siteevent_icon siteevent_icon_tick fleft mright5"></i>
                                        <?php echo $this->translate("Thanks for your feedback!"); ?>
                                        </div>
                                        <?php endif; ?>
                                    <div id="review_helpful_already_message_<?php echo $review->review_id; ?>" style="display:none;">
        <?php echo $this->translate("You have already submitted your feedback for this Review!"); ?>
                                    </div>
                                </div> 
                            </li>
                        </ul>
                        <div class="action_link">
                            <?php if ($this->viewer_id): ?>
                                <?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $review->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report Review"), 'class' => "seaocore_icon_report smoothbox")) ?>
                            <?php endif; ?>
                            <?php if ($review->owner_id != 0 && (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.share', 1) && !empty($this->viewer_id))): ?>
                                <?php echo $this->htmlLink($this->url(array('action' => 'share', 'module' => 'seaocore', 'controller' => 'activity', 'type' => $review->getType(), 'id' => $review->review_id, 'format' => 'smoothbox', 'not_parent_refresh' => 1), 'default', true), $this->translate("Share Review"), array('title' => $this->translate("Share Review"), 'class' => "seaocore_icon_share smoothbox")) ?>
                            <?php endif; ?>
        <?php echo $this->htmlLink($review->getHref(), $this->translate("Permalink"), array('title' => $this->translate("Permalink"), 'class' => "siteevent_icon_link")) ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($this->paginator->count() > 1): ?>
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
    var active_request_review = false;
    function reviewHelpful(option, review_id) {
        if (active_request_review)
            return;
<?php if (!$this->viewer_id): ?>
            window.location.href = en4.core.baseUrl + 'siteevent/review/helpful/review_id/' + review_id + '/helpful/' + option + '/anonymous/1';
<?php endif; ?>
        active_request_review = true;
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'siteevent/review/helpful',
            data: {
                format: 'html',
                review_id: review_id,
                helpful: option
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.already_entry == 0 && $('review_helpful_message_' + review_id)) {
                    $('review_helpful_message_' + review_id).style.display = 'block';
                    $('review_helpful_already_message_' + review_id).style.display = 'none';
                } else if ((responseJSON.already_entry == 1 || responseJSON.already_entry == 2) && $('review_helpful_already_message_' + review_id)) {
                    $('review_helpful_message_' + review_id).style.display = 'none';
                    $('review_helpful_already_message_' + review_id).style.display = 'block';
                }
                $('review_helpful_' + review_id).style.display = 'none';
                active_request_review = false;
            }
        });
        request.send();
        return false;
    }
</script>