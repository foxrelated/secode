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
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js');
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_board.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php if ($this->autoload): ?>
    <div id="pinboard_<?php echo $this->identity ?>">
        <?php if (isset($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
            <div class="siteevent_profile_loading_image"></div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">
        var layoutColumn = 'middle';
        if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')) {
            layoutColumn = 'left';
        } else if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')) {
            layoutColumn = 'right';
        }
        PinBoardSeaoObject[layoutColumn].add({
            contentId: 'pinboard_<?php echo $this->identity ?>',
            widgetId: '<?php echo $this->identity ?>',
            totalCount: '<?php echo $this->totalCount ?>',
            requestParams:<?php echo json_encode($this->params) ?>,
            detactLocation: <?php echo $this->detactLocation; ?>,
            responseContainerClass: 'layout_siteevent_pinboard_events_siteevent'
        });

    </script>
<?php else: ?>
    <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
        <div id="pinboard_<?php echo $this->identity ?>"></div>
        <script type="text/javascript">
            en4.core.runonce.add(function() {
                var pinBoardViewMore = new PinBoardSeaoViewMore({
                    contentId: 'pinboard_<?php echo $this->identity ?>',
                    widgetId: '<?php echo $this->identity ?>',
                    totalCount: '<?php echo $this->totalCount ?>',
                    viewMoreId: 'seaocore_view_more_<?php echo $this->identity ?>',
                    loadingId: 'seaocore_loading_<?php echo $this->identity ?>',
                    requestParams:<?php echo json_encode($this->params) ?>,
                    responseContainerClass: 'layout_siteevent_pinboard_events_siteevent'
                });
                PinBoardSeaoViewMoreObjects.push(pinBoardViewMore);
            });
        </script>
    <?php endif; ?>

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

    <?php $countButton = count($this->show_buttons); ?>
    <?php foreach ($this->events as $siteevent): ?>

        <?php
        $noOfButtons = $countButton;
        if ($this->show_buttons):

            $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $siteevent->authorization()->isAllowed($this->viewer(), "comment");
            if (in_array('comment', $this->show_buttons) && !$alllowComment) {
                $noOfButtons--;
            }
            if (in_array('like', $this->show_buttons) && !$alllowComment) {
                $noOfButtons--;
            }

            if (in_array('membership', $this->show_buttons)) {
                $membershipButton = $this->eventMembershipButton($siteevent, array('class' => 'seaocore_board_icon'));
                if (!$membershipButton)
                    $noOfButtons--;
            }
        endif;
        ?>
        <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
            <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;">
                <div>
                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                        <i class="seaocore_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                    <?php endif; ?>
                    <div class="seaocore_board_list_thumb">
                        <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>" class="seaocore_thumb">
                            <table>
                                <tr valign="middle">
                                    <td>
                                        <?php
                                        $options = array('align' => 'center');

                                        if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                                            $options['style'] = 'width:auto; max-width:' . ($this->params['itemWidth'] - 18) . 'px;';
                                        endif;
                                        ?>  
                                        <?php echo $this->itemPhoto($siteevent, ($this->params['itemWidth'] > 300) ? 'thumb.main' : 'thumb.profile', '', $options); ?>
                                    </td> 
                                </tr> 
                            </table>
                        </a>
                    </div>
                    
                    <?php if ((!empty($this->statistics) && (in_array('hostName', $this->statistics) || in_array('categoryLink', $this->statistics)))): ?>  
                    <div class="seaocore_board_list_btm">
                            <?php if (!empty($this->statistics) && in_array('hostName', $this->statistics)): ?>
                                <?php $hostDisplayName = $siteevent->getHostName(true); ?>
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
                                <?php if (!empty($this->statistics) && in_array('categoryLink', $this->statistics)) : ?>
                                    <?php if (!in_array('hostName', $this->statistics) || empty($hostDisplayName)): ?>
                                        <div class="o_hidden seaocore_stats seaocore_txt_light">
                                        <?php endif; ?>
                                        <?php if (in_array('hostName', $this->statistics)) : ?>
                                            <?php echo $this->translate("in %s", $this->htmlLink($siteevent->getCategory()->getHref(), $this->translate($siteevent->getCategory()->getTitle(true)))) ?>
                                        <?php else: ?>
                                            <?php echo $this->htmlLink($siteevent->getCategory()->getHref(), $this->translate($siteevent->getCategory()->getTitle(true))); ?>
                                        <?php endif; ?>
                                        <?php //echo $this->timestamp(strtotime($siteevent->creation_date)) ?>
                                    <?php endif; ?>
                                    <?php if ((in_array('hostName', $this->statistics) && !empty($hostDisplayName)) || in_array('categoryLink', $this->statistics)): ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                         <?php endif; ?>
                         
                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                            <?php echo $this->translate('SPONSORED'); ?>                 
                        </div>
                    <?php endif; ?>
                    
                    <div class="seaocore_board_list_cont">
                        <div class="seaocore_title">
                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), $siteevent->getTitle()) ?>
                        </div>

                        <?php if ($this->truncationDescription): ?>
                            <div class="seaocore_description">
                                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getDescription(), $this->truncationDescription) ?>
                            </div>  
                        <?php endif; ?>

                        <!-- EVENT INFO WORK -->
                        <?php if (!empty($this->statistics)) : ?>
                            <?php if (in_array('venueName', $this->statistics) && !$siteevent->is_online && !empty($siteevent->venue_name)) : ?>
                                <div class="siteevent_listings_stats">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>
                                    <div class="o_hidden">
                                        <?php echo $siteevent->venue_name; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($locationEnabled && !empty($siteevent->location) && in_array('location', $this->statistics)): ?>
                                <div class="siteevent_listings_stats">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                                    <div class="o_hidden">
                                        <?php $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->location, $this->truncationLocation); ?>
                                        <?php if (!in_array('directionLink', $this->statistics)): ?>  
                                            <?php echo "<span title='$siteevent->location'>$location</span>"; ?>
                                        <?php else: ?>
                                            <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $siteevent->event_id, 'resouce_type' => 'siteevent_event'), $location, array('class' => 'smoothbox', 'title' => $siteevent->location)); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('startDate', $this->statistics) || in_array('endDate', $this->statistics)) : ?>
                                <?php $dateTimeInfo = array(); ?>
                                <?php $dateTimeInfo['occurrence_id'] = $siteevent->occurrence_id; ?>
                                <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->statistics); ?>
                                <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->statistics); ?>
                                <?php $dateTimeInfo['showEventType'] = $this->showEventType; ?>
                                <?php $this->eventDateTime($siteevent, $dateTimeInfo); ?>
                            <?php endif; ?>

                            <?php if (in_array('price', $this->statistics) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) : ?>
                              <?php if(!empty($siteevent->price) && $siteevent->price > 0):?>
                                <div class="siteevent_listings_stats">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
                                    <div class="o_hidden bold">
                                        <?php echo $this->locale()->toCurrency($siteevent->price, $currency); ?>
                                    </div>
                                </div>
																<?php else:?>
																	<div class="siteevent_listings_stats siteevent_listings_price_free">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
                                    <div class="o_hidden bold">
                                        <?php echo $this->translate("FREE"); ?>
                                    </div>
                                </div>
															<?php endif; ?>
                            <?php endif; ?>

                            <?php if (in_array('ledBy', $this->statistics)) : ?>
                                <?php $ledBys = $siteevent->getEventLedBys(in_array('hostName', $this->statistics)); ?>
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
                                $statistics .= $this->translate(array('%s guest', '%s guests', $siteevent->member_count), $this->locale()->toNumber($siteevent->member_count)) . ', ';
                            }

                            if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s comment', '%s comments', $siteevent->comment_count), $this->locale()->toNumber($siteevent->comment_count)) . ', ';
                            }

                            if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s view', '%s views', $siteevent->view_count), $this->locale()->toNumber($siteevent->view_count)) . ', ';
                            }

                            if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                                $statistics .= $this->translate(array('%s like', '%s likes', $siteevent->like_count), $this->locale()->toNumber($siteevent->like_count)) . ', ';
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

                            <?php if ((in_array('ratingStar', $this->statistics) && !empty($siteevent->review_count)) || (in_array('reviewCount', $this->statistics) && (!empty($siteevent->rating_editor) || !empty($siteevent->rating_users) || !empty($siteevent->$ratingValue) ))) : ?>
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
                                                    <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_editor, 'editor', $ratingShow); ?>
                                                    <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_users, 'user', $ratingShow); ?>
                                                <?php else: ?>
                                                    <?php echo $this->ShowRatingStarSiteevent($siteevent->$ratingValue, $ratingType, $ratingShow); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (in_array('reviewCount', $this->statistics)) : ?>
                                            <div class="fleft f_small"> 
                                                <?php echo $this->translate(array('%s review', '%s reviews', $siteevent->review_count), $this->locale()->toNumber($siteevent->review_count)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!-- END EVENT INFO WORK -->
                    </div>

                    <?php if ((!empty($this->statistics) && (in_array('hostName', $this->statistics) || in_array('categoryLink', $this->statistics))) || (!empty($this->userComment)) || (!empty($this->show_buttons))): ?>    
                        
                            <?php if (!empty($this->userComment)) : ?>
                                <div class="seaocore_board_list_comments o_hidden">
                                    <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $siteevent->getType(), "id" => $siteevent->event_id, 'widget_id' => $this->identity)); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($this->show_buttons)): ?>
                                <div class="seaocore_board_list_action_links">
                                    <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $siteevent->getHref(array('showEventType' => $this->showEventType))); ?>
                                    <?php if (in_array('membership', $this->show_buttons)): ?>
                                        <?php echo $membershipButton; ?>
                                    <?php endif; ?>
                                    <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment && !empty($this->userComment)): ?>
                                        <?php if (in_array('comment', $this->show_buttons)): ?>
                                            <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $siteevent->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                                        <?php endif; ?>
                                        <?php if (in_array('like', $this->show_buttons)): ?>
                                            <a href="javascript:void(0)" title="Like" class="seaocore_board_icon like_icon <?php echo $siteevent->getGuid() ?>like_link" id="<?php echo $siteevent->getType() ?>_<?php echo $siteevent->getIdentity() ?>like_link" <?php if ($siteevent->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocorepinboard.likes.like('<?php echo $siteevent->getType() ?>', '<?php echo $siteevent->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                                            <a  href="javascript:void(0)" title="Unlike" class="seaocore_board_icon unlike_icon <?php echo $siteevent->getGuid() ?>unlike_link" id="<?php echo $siteevent->getType() ?>_<?php echo $siteevent->getIdentity() ?>unlike_link" <?php if (!$siteevent->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.unlike('<?php echo $siteevent->getType() ?>', '<?php echo $siteevent->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a> 
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (in_array('share', $this->show_buttons)): ?>
                                        <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $siteevent->getType(), 'id' => $siteevent->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon share_icon' , 'title' => 'Share')); ?>
                                    <?php endif; ?>

                                    <?php if (in_array('facebook', $this->show_buttons)): ?>
                                        <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $siteevent->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon fb_icon' , 'title' => 'Facebook')) ?>
                                    <?php endif; ?>

                                    <?php if (in_array('twitter', $this->show_buttons)): ?>
                                        <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $siteevent->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon' , 'title' => 'Twitter')) ?> 
                                    <?php endif; ?>

                                    <?php if (in_array('pinit', $this->show_buttons)): ?>
                                        <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $siteevent->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $siteevent->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $siteevent->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon" title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                                    <?php endif; ?>

                                    <?php if (in_array('tellAFriend', $this->show_buttons)): ?>
                                        <?php echo $this->htmlLink(array('action' => 'tellafriend', 'route' => 'siteevent_specific', 'type' => $siteevent->getType(), 'event_id' => $siteevent->getIdentity()), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon taf_icon' , 'title' => 'Tell a Friend')); ?>
                                    <?php endif; ?>

                                    <?php if (in_array('print', $this->show_buttons)): ?>
                                        <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'siteevent_specific', 'type' => $siteevent->getType(), 'event_id' => $siteevent->getIdentity()), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon print_icon' , 'title' => 'Print')); ?> 
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>    
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$this->autoload && !$this->is_ajax_load): ?>
            <div class="seaocore_view_more mtop10 dnone" id="seaocore_view_more_<?php echo $this->identity ?>">
                <a href="javascript:void(0);" id="" class="buttonlink icon_viewmore"><?php echo$this->translate('View More') ?></a>
            </div>
            <div class="seaocore_loading dnone" id="seaocore_loading_<?php echo $this->identity ?>" >
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" style="margin-right: 5px;">
                <?php echo $this->translate('Loading...') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

