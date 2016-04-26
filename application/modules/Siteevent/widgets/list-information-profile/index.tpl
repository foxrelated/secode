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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<script type="text/javascript">
    var seaocore_content_type = 'siteevent_event';
    var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';

    window.addEvent('load', function() {
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'siteevent/index/get-default-event',
            data: {
                format: 'json',
                isAjax: 1,
                type: 'layout_siteevent'
            },
            'onSuccess': function(responseJSON) {
                if (!responseJSON.getEventType) {
                    document.getElement("." + responseJSON.getClassName + "_list_information_profile").empty();
                    document.getElement(".layout_core_container_tabs").empty();
                }
            }
        });
        request.send();
    });
</script>

<?php if (!empty($this->siteevent->closed)) : ?>
    <div class="tip"> 
        <span> <?php echo $this->translate('This event has been cancelled by the owner.'); ?> </span>
    </div>
<?php endif; ?>

<div class="clr siteevent_profile_info">
    <?php if (in_array('photo', $this->showContent)): ?>
        <div class="siteevent_profile_photo_wrapper b_medium photo_zoom">

            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('FEATURED'); ?>"></i>
            <?php endif; ?>
            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                <i class="siteevent_list_new_label" title="<?php echo $this->translate('NEW'); ?>"></i>
            <?php endif; ?>

            <div class='siteevent_profile_photo <?php if ($this->can_edit): ?>siteevent_photo_edit_wrapper<?php endif; ?>'>
                <?php if (!empty($this->can_edit)) : ?>
                    <a class='siteevent_photo_edit' href="<?php echo $this->url(array('action' => 'change-photo', 'event_id' => $this->siteevent->event_id), "siteevent_dashboard", true) ?>">
                        <i class="siteevent_icon"></i>
                        <?php echo $this->translate('Change Picture'); ?>        
                    </a>
                <?php endif; ?>
                <?php if ($this->siteevent->photo_id): ?>
                    <?php $photo = $this->siteevent->getPhoto($this->siteevent->photo_id); ?>

                    <a href="<?php echo $photo->getHref(); ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");
                    return false;' <?php endif; ?>>
                        <?php echo $this->itemPhoto($this->siteevent, 'thumb.profile', '', array('align' => 'center')); ?></a>

                <?php else: ?>
                    <?php echo $this->itemPhoto($this->siteevent, 'thumb.profile', '', array('align' => 'center')); ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                <center class="mtop5 clr">
                    <span class="siteevent_sponsorfeatured_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>;' title="<?php echo $this->translate('SPONSORED'); ?>">
                        <?php echo $this->translate('SPONSORED'); ?>
                    </span>
                </center>
            <?php endif; ?>

            <?php
            if (in_array('photo', $this->showContent) && in_array('photosCarousel', $this->showContent)):
                $widgetContent = $this->content()->renderWidget("siteevent.photos-carousel", array('includeInWidget' => $this->identity, 'minMum' => 2, 'itemCount' => 3))
                ?>
                    <?php if (strlen($widgetContent) > 15): ?>
                    <div class="b_medium siteevent_photoscarousel o_hidden">
                    <?php echo $widgetContent ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->siteevent->photo_id): ?>
                <p class="mtop10">
                    <a href="<?php echo $photo->getHref(); ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");
                    return false;' <?php endif; ?>>
                        <?php if (in_array('photo', $this->showContent) && in_array('photosCarousel', $this->showContent) && strlen($widgetContent) > 15): ?>
                            <?php echo $this->translate('Click on above images to view full picture'); ?>
                        <?php else: ?>
                            <?php echo $this->translate('Click on above image to view full picture'); ?>
                        <?php endif; ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="siteevent_profile_content">
        <div class="siteevent_profile_title">
                <?php if (in_array('title', $this->showContent)): ?>
                <h2>
                <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()); ?>
                </h2>
            <?php endif; ?>

            <?php
            //LIKE BUTTON WORK
            $viewer_id = $this->viewer->getIdentity();
            ?>
            <?php if (!empty($this->like_button) && $this->like_button == 1 && !empty($viewer_id)) : ?>
                <?php
                if (!empty($this->viewer)) {
                    $resourceId = Engine_Api::_()->core()->getSubject()->getIdentity();
                    $check_availability = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('siteevent_event', $resourceId);
                    if (!empty($check_availability)) {
                        $label = 'Unlike this';
                        $unlike_show = "display:inline-block;";
                        $like_show = "display:none;";
                        $like_id = $check_availability;
                    } else {
                        $label = 'Like this';
                        $unlike_show = "display:none;";
                        $like_show = "display:inline-block;";
                        $like_id = 0;
                    }
                }
                ?>
                <?php
                if (empty($this->siteevent_like)) {
                    exit();
                }
                ?>
                <div class="seaocore_like_button" id="siteevent_event_unlikes_<?php echo $resourceId; ?>" style ='<?php echo $unlike_show; ?>' >
                    <a href="javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resourceId; ?>', 'siteevent_event');">
                        <i class="seaocore_like_thumbdown_icon"></i>
                        <span><?php echo $this->translate('Unlike') ?></span>
                    </a>
                </div>
                <div class="seaocore_like_button" id="siteevent_event_most_likes_<?php echo $resourceId; ?>" style ='<?php echo $like_show; ?>'>
                    <a href="javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resourceId; ?>', 'siteevent_event');">
                        <i class="seaocore_like_thumbup_icon"></i>
                        <span><?php echo $this->translate('Like') ?></span>
                    </a>
                </div>
                <input type ="hidden" id = "siteevent_event_like_<?php echo $resourceId; ?>" value = '<?php echo $like_id; ?>' />
            <?php endif; ?>
            <?php //LIKE BUTTON WORK  ?>
        </div>

        <!--//IF EVENT REPEAT MODULE EXIST THEN SHOW EVENT REPEAT INFO WIDGET-->
        <?php
        $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
        if ($siteeventrepeat) {
            $showrepeatinfo = is_array($this->showContent) && in_array('showrepeatinfo', $this->showContent) ? true : false;
            echo $this->content()->renderWidget("siteeventrepeat.event-profile-repeateventdate", array("showrepeatinfo" => $showrepeatinfo));
        }
        ?>
        <?php if ($this->like_button == 2 && $this->success_showFBLikeButton) : ?>
            <?php echo $this->content()->renderWidget("Facebookse.facebookse-commonlike", array('module_current' => 'siteevent', 'subject' => $this->siteevent->getGuid())); ?>
        <?php endif; ?>

        <?php if (!empty($this->showContent)) : ?>
            <?php if (in_array('hostName', $this->showContent)): ?>
                <?php $hostDisplayName = $this->siteevent->getHostName(); ?>
                <?php if (!empty($hostDisplayName)): ?>
                    <div class="siteevent_listings_stats">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="<?php echo $this->translate("Host") ?>"></i>
                        <div class="o_hidden">
                    <?php echo $hostDisplayName; ?><br />
                        </div>
                    </div>    
                <?php endif; ?>
            <?php endif; ?>

            <?php if (in_array('venueName', $this->showContent) && !$this->siteevent->is_online && !empty($this->siteevent->venue_name)) : ?>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>
                    <div class="o_hidden">
                <?php echo $this->siteevent->venue_name; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($locationEnabled && !empty($this->siteevent->location) && in_array('location', $this->showContent)): ?>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                    <div class="o_hidden">
                        <?php echo $this->siteevent->location; ?>
                        <?php if (in_array('directionLink', $this->showContent)): ?>
                            - <b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->siteevent->event_id, 'resouce_type' => 'siteevent_event'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array('startDate', $this->showContent) || in_array('endDate', $this->showContent)) : ?>
                <?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
                <?php $dateTimeInfo = array(); ?>
                <?php $dateTimeInfo['occurrence_id'] = $occurrence_id; ?>
                <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->showContent); ?>
                <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->showContent); ?>
                <?php $this->eventDateTime($this->siteevent, $dateTimeInfo); ?>
            <?php endif; ?>

            <?php if (in_array('price', $this->showContent) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) ) : ?>
               <?php if(!empty($this->siteevent->price) && $this->siteevent->price > 0):?>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
                    <div class="o_hidden bold">
                <?php echo $this->locale()->toCurrency($this->siteevent->price, $currency); ?>
                    </div>
                </div>
								<?php else:?>
									<div class="siteevent_listings_stats siteevent_listings_price_free">
											<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
											<div class="o_hidden bold">
												<?php echo $this->translate("FREE"); ?>
											</div>
									</div>
								<?php endif;?>
            <?php endif; ?>

            <?php if (in_array('ledBy', $this->showContent)) : ?>
                <?php $ledBys = $this->siteevent->getEventLedBys(in_array('hostName', $this->showContent)); ?>
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
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_stats" title="<?php echo $this->translate("Statistics") ?>"></i>
                    <div class="o_hidden">
                        <?php echo $statistics; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array('reviewCount', $this->showContent)): ?>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_rating" title="<?php echo $this->translate("Reviews") ?>"></i>
                    <div class="o_hidden stats_rating_star">
                        <div class="fleft f_small"> 
                <?php echo $this->translate(array('%s review', '%s reviews', $this->siteevent->review_count), $this->locale()->toNumber($this->siteevent->review_count)); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('tags', $this->showContent)): ?>
            <?php if (count($this->siteeventTags) > 0): $tagCount = 0; ?>
                <div class="siteevent_listings_stats">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_link" title="<?php echo $this->translate('Tag') ?>"></i>
                    <div class="o_hidden seaocore_txt_light">
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
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($this->showContent) && in_array('postedDate', $this->showContent)): ?>
            <div class="siteevent_listings_stats">
                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_date" title="<?php echo $this->translate('Posted on') ?>"></i>
                <div class="o_hidden"> 
                    <?php if (in_array('postedDate', $this->showContent)): ?>
                        <?php echo $this->locale()->toEventDateTime($this->siteevent->creation_date, array('size' => $datetimeFormat)); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (($this->phone) || ($this->email) || ($this->website)) : ?>
            <div class="siteevent_listings_stats_wrap o_hidden">
                <?php if (!empty($this->phone)) : ?>
                    <div class="siteevent_listings_stats">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_contact" title="<?php echo $this->translate('Phone') ?>"></i>
                        <div id="showPhoneNumber" class="fleft f_small">
                            <?php echo $this->phone ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($this->email)) : ?>
                    <div class="siteevent_listings_stats">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_mail" title="<?php echo $this->translate('E-mail') ?>"></i>
                        <div id="showEmailAddress" class="fleft f_small">
                            <a href='mailto:<?php echo $this->email ?>' title="<?php echo $this->email ?>"><?php echo $this->translate('Email Me') ?></a>
                        </div>      
                    </div>
               <?php endif; ?>

               <?php if (!empty($this->website)) : ?>
                    <div class="siteevent_listings_stats">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_web" title="<?php echo $this->translate('Website') ?>"></i>
                        <div id="showWebsite" class="fleft f_small">   
                            <?php if (strstr($this->website, 'http://') || strstr($this->website, 'https://')): ?>
                                <a href='<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
                            <?php else: ?>
                                <a href='http://<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
                            <?php endif; ?>
                        </div>
                    </div>    
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->create_review)): ?>
            <div class="clr siteevent_profile_information_option mtop5">
                <div>
                    <?php echo $this->content()->renderWidget("siteevent.review-button", array('event_guid' => $this->siteevent->getGuid(), 'event_profile_page' => 1, 'identity' => $this->identity)) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('description', $this->showContent) && strip_tags($this->siteevent->body)): ?>
            <div class="siteevent_profile_info_sep b_medium"></div>
            <div class="siteevent_profile_information_des">
                <?php if ($this->truncationDescription): ?>
                    <?php echo $this->viewMore(strip_tags($this->siteevent->body), $this->truncationDescription, 5000) ?>
                <?php else: ?>  
                    <?php echo $this->siteevent->body ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($this->gutterNavigation && $this->actionLinks): ?>
          <?php
          echo $this->navigation()
                  ->menu()
                  ->setContainer($this->gutterNavigation)
                  ->setUlClass('siteevent_information_gutter_options b_medium clr')
                  ->render();
          ?>
      	<?php endif; ?>
    </div>
    
</div>
<div class="clr widthfull"></div>
