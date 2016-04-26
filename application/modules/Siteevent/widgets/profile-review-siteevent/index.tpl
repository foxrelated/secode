<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>



<?php $review = $this->reviews; ?>
<?php $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent'); ?>
<?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'siteevent'); ?>
<?php $reviewDescriptionsTable = Engine_Api::_()->getDbtable('reviewDescriptions', 'siteevent'); ?>

<div id="profile_review" class="pabsolute"></div>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>

<div class="o_hidden">
    <div class="siteevent_view_top">
        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', $this->siteevent->getTitle()), array('class' => "thumb_icon", 'title' => $this->siteevent->getTitle())) ?>
        <div class="siteevent_review_view_right">
            <?php echo $this->content()->renderWidget("siteevent.review-button", array('event_guid' => $this->siteevent->getGuid(), 'event_profile_page' => 1, 'identity' => $this->identity)) ?>
            <?php if ($this->price > 0): ?>
                <div class="siteevent_price mtop10">
                    <?php echo $this->locale()->toCurrency($this->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
                </div>   
            <?php else: ?> 
                <div class="siteevent_price mtop10 siteevent_listings_price_free">
                    <?php echo $this->translate("FREE"); ?>
                </div> 
						<?php endif;?>
        </div>
        <h2>
            <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
        </h2>

    </div>

    <div class="siteevent_profile_review b_medium siteevent_review_block">
        <div class="siteevent_profile_review_left">
            <div class="siteevent_profile_review_title">
                <?php if (empty($reviewcatTopbox['ratingparam_name'])): ?>
                    <?php echo $this->translate("Average User Rating"); ?>
                <?php endif; ?>
            </div>
            <?php $iteration = 1; ?>
            <div class="siteevent_profile_review_stars">
                <span class="siteevent_profile_review_rating">
                    <span class="fleft">
                        <?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_users, 'user', 'big-star'); ?>
                    </span>
                    <?php if (count($this->ratingDataTopbox) > 1): ?>
                        <i class="arrow_btm fleft"></i>
                    <?php endif; ?>
                </span>	
            </div>

            <?php if (count($this->ratingDataTopbox) > 1): ?>
                <div class="siteevent_ur_bdown_box_wrapper br_body_bg b_medium">
                    <div class="siteevent_ur_bdown_box">
                        <div class="siteevent_profile_review_title">
                            <?php echo $this->translate("Average User Rating"); ?>
                        </div>
                        <div class="siteevent_profile_review_stars">
                            <?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_users, 'user', 'big-star'); ?>
                        </div>

                        <div class="siteevent_profile_rating_parameters">
                            <?php $iteration = 1; ?>
                            <?php foreach ($this->ratingDataTopbox as $reviewcatTopbox): ?>
                                <?php if (!empty($reviewcatTopbox['ratingparam_name'])): ?>	         
                                    <div class="o_hidden">
                                        <div class="parameter_title">
                                            <?php echo $this->translate($reviewcatTopbox['ratingparam_name']) ?>
                                        </div>
                                        <div class="parameter_value">
                                            <?php echo $this->ShowRatingStarSiteevent($reviewcatTopbox['avg_rating'], 'user', 'small-box', $reviewcatTopbox['ratingparam_name']); ?>     
                                        </div>
                                        <div class="parameter_count"><?php echo $this->siteevent->getNumbersOfUserRating('user', $reviewcatTopbox['ratingparam_id']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php $iteration++; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="clr"></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="siteevent_profile_review_stat clr">
                <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
            </div>
            
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1)):?>
							<div class="siteevent_profile_review_stat clr">
									<?php echo $this->translate("Recommended by %s users", '<b>' . $this->recommend_percentage . '%</b>'); ?>
							</div>
						<?php endif;?>

            <?php if (!empty($this->viewer_id) && $this->can_create && empty($this->isajax)): ?>
                <?php $rating_value_2 = 0; ?>	
                <?php if (!empty($this->reviewRateMyData)): ?>	
                    <?php foreach ($this->reviewRateMyData as $reviewRateData): ?>
                        <?php if ($reviewRateData['ratingparam_id'] == 0): ?>
                            <?php $rating_value_2 = $reviewRateData['rating']; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="siteevent_profile_review_title mtop5" id="review-my-rating">
                    <?php echo $this->translate("My Rating"); ?>
                </div>	
                <div class="siteevent_profile_review_stars">
                    <?php echo $this->ShowRatingStarSiteevent($rating_value_2, 'user', 'big-star'); ?>		     
                </div>
                <?php if (!empty($this->reviewRateMyData) && !empty($this->hasPosted) && !empty($this->can_update)): ?>
                    <div class="siteevent_profile_review_stat mtop10">
                        <?php echo $this->translate('Please %1$sclick here%2$s to update your reviews for this event.', "<a href='javascript:void(0);' onclick='showForm();'>", "</a>"); ?>
                    </div>	
                <?php endif; ?>
                <?php if (empty($this->reviewRateMyData) && empty($this->hasPosted) && !empty($this->create_level_allow)): ?>
                    <div class="siteevent_profile_review_stat">
                        <?php echo $this->translate('Please %1$sclick here%2$s to give your review and ratings for this event.', "<a href='javascript:void(0);' onclick='showForm();'>", "</a>"); ?>
                    </div>	
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!--Rating Breakdown Hover Box Starts-->
        <div class="siteevent_profile_review_right">
            <div class="siteevent_rating_breakdowns">
                <div class="siteevent_profile_review_title">
                    <?php echo $this->translate("Ratings Breakdown"); ?>
                </div>
                <ul>
                    <?php for ($i = 5; $i > 0; $i--): ?>
                        <li>
                            <div class="left"><?php echo $this->translate(array("%s star:", "%s stars:", $i), $i); ?></div>
                            <?php
                            $count = $this->siteevent->getNumbersOfUserRating('user', 0, $i);
                            $pr = $count ? ($count * 100 / $this->totalReviews) : 0;
                            ?>
                            <div class="count"><?php echo $count; ?></div>
                            <div class="rate_bar b_medium">
                                <span style="width:<?php echo $pr; ?>%;" <?php echo empty($count) ? "class='siteevent_border_none'" : "" ?>></span>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
            <div class="clr"></div>
        </div>
        <!--Rating Breakdown Hover Box Ends-->
    </div>

    <ul class="siteevent_reviews_event" id="profile_siteevent_content">
        <li>
            <div class="siteevent_reviews_event_photo">
                <?php if ($review->owner_id): ?>
                    <?php echo $this->htmlLink($review->getOwner()->getHref(), $this->itemPhoto($review->getOwner(), 'thumb.icon', $review->getOwner()->getTitle()), array('class' => "thumb_icon")) ?>
                <?php else: ?>
                    <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/nophoto_user_thumb_icon.png"; ?>
                    <img src="<?php echo $itemphoto; ?>" class="thumb_icon" alt="" />
                <?php endif; ?>
            </div>
            <div class="siteevent_reviews_event_info">
                <div class=" siteevent_reviews_event_title">
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
                            <?php echo $this->ShowRatingStarSiteevent($rating_value, 'user', 'big-star'); ?>
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
                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                </div>
                                                <?php else: ?>
                                                <div class="parameter_title">
                                                    <?php echo $this->translate("Overall Rating"); ?>
                                                </div>	
                                                <div class="parameter_value">
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
                    <div class="siteevent_review_title"><?php echo $review->getTitle() ?></div>
                </div>

                <div class="siteevent_reviews_event_stat seaocore_txt_light">
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1) && $review->recommend): ?>
                        <span class="fright siteevent_profile_userreview_recommended">
                            <?php echo $this->translate('Recommended'); ?>
                            <span class='siteevent_icon_tick siteevent_icon'></span>
                        </span>
                    <?php endif; ?>
                    <?php echo $this->timestamp(strtotime($review->modified_date)); ?> - 
                    <?php if (!empty($review->owner_id)): ?>
                        <?php echo $this->translate('by'); ?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
                    <?php endif; ?>
                </div> 
                <div class="clr"></div>
