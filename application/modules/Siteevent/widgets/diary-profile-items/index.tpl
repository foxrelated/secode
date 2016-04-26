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
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_board.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/mooMasonry.js');
?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<script type="text/javascript">
    var seaocore_content_type = 'siteevent';
    var seaocore_like_url = en4.core.baseUrl + 'siteevent/index/globallikes';
</script>

<script type="text/javascript">
    var currentPage =<?php echo $this->paginator->getCurrentPageNumber(); ?>;
    <?php if (!$this->isAjax): ?>
        var requestActive = false;
        en4.core.runonce.add(function() {
            en4.srpinboard.masonryWidgetAllow[<?php echo $this->identity ?>] = false;
            en4.srpinboard.masonryArray.push({
                columnWidth: <?php echo $this->itemWidth; ?>,
                singleMode: true,
                itemSelector: '.siteevent_list_wrapper',
                responseContainer: $('items_content'),
                allowId:<?php echo $this->identity ?>
            });
            <?php if ($this->total_item > 0): ?>
                en4.srpinboard.masonryWidgetAllow[<?php echo $this->identity ?>] = true;
                en4.srpinboard.setMasonryLayout();
            <?php endif; ?>
            window.addEvent('scroll', function() {
                if (requestActive)
                    return;
                if (<?php echo $this->paginator->count() ?> > currentPage && currentPage != 0) {
                    var elementPostionY = 0;
                    if (typeof($('srw_loading').offsetParent) != 'undefined') {
                        elementPostionY = $('srw_loading').offsetTop;
                    } else {
                        elementPostionY = $('srw_loading').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 10)) {
                        ajaxContent(currentPage + 1);
                    }
                }
            });
            var ajaxContent = function(page) {
                if (requestActive)
                    return;
                if (page == 1) {
                    $('loading_image').style.display = 'block';
                    $('items_content').empty();
                    $('items_content').style.display = 'none';
                } else {
                    $('srw_loading').removeClass('dnone');
                }
                //  var params = $('diary_items_filter_form').toQueryString();
                requestActive = true;
                en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>',
                    data: $merge(<?php echo json_encode($this->params) ?>, {
                        format: 'html',
                        method: 'get',
                        subject: en4.core.subject.guid,
                        currentpage: page,
                        isAjax: true,
                        itemCount: '<?php echo $this->itemCount; ?>',
                        postedby: '<?php echo $this->postedby; ?>',
                        ratingType: '<?php echo $this->ratingType; ?>'
                    }),
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        if (page == 1) {
                            $('loading_image').style.display = 'none';
                            $('items_content').style.display = 'block';
                        } else {
                            $('srw_loading').addClass('dnone');
                        }
                        requestActive = false;
                        Elements.from(responseHTML).inject($('items_content'));
                        en4.core.runonce.trigger();
                        Smoothbox.bind($('items_content'));
                        en4.srpinboard.setMasonryLayout();
                    }
                }), {
                    'force': true
                })
            }
        });
    <?php endif; ?>
</script>

