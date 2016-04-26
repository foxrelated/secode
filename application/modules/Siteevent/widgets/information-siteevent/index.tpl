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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $categoryRouteName = Engine_Api::_()->siteevent()->getCategoryHomeRoute(); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<ul id="siteevent_stats" class="siteevent_side_widget siteevent_profile_event_info">
    <li class="siteevent_profile_event_info_btns txt_center">
        <?php if (!empty($this->showContent) && in_array('likeButton', $this->showContent) && $this->viewer->getIdentity()): ?>  
            <?php echo $this->content()->renderWidget("seaocore.like-button") ?>
        <?php endif; ?>

        <?php if ($this->viewer->getIdentity() && !empty($this->showContent) && in_array('joinLink', $this->showContent)): ?>
        
            <?php if($this->siteevent->isEventFull(array('occurrence_id' => $this->occurrence_id))): ?>
                
            <?php else: ?>

                <?php if(Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>

                <?php elseif(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>        
                    <div class="seaocore_button">
                        <?php $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->siteevent->event_id, 'DESC', $this->occurrence_id);?>
                        <?php $row = $this->siteevent->membership()->getRow($this->viewer); ?>
                        <?php $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $this->occurrence_id);?>
                        <?php if (null === $row && strtotime($endDate) > time() && empty($occurrence->waitlist_flag)): ?>
                            <?php if ($this->siteevent->membership()->isResourceApprovalRequired()): ?>
                                <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Request Invite"); ?></span></a>
                                <?php
                            //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN EVENT LINK.

                            else:
                                ?>
                                <?php

                                if (strtotime($endDate) > time()) :
                                    ?>
                                    <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Join Event"); ?></span></a>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php elseif ($row->active): ?>
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'leave', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Leave Event"); ?></span></a>
                        <?php elseif (!$row->resource_approved && $row->user_approved): ?>  
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Cancel Invite Request"); ?></span></a>
            <?php elseif (!$row->user_approved && $row->resource_approved ): ?>  
                            <?php if(strtotime($endDate) > time()):?>
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Accept Event Invite"); ?></span></a>
                            <?php endif;?>
                        </div><div class="seaocore_button">
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Ignore Event Invite"); ?></span></a>

                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </li>

    <!-- EVENT INFO WORK -->
    <?php if (!empty($this->showContent)) : ?>
        <?php if (in_array('hostName', $this->showContent)): ?>
            <?php $hostDisplayName = $this->siteevent->getHostName(); ?>
        <?php if (!empty($hostDisplayName)): ?>
                <li>
                    <div class="siteevent_listings_stats">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="<?php echo $this->translate("Host") ?>"></i>
                        <div class="o_hidden">
            <?php echo $hostDisplayName; ?><br />
                        </div>
                    </div> 
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('venueName', $this->showContent) && !$this->siteevent->is_online && !empty($this->siteevent->venue_name)) : ?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>
                    <div class="o_hidden">
                        <?php echo $this->siteevent->venue_name; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !empty($this->siteevent->location) && in_array('location', $this->showContent)): ?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                    <div class="o_hidden">
                        <?php echo $this->siteevent->location; ?>
                        <?php if (in_array('directionLink', $this->showContent)): ?>
                            - <b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->siteevent->event_id, 'resouce_type' => 'siteevent_event'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if (in_array('startDate', $this->showContent) || in_array('endDate', $this->showContent)) : ?>
            <?php $dateTimeInfo = array(); ?>
            <?php $dateTimeInfo['occurrence_id'] = $this->occurrence_id; ?>
            <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->showContent); ?>
            <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->showContent); ?>
            <?php $this->eventDateTime($this->siteevent, $dateTimeInfo); ?> 
        <?php endif; ?>

        <?php if (in_array('price', $this->showContent) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) : ?>
						<?php if(!empty($this->siteevent->price) && $this->siteevent->price > 0):?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
                    <div class="o_hidden bold f_small">
                        <?php echo $this->locale()->toCurrency($this->siteevent->price, $currency); ?>
                    </div>
                </div>
            </li>
						<?php else:?>
							<li>
									<div class="siteevent_listings_stats siteevent_listings_price_free">
											<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
											<div class="o_hidden bold f_small">
													<?php echo $this->translate("FREE"); ?>
											</div>
									</div>
							</li>
						<?php endif;?>
        <?php endif; ?>

        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1) && in_array('addtodiary', $this->showContent) && $this->canCreate): ?> 
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_diary_a"></i>
                    <div class="o_hidden">
        <?php echo $this->AddToDiarySiteevent($this->siteevent, array('text' => 'Add to Diary', 'class' => 'smoothbox')); ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if (in_array('categoryLink', $this->showContent)) : ?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_tag" title="<?php echo $this->translate("Category") ?>"></i>
                    <div class="o_hidden">
                        <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => $this->siteevent->getCategory()->getCategorySlug()), $categoryRouteName); ?>"> 
                            <?php echo $this->translate($this->siteevent->getCategory()->getTitle(true)) ?>
                        </a>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if (in_array('ledBy', $this->showContent)) : ?>
            <?php $leaders = $this->siteevent->getLedBys(false); ?>
            <?php
            // CHECK EVENT HOST
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1)) {
                if ($this->siteevent->host_type == 'user' && $this->siteevent->host_id) {
                    if (in_array('hostName', $this->showContent))
                        $eventHostId = $this->siteevent->host_id;
                    else
                        $eventHostId = '';
                }
            } else {
                $eventHostId = '';
            }
            ?>
            <?php if(!empty($leaders) && is_array($leaders)): ?>
                <?php foreach ($leaders as $leader): ?>
                    <li>
                        <div class="siteevent_profile_info_host">
                                <?php if (empty($eventHostId) || $leader->getIdentity() != $eventHostId) : ?>
                                <span class="h_thumb">
                                    <?php echo $this->htmlLink($leader->getOwner()->getHref(), $this->itemPhoto($leader->getOwner(), 'thumb.icon')); ?>
                                </span>
                                <span class="h_info">
                                    <span class="mbot5"><?php echo $this->htmlLink($leader->getOwner()->getHref(), $leader->getOwner()->getTitle()); ?></span><br />

                                    <a href='<?php echo $this->url(array('action' => 'messageowner', 'event_id' => $this->siteevent->getIdentity(), 'leader_id' => $leader->getOwner()->user_id), "siteevent_specific", true) ?>' class="smoothbox mright5" title="<?php echo $this->translate("Contact"); ?>" title="<?php echo $this->translate("Contact"); ?>">
                                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_msg"></i>

                                    </a>
                                    <?php $tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.profile-siteevent', 'user_profile_index'); ?>
                    <?php $href = $tab_id ? $leader->getOwner()->getHref() . '/tab/' . $tab_id : $leader->getOwner()->getHref(); ?>
                                    <a href="<?php echo $href; ?>" title="<?php echo $this->translate("Show all events"); ?>">
                                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_calendar"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        $statistics = '';

        if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('memberCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s guest', '%s guests', $this->siteevent->member_count), $this->locale()->toNumber($this->siteevent->member_count)) . ', ';
        }

        if (!empty($this->showContent) && in_array('commentCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s comment', '%s comments', $this->siteevent->comment_count), $this->locale()->toNumber($this->siteevent->comment_count)) . ', ';
        }

        if (!empty($this->showContent) && in_array('viewCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s view', '%s views', $this->siteevent->view_count), $this->locale()->toNumber($this->siteevent->view_count)) . ', ';
        }

        if (!empty($this->showContent) && in_array('likeCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s like', '%s likes', $this->siteevent->like_count), $this->locale()->toNumber($this->siteevent->like_count)) . ', ';
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        ?>
        <?php if (!empty($statistics)) : ?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_stats" title="<?php echo $this->translate("Statistics") ?>"></i>
                    <div class="o_hidden">
        <?php echo $statistics; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <?php if (in_array('reviewCount', $this->showContent)): ?>
            <li>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_rating" title="<?php echo $this->translate("Reviews") ?>"></i>
                    <div class="o_hidden stats_rating_star">
                        <div class="fleft f_small"> 
        <?php echo $this->translate(array('%s review', '%s reviews', $this->siteevent->review_count), $this->locale()->toNumber($this->siteevent->review_count)); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php endif; ?>

    <!-- END EVENT INFO WORK -->

    <?php if (!empty($this->showContent) && in_array('tags', $this->showContent) && count($this->siteeventTags) > 0): $tagCount = 0; ?>
        <li>
            <div class="siteevent_listings_stats">
                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_link" title="<?php echo $this->translate("Tags") ?>"></i>
                <div class="o_hidden">
                    <?php foreach ($this->siteeventTags as $tag): ?>
                        <?php if (!empty($tag->getTag()->text)): ?>
                            <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
                            <?php if (empty($tagCount)): ?>
                                <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                                <?php
                                $tagCount++;
                            else:
                                ?>
                                <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && !empty($this->showContent) && in_array('rsvp', $this->showContent)): ?>
        <li>
            <div class="siteevent_listings_stats">
                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_users" title="<?php echo $this->translate("RSVP") ?>"></i>
                <div class="siteevent_stats_content o_hidden">
                    <ul>
                        <li class="f_small">
                            <?php echo $this->locale()->toNumber($this->siteevent->getAttendingCount()) ?>
                            <span><?php echo $this->translate('attending'); ?></span>
                        </li>
                        <li class="f_small">
                            <?php echo $this->locale()->toNumber($this->siteevent->getMaybeCount()) ?>
                            <span><?php echo $this->translate('maybe attending'); ?></span>
                        </li>
                        <li class="f_small">
                            <?php echo $this->locale()->toNumber($this->siteevent->getNotAttendingCount()) ?>
                            <span><?php echo $this->translate('not attending'); ?></span>
                        </li>
                        <li class="f_small">
                            <?php echo $this->locale()->toNumber($this->siteevent->getAwaitingReplyCount()) ?>
                            <span><?php echo $this->translate('awaiting approval'); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </li>    
    <?php endif; ?>

    <?php if (!empty($this->showContent) && in_array('socialShare', $this->showContent) && $this->allowSocialSharing): ?>  
        <li class="mtop10">
            <div class="o_hidden"> 
                <div class="siteevent_social_share">
                    <?php echo $this->code; ?>
                </div>
            </div>
        </li>    
    <?php endif; ?>  
</ul>