<?php if ($review->pros): ?>
                    <div class="siteevent_reviews_event_proscons">
                        <b><?php echo $this->translate("Pros") ?>: </b>
                    <?php echo $review->pros ?> 
                    </div>
                <?php endif; ?>
<?php if ($review->cons): ?>
                    <div class="siteevent_reviews_event_proscons"> 
                        <b><?php echo $this->translate("Cons") ?>: </b>
                    <?php echo $review->cons ?>
                    </div>
                <?php endif; ?>

                    <?php if ($this->reviews->profile_type_review): ?>
                    <div class="siteevent_reviews_event_proscons"> 
                        <?php $custom_field_values = $this->FieldValueLoopReviewSiteevent($this->reviews, $this->fieldStructure); ?>
                    <?php echo htmlspecialchars_decode($custom_field_values); ?>
                    </div>	
                <?php endif; ?>

                <?php if ($review->getDescription()): ?>
                    <div class="siteevent_reviews_event_proscons">
                        <b><?php echo $this->translate("Summary") ?>: </b>
                    <?php echo $review->body ?>
                    </div>
                <?php endif; ?>

                <div class="feed_item_link_desc">
                    <?php $this->reviewDescriptions = $reviewDescriptionsTable->getReviewDescriptions($this->reviews->review_id); ?>
                        <?php if (count($this->reviewDescriptions) > 0): ?>
                        <div class="siteevent_profile_info_des_update siteevent_review_block">        
                            <?php foreach ($this->reviewDescriptions as $value) : ?>
                                <?php if ($value->body): ?>
                                    <div class="b_medium">
                                        <div class="siteevent_profile_info_des_update_date">
                                            <?php echo $this->translate("Updated On %s", $this->timestamp(strtotime($value->modified_date))); ?>
                                        </div>
                                        <div>
                                            <?php echo $value->body; ?>
                                        </div>
                                    </div>
                                <?php endif; ?> 
                            <?php endforeach; ?>
                        </div> 
                    <?php endif; ?> 
                </div>
                <?php
                include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_formReplyReview.tpl';
                ?> 
            </div>
        </li>
    </ul>