<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
    $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
    $ratingType = 'overall';
} else {
    $ratingType = 'user';
}
?>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php if (!$this->isAjax): ?>
    <div class="siteevent_diary_view">
        <div class="siteevent_diary_view_title"> 
            <?php echo $this->diary->title; ?> 
        </div>
        <div class="siteevent_diary_view_des mbot10">
            <?php echo $this->diary->body; ?>
        </div>
        <div class="siteevent_diary_view_about b_medium clr o_hidden">
            <div class="siteevent_diary_view_about_left fleft">
                <?php if ($this->postedby): ?>
                    <div class="thumb fleft mright5">
                        <?php echo $this->htmlLink($this->diary->getOwner()->getHref(), $this->itemPhoto($this->diary->getOwner(), 'thumb.icon', '')); ?>
                    </div>
                <?php endif; ?>
                <div class="o_hidden">
                    <?php if ($this->postedby): ?>
                        <div class="bold mbot5">
                            <?php echo $this->diary->getOwner()->toString(); ?>
                        </div>
                    <?php endif; ?>
                    <div class="siteevent_diary_view_stats seaocore_txt_light">
                        <?php echo $this->timestamp($this->diary->creation_date); ?>
                    </div>
                    <?php if (!empty($this->statisticsDiary)): ?>
                        <div class="siteevent_diary_view_stats seaocore_txt_light">
                            <?php
                            $statistics = '';

                            if (in_array('entryCount', $this->statisticsDiary)) {
                                $statistics .= $this->translate(array('<b>%s</b> Event', '<b>%s</b> Events', $this->total_item), $this->locale()->toNumber($this->total_item)) . '&nbsp&nbsp&nbsp';
                            }

                            if (in_array('viewCount', $this->statisticsDiary)) {
                                $statistics .= $this->translate(array('<b>%s</b> View', '<b>%s</b> Views', $this->diary->view_count), $this->locale()->toNumber($this->diary->view_count)) . '&nbsp&nbsp&nbsp';
                            }
                            ?>
                            <?php echo $statistics; ?>
                        </div>  
                    <?php endif; ?>
                </div>
            </div>

            <?php $widgetContent = $this->content()->renderWidget("siteevent.share", array('subject' => $this->diary->getGuid(), 'withoutContainer' => true, 'options' => $this->shareOptions, 'content_id' => $this->identity)) ?>
            <?php if (strlen($widgetContent) > 15): ?>
                <div class="siteevent_diary_view_about_right fright">
                    <?php echo $widgetContent ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($this->viewer_id): ?>
            <div class="siteevent_diary_item_options clr O_hidden mtop10 pleft10">
                <?php if ($this->can_create): ?>
                    <?php echo $this->htmlLink(array('route' => 'siteevent_diary_general', 'action' => 'create'), $this->translate('Create New Event Diary'), array('class' => 'smoothbox siteevent_icon_diary_add')) ?>
                <?php endif; ?>
                <?php if (!empty($this->messageOwner)): ?>
                    <?php echo $this->htmlLink(array('route' => 'siteevent_diary_general', 'action' => 'message-owner', 'diary_id' => $this->diary->getIdentity()), $this->translate('Message Owner'), array('class' => 'smoothbox icon_siteevents_messageowner')) ?>
                <?php endif; ?>
                <?php if ($this->diary->owner_id == $this->viewer_id || $this->level_id == 1): ?>
                    <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'edit', 'diary_id' => $this->diary->getIdentity()), $this->translate('Edit Diary'), array('title' => $this->translate('Edit Diary'), 'class' => 'smoothbox seaocore_icon_edit', 'style' => 'margin-left:0px;')) ?> 
                    <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'delete', 'diary_id' => $this->diary->getIdentity()), $this->translate('Delete Diary'), array('title' => $this->translate('Delete Diary'), 'class' => 'smoothbox seaocore_icon_delete')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'member' => $this->diary->getOwner()->getTitle()), $this->translate("%s's Event Diaries", $this->diary->getOwner()->getTitle()), array('title' => $this->translate("%s's Event Diaries", $this->diary->getOwner()->getTitle()), 'class' => 'siteevent_icon_diary')) ?>
            </div>
        <?php endif; ?>

        <div id="siteevent_diary_items" class="clr">
            <div id="loading_image" class="seaocore_content_loader" style="display: none;"></div>      

            <ul class="seaocore_browse_pin" id="items_content">
            <?php endif; ?>
            <?php if ($this->total_item > 0): ?>
                <?php foreach ($this->paginator as $event): ?>

                    <?php $countButton = count($this->show_buttons); ?>

                    <?php
                    $noOfButtons = $countButton;
                    if ($this->show_buttons):

                        $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $event->authorization()->isAllowed($this->viewer(), "comment");
                        if (in_array('comment', $this->show_buttons) && !$alllowComment) {
                            $noOfButtons--;
                        }
                        if (in_array('like', $this->show_buttons) && !$alllowComment) {
                            $noOfButtons--;
                        }
                        if (in_array('diary', $this->show_buttons) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1)):
                            $noOfButtons--;
                        endif;
                    endif;
                    if ($this->diary->owner_id == $this->viewer_id):
                        $noOfButtons++;
                        if ($this->diary->event_id != $event->event_id):
                            $noOfButtons++;
                        endif;
                    endif;
                    ?>
                    <div class="siteevent_list_wrapper" style="width:<?php echo $this->itemWidth ?>px;">
                        <div class="siteevent_board_list b_medium" style="width:<?php echo $this->itemWidth - 18 ?>px;">
                            <div>
                                <?php if ($event->featured): ?>
                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                <?php endif; ?>
                                <?php if ($event->newlabel): ?>
                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                <?php endif; ?>
                                <div class="siteevent_board_list_thumb">
                                    <a href="<?php echo $event->getHref(array('showEventType' => 'all')) ?>" class="siteevent_thumb">
                                        <table style="height: <?php echo 30 * $noOfButtons ?>px;">
                                            <tr valign="middle">
                                                <td>

                                                    <?php
                                                    $options = array('align' => 'center');

                                                    if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                                                        $options['style'] = 'width:auto; max-width:' . ($this->itemWidth - 18) . 'px;';
                                                    endif;
                                                    ?>  
                                                    <?php echo $this->itemPhoto($event, ($this->itemWidth > 300) ? 'thumb.main' : 'thumb.profile', '', $options); ?>
                                                    <?php if (!empty($event->sponsored)): ?>
                                                        <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                            <?php echo $this->translate('SPONSORED'); ?>                 
                                                        </div>
                                                    <?php endif; ?>
                                                </td> 
                                            </tr> 
                                        </table>
                                    </a>
                                </div>
                                <div class="siteevent_board_list_cont">
                                    <div class="siteevent_title">
                                        <?php echo $this->htmlLink($event->getHref(array('showEventType' => 'all')), $event->getTitle()) ?>
                                    </div>

                                    <?php if ($this->truncationDescription): ?>
                                        <div class="siteevent_description">
                                            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($event->getDescription(), $this->truncationDescription) ?>
                                        </div>  
                                    <?php endif; ?>             

                                    <?php if (!empty($this->statistics)): ?>
                                        <div class="siteevent_stats seaocore_txt_light">
                                            <?php if (in_array('hostName', $this->statistics)): ?>
                                                <?php $hostDisplayName = $event->getHostName(); ?>
                                                <?php if (!empty($hostDisplayName)): ?>
                                                    <div class="siteevent_listings_stats">
                                                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="<?php echo $this->translate("Host") ?>"></i>
                                                        <div class="o_hidden">
                                                            <?php echo $hostDisplayName; ?><br />
                                                        </div>
                                                    </div>    
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if (in_array('venueName', $this->statistics) && !$event->is_online && !empty($event->venue_name)) : ?>
                                                <div class="siteevent_listings_stats">
                                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>
                                                    <div class="o_hidden">
                                                        <?php echo $event->venue_name; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($locationEnabled && !empty($event->location) && in_array('location', $this->statistics)): ?>
                                                <div class="siteevent_listings_stats">
                                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                                                    <div class="o_hidden">
                                                        <?php $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($event->location, $this->truncationLocation); ?>
                                                        <?php if (!in_array('directionLink', $this->statistics)): ?>  
                                                            <?php echo "<span title='$event->location'>$location</span>"; ?>
                                                        <?php else: ?>
                                                            <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $event->event_id, 'resouce_type' => 'siteevent_event'), $location, array('class' => 'smoothbox', 'title' => $event->location)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (in_array('startDate', $this->statistics) || in_array('endDate', $this->statistics)) : ?>
                                                <?php $dateTimeInfo = array(); ?>
                                                <?php $dateTimeInfo['occurrence_id'] = $event->occurrence_id; ?>
                                                <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->statistics); ?>
                                                <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->statistics); ?>
                                                <?php $dateTimeInfo['showEventType'] = 'all'; ?>
                                                <?php $this->eventDateTime($event, $dateTimeInfo); ?> 
                                            <?php endif; ?>

                                            <?php if (in_array('price', $this->statistics) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) : ?>
																							<?php if(!empty($event->price) && $event->price > 0) :?>
																									<div class="siteevent_listings_stats">
																											<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
																											<div class="o_hidden bold">
																													<?php echo $this->locale()->toCurrency($event->price, $currency); ?>
																											</div>
																									</div>		
																								<?php else :?>
																									<div class="siteevent_listings_stats siteevent_listings_price_free">
																											<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
																											<div class="o_hidden bold">
																													<?php echo $this->translate("FREE"); ?>
																											</div>
																									</div>		
																								<?php endif;?>
                                            <?php endif; ?>




                                            <?php if (in_array('ledBy', $this->statistics)) : ?>
                                                <?php $ledBys = $event->getEventLedBys(in_array('hostName', $this->statistics)); ?>
                                                <?php if (!empty($ledBys)) : ?>
                                                    <div class="siteevent_listings_stats">
                                                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_user" title="<?php echo $this->translate("Leader") ?>"></i>
                                                        <div class="o_hidden">
                                                            <?php echo $ledBys; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php
                                            $statistics = '';

                                            if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('memberCount', $this->statistics)) {
                                                $statistics .= $this->translate(array('%s guest', '%s guests', $event->member_count), $this->locale()->toNumber($event->member_count)) . ', ';
                                            }

                                            if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                                                $statistics .= $this->translate(array('%s comment', '%s comments', $event->comment_count), $this->locale()->toNumber($event->comment_count)) . ', ';
                                            }

                                            if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                                                $statistics .= $this->translate(array('%s view', '%s views', $event->view_count), $this->locale()->toNumber($event->view_count)) . ', ';
                                            }

                                            if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                                                $statistics .= $this->translate(array('%s like', '%s likes', $event->like_count), $this->locale()->toNumber($event->like_count)) . ', ';
                                            }

                                            $statistics = trim($statistics);
                                            $statistics = rtrim($statistics, ',');
                                            ?>
                                            <?php if (!empty($statistics)) : ?>
                                                <div class="siteevent_listings_stats">
                                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_stats" title="<?php echo $this->translate("Statistics") ?>"></i>
                                                    <div class="o_hidden">
                                                        <?php echo $statistics; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ((in_array('ratingStar', $this->statistics) && !empty($event->review_count)) || (in_array('reviewCount', $this->statistics) && (!empty($event->rating_editor) || !empty($event->rating_users) || !empty($event->$ratingValue) ))) : ?>
                                                <?php
                                                if (in_array('ratingStar', $this->statistics) && in_array('reviewCount', $this->statistics))
                                                    $iconHtmlTitle = $this->translate("Reviews & Ratings");
                                                else if (in_array('ratingStar', $this->statistics) && !in_array('reviewCount', $this->statistics))
                                                    $iconHtmlTitle = $this->translate("Ratings");
                                                else if (!in_array('ratingStar', $this->statistics) && in_array('reviewCount', $this->statistics))
                                                    $iconHtmlTitle = $this->translate("Reviews");
                                                ?>
                                                <div class="siteevent_listings_stats">
                                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_rating" title="<?php echo $iconHtmlTitle ?>"></i>
                                                    <div class="o_hidden stats_rating_star">
                                                        <div class="fleft">
                                                            <?php if (in_array('ratingStar', $this->statistics)) : ?>
                                                                <?php if ($ratingValue == 'rating_both'): ?>
                                                                    <?php echo $this->ShowRatingStarSiteevent($event->rating_editor, 'editor', $ratingShow); ?>
                                                                    <?php echo $this->ShowRatingStarSiteevent($event->rating_users, 'user', $ratingShow); ?>
                                                                <?php else: ?>
                                                                    <?php echo $this->ShowRatingStarSiteevent($event->$ratingValue, $ratingType, $ratingShow); ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (in_array('reviewCount', $this->statistics)) : ?>
                                                            <div class="fleft f_small"> 
                                                                <?php echo $this->translate(array('%s review', '%s reviews', $event->review_count), $this->locale()->toNumber($event->review_count)); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="siteevent_board_list_btm o_hidden clr">
                                    <?php if (!empty($this->statistics) && in_array('hostName', $this->statistics)): ?>
                                        <?php $hostDisplayName = $event->getHostName(true); ?>
                                        <?php if (!empty($hostDisplayName)): ?>
                                            <?php if (is_array($hostDisplayName)) : ?>
                                                <?php echo $hostDisplayName['displayImage']; ?>
                                            <?php endif; ?>
                                            <div class="o_hidden seaocore_stats seaocore_txt_light">
                                                <?php if (is_array($hostDisplayName)) : ?>
                                                    <b><?php echo $hostDisplayName['displayName'] ?></b><br />
                                                <?php else: ?>
                                                    <span class="f_small"><?php echo $this->translate("Hosted by: ") ?></span>
                                                    <b><?php echo $hostDisplayName ?></b><br/>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (!in_array('hostName', $this->statistics) || empty($hostDisplayName)): ?>
                                            <div class="o_hidden seaocore_stats seaocore_txt_light">
                                            <?php endif; ?>
                                            <?php if (!empty($this->statistics) && in_array('categoryLink', $this->statistics)) : ?>
                                                <?php echo $this->translate("in %s", $this->htmlLink($event->getCategory()->getHref(), $this->translate($event->getCategory()->getTitle(true)))) ?>
                                            <?php endif; ?>
                                            <?php //echo $this->timestamp(strtotime($event->creation_date)) ?>
                                            <?php if (in_array('hostName', $this->statistics) || in_array('categoryLink', $this->statistics)): ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="siteevent_board_list_comments o_hidden">
                                        <?php echo $this->action("list", "pin-board-comment", "siteevent", array("type" => $event->getType(), "id" => $event->event_id, 'widget_id' => $this->identity)); ?>
                                    </div>
                                    <?php if ($noOfButtons): ?>
                                        <div class="siteevent_board_list_action_links">
                                            <?php if ($this->diary->owner_id == $this->viewer_id): ?>
                                                <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'remove', 'event_id' => $event->event_id, 'diary_id' => $this->diary->diary_id), $this->translate('Remove'), array('class' => 'smoothbox siteevent_board_icon seaocore_icon_delete')) ?>
                                                <?php if ($this->diary->event_id != $event->event_id): ?>                   
                                                    <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'cover-photo', 'event_id' => $event->event_id, 'diary_id' => $this->diary->diary_id), $this->translate('Make Cover'), array('class' => 'smoothbox siteevent_board_icon siteevent_icon_cover')) ?>                    
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $event->getHref(array('showEventType' => 'all'))); ?>
                                            <?php if (in_array('diary', $this->show_buttons) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1)): ?> 
                                                <?php echo $this->AddToDiarySiteevent($event, array('classIcon' => 'siteevent_board_icon', 'classLink' => 'diary_icon', 'text' => $this->translate('Diary'))); ?>
                                            <?php endif; ?>

                                            <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment): ?>
                                                <?php if (in_array('comment', $this->show_buttons)): ?>
                                                    <a href='javascript:void(0);' onclick="en4.srpinboard.comments.addComment('<?php echo $event->getGuid() . "_" . $this->identity ?>')" class="siteevent_board_icon icon_siteevents_comment"><?php echo $this->translate('Comment'); ?></a> 
                                                <?php endif; ?>
                                                <?php if (in_array('like', $this->show_buttons)): ?>
                                                    <a href="javascript:void(0)" class="siteevent_board_icon like_icon <?php echo $event->getGuid() ?>like_link" id="<?php echo $event->getType() ?>_<?php echo $event->getIdentity() ?>like_link" <?php if ($event->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.srpinboard.likes.like('<?php echo $event->getType() ?>', '<?php echo $event->getIdentity() ?>');" ><?php echo $this->translate('Like'); ?></a>

                                                    <a  href="javascript:void(0)" class="siteevent_board_icon unlike_icon <?php echo $event->getGuid() ?>unlike_link" id="<?php echo $event->getType() ?>_<?php echo $event->getIdentity() ?>unlike_link" <?php if (!$event->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.srpinboard.likes.unlike('<?php echo $event->getType() ?>', '<?php echo $event->getIdentity() ?>');"><?php echo $this->translate('Unlike'); ?></a> 
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if (in_array('share', $this->show_buttons)): ?>
                                                <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $event->getType(), 'id' => $event->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox siteevent_board_icon seaocore_icon_share')); ?>
                                            <?php endif; ?>

                                            <?php if (in_array('facebook', $this->show_buttons)): ?>
                                                <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $event->getTitle(), $this->translate('Facebook'), array('class' => 'pb_ch_wd siteevent_board_icon fb_icon')) ?>
                                            <?php endif; ?>

                                            <?php if (in_array('twitter', $this->show_buttons)): ?>
                                                <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $event->getTitle(), $this->translate('Twitter'), array('class' => 'pb_ch_wd siteevent_board_icon tt_icon')) ?> 
                                            <?php endif; ?>

                                            <?php if (in_array('pinit', $this->show_buttons)): ?>
                                                <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $event->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $event->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $event->getTitle(); ?>"  class="pb_ch_wd siteevent_board_icon pin_icon"  ><?php echo $this->translate('Pin It') ?></a>
                                            <?php endif; ?>

                                            <?php if (in_array('tellAFriend', $this->show_buttons)): ?>
                                                <?php echo $this->htmlLink(array('action' => 'tellafriend', 'route' => 'siteevent_specific', 'type' => $event->getType(), 'event_id' => $event->getIdentity()), $this->translate('Tell a Friend'), array('class' => 'smoothbox siteevent_board_icon taf_icon')); ?>
                                            <?php endif; ?>

                                            <?php if (in_array('print', $this->show_buttons)): ?>
                                                <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'siteevent_specific', 'type' => $event->getType(), 'event_id' => $event->getIdentity()), $this->translate('Print'), array('class' => 'pb_ch_wd siteevent_board_icon print_icon')); ?> 
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate('There are currently no events in this diary.'); ?>
                    </span> 
                </div>
            <?php endif; ?> 
            <?php if (!$this->isAjax): ?>
            </ul>
            <div class="seaocore_loading o_hidden dnone" id="srw_loading" >
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" style="margin-right: 5px;">
                <?php echo $this->translate('Loading...') ?>
            </div>
        </div>   
    </div>
<?php endif; ?>