<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listNestedComment.tpl';
    ?>

    <div class="clr o_hidden b_medium siteevent_review_view_footer fleft">  
        <div class="fleft">
            <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'slug' => $this->siteevent->getSlug(), 'tab' => $this->tab_id), "siteevent_entry_view", true) ?>' class="buttonlink siteevent_item_icon_back">
<?php echo $this->translate('Back to Reviews'); ?>
            </a>
        </div>      
        <div class="o_hidden fright siteevent_review_view_paging">
            <?php $pre = $this->reviews->getPreviousReview(); ?>
                <?php if ($pre): ?>
                <div id="user_group_members_previous" class="paginator_previous">
                    <?php
                    echo $this->htmlLink($pre->getHref(), $this->translate('Previous'), array(
                        'class' => 'buttonlink icon_previous'
                    ));
                    ?>
                </div>
            <?php endif; ?>
            <?php $next = $this->reviews->getNextReview(); ?>
                <?php if ($next): ?>
                <div id="user_group_members_previous" class="paginator_next">
                    <?php
                    echo $this->htmlLink($next->getHref(), $this->translate('Next'), array(
                        'class' => 'buttonlink_right icon_next'
                    ));
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="clr fleft widthfull">
        <?php if ($this->hasPosted && $this->can_update): ?>
            <?php echo $this->update_form->setAttrib('class', 'siteevent_review_form global_form')->render($this) ?>
            <?php
            include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_formUpdateReview.tpl';
            ?>
        <?php endif; ?>
        <?php if (!$this->hasPosted && $this->can_create): ?>
            <?php echo $this->form->setAttrib('class', 'siteevent_review_form global_form')->render($this) ?>
        <?php endif; ?>
        <?php
        include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_formCreateReview.tpl';
        ?>
    </div>
</div>

<script type="text/javascript">
    var seaocore_content_type = '<?php echo $this->reviews->getType(); ?>';
    en4.core.runonce.add(function() {
<?php if (count($this->ratingDataTopbox) > 1): ?>
            $$('.siteevent_profile_review_rating').addEvents({
                'mouseover': function(event) {
                    document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'block');
                },
                'mouseleave': function(event) {
                    document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'none');
                }});
            $$('.siteevent_ur_bdown_box_wrapper').addEvents({
                'mouseenter': function(event) {
                    document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'block');
                },
                'mouseleave': function(event) {
                    document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'none');
                }});
<?php endif; ?>
    });
</script>