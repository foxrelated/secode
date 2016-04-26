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

<?php if ($this->placeWidget == 'smallColumn'): ?>
    <div class="siteevent_profile_host_info siteevent_side_widget siteevent_profile_host_info_side">
        <div class="siteevent_listings_stats siteevent_listings_host event_host" style="margin-top:0;">
            <?php echo $this->htmlLink($this->host->getHref(), $this->itemPhoto($this->host, 'thumb.icon')); ?>
            <span class="o_hidden ">
                <b><?php echo $this->htmlLink($this->host->getHref(), $this->host->getTitle()); ?></b>
            </span>
        </div>
        <?php if (in_array('body', $this->allowedInfo) && $this->getDescription) : ?>
            <div class="host_body show_content_body mbot10"><?php echo $this->getDescription; ?></div>
        <?php endif; ?>

        <?php if (in_array('totalevent', $this->showInfo) || in_array('totalguest', $this->showInfo) || $this->totalRating): ?>  
            <div class="o_hidden host_info_stats clr b_medium mbot10">
                <?php if (in_array('totalevent', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php //echo $this->translate("%s events hosted.", "<b>" .$this->subject()->countOrganizedEvent(). "</b>"); ?>
                        <?php $countOrganizedEvent = $this->subject()->countOrganizedEvent(); ?> 
                        <?php echo $this->translate(array('<b>%s</b> event hosted.', '<b>%s</b> events hosted.', $countOrganizedEvent), $this->locale()->toNumber($countOrganizedEvent)); ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array('totalguest', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php //echo $this->translate("%s guests joined.", "<b>" .$this->totalGuest. "</b>"); ?>
                        <?php echo $this->translate(array('<b>%s</b> guest joined', '<b>%s</b> guests joined.', $this->totalGuest), $this->locale()->toNumber($this->totalGuest)); ?>  
                    </div>
                <?php endif; ?>
                <?php if ($this->totalRating): ?>
                    <div class="clr">
                        <div class="mright5">
                            <?php echo $this->translate("Total ratings:"); ?>
                        </div>
                        <div>
                            <?php echo $this->showRatingStarSiteevent($this->totalRating, 'overall', 'big-star'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>  

        <div class="clr host_contact_options">
            <?php if ($this->host->getType() == 'user' && $this->host->getIdentity() != $this->viewer_id && $this->messageSettings && !empty($this->showInfo) && in_array('messageHost', $this->showInfo)): ?>
                <div class="mbot5">
                    <i class="siteevent_icon_message siteevent_icon siteevent_icon_strip fleft mright5"></i>
                    <div class="o_hidden dblock">
                        <a href='<?php echo $this->url(array('action' => 'messageowner', 'event_id' => $this->siteevent->getIdentity(), 'host_id' => $this->host->getIdentity()), "siteevent_specific", true) ?>' class="smoothbox" title="<?php echo $this->translate("Message host"); ?>"><?php echo $this->translate("Message host"); ?></a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($this->showInfo) && in_array('viewHostProfile', $this->showInfo)): ?>  
                <div class="mbot5">
                    <i class="siteevent_icon_profile siteevent_icon siteevent_icon_strip fleft mright5"></i>
                    <div class="o_hidden dblock">
                        <a href='<?php echo $this->host->getHref(); ?>' title="<?php echo $this->translate("View host profile"); ?>"><?php echo $this->translate("View host profile"); ?></a>
                    </div>
                </div>
            <?php endif; ?>  
            <?php if ($this->host->getType() == 'siteevent_organizer'): ?>
                <?php if (in_array('sociallinks', $this->allowedInfo) && !empty($this->showInfo) && in_array('socialLinks', $this->showInfo)): ?>  
                    <?php if ($this->host->facebook_url): ?>
                        <div class="mbot5">
                            <i class="siteevent_icon_facebook siteevent_icon siteevent_icon_strip fleft mright5"></i>
                            <div class="o_hidden dblock">
                                <a href='https://facebook.com/<?php echo $this->host->facebook_url ?>' target="_blank" title="<?php echo $this->host->facebook_url ?>">facebook.com/<?php echo $this->host->facebook_url ?></a>
                            </div>   
                        </div>

                    <?php endif; ?>
                    <?php if ($this->host->twitter_url): ?>
                        <div class="mbot5">
                            <i class="siteevent_icon_twitter siteevent_icon siteevent_icon_strip fleft mright5"></i>
                            <div class="o_hidden dblock">
                                <a href='https://twitter.com/<?php echo $this->host->twitter_url ?>' target="_blank" title="@<?php echo $this->host->twitter_url; ?>">twitter.com/<?php echo $this->host->twitter_url ?></a>
                            </div>
                        </div>

                    <?php endif; ?>
                    <?php if ($this->host->web_url): ?>
                        <?php
                        //CHECK IF HTTP IS ADDED OR NOT IN THIS URL.
                        $suffix = '';

                        if (strpos($this->host->web_url, "http") === false)
                            $suffix = "http://";
                        ?>
                        <div class="mtop5">
                            <i class="siteevent_icon_website siteevent_icon siteevent_icon_strip fleft mright5"></i>
                            <div class="o_hidden dblock">
                                <a href='<?php echo $suffix . $this->host->web_url ?>' target="_blank" title="<?php echo $this->host->web_url; ?>"><?php echo $this->host->web_url ?></a>
                            </div>
                        </div>
                    <?php endif; ?>  
                <?php endif; ?>
            <?php endif; ?>
        </div> 
    </div>
<?php else: ?>
    <h3 class="event_profile_host_info_main_heading">
        <?php echo $this->translate("Hosted by <b>%s</b>", $this->htmlLink($this->host->getHref(), $this->host->getTitle())); ?>

        <span class="host_contact_options pleft10">
            <?php if ($this->host->getType() == 'user' && $this->host->getIdentity() != $this->viewer_id && $this->messageSettings && !empty($this->showInfo) && in_array('messageHost', $this->showInfo)): ?>
                <a href='<?php echo $this->url(array('action' => 'messageowner', 'event_id' => $this->siteevent->getIdentity(), 'host_id' => $this->host->getIdentity()), "siteevent_specific", true) ?>' class="smoothbox siteevent_icon_message siteevent_icon siteevent_icon_strip" title="<?php echo $this->translate("Message Host"); ?>"></a>
            <?php endif; ?>

            <?php if (!empty($this->showInfo) && in_array('viewHostProfile', $this->showInfo)): ?>  
                <a href='<?php echo $this->host->getHref(); ?>' class="siteevent_icon_profile siteevent_icon siteevent_icon_strip" title="<?php echo $this->translate("View Host profile"); ?>"></a>
            <?php endif; ?>

            <?php if ($this->host->getType() == 'siteevent_organizer'): ?>
                <?php if (in_array('sociallinks', $this->allowedInfo) && !empty($this->showInfo) && in_array('messageHost', $this->showInfo)): ?>  
                    <?php if ($this->host->facebook_url): ?>
                        <a href='https://facebook.com/<?php echo $this->host->facebook_url ?>' target="_blank" class="siteevent_icon_facebook siteevent_icon siteevent_icon_strip" title="<?php echo $this->host->facebook_url ?>"></a>

                    <?php endif; ?>
                    <?php if ($this->host->twitter_url): ?>
                        <a href='https://twitter.com/<?php echo $this->host->twitter_url ?>' target="_blank" class="siteevent_icon_twitter siteevent_icon siteevent_icon_strip" title="@<?php echo $this->host->twitter_url; ?>"></a>

                    <?php endif; ?>
                    <?php if ($this->host->web_url): ?>
                        <?php
                        //CHECK IF HTTP IS ADDED OR NOT IN THIS URL.
                        $suffix = '';

                        if (strpos($this->host->web_url, "http") === false)
                            $suffix = "http://";
                        ?>
                        <a href='<?php echo $suffix . $this->host->web_url ?>' target="_blank" class="siteevent_icon_website siteevent_icon siteevent_icon_strip" title="<?php echo $this->host->web_url; ?>"></a>

                    <?php endif; ?>  
                <?php endif; ?>
            <?php endif; ?>
        </span>
    </h3>
    <div class="siteevent_profile_host_info siteevent_side_widget">
        <div class="o_hidden">
            <span class="host_photo">
                <?php echo $this->htmlLink($this->host->getHref(), $this->itemPhoto($this->host, 'thumb.icon')); ?>
            </span>

            <div class="o_hidden host_info_stats mbot5">
                <?php if (in_array('totalevent', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php echo $this->translate("%s events hosted.", "<b>" . $this->subject()->countOrganizedEvent() . "</b>"); ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array('totalguest', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php echo $this->translate("%s guests joined.", "<b>" . $this->totalGuest . "</b>"); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->totalRating): ?>
                    <div class="mbot5 clr">
                        <div class="mright5">
                            <?php echo $this->translate("Total ratings:"); ?>
                        </div>
                        <div>
                            <?php echo $this->showRatingStarSiteevent($this->totalRating, 'overall', 'big-star'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (in_array('body', $this->allowedInfo) && $this->getDescription) : ?>
                <div class="host_body show_content_body clr">
                    <?php echo $this->getDescription; ?>
                </div>
            <?php endif; ?>
        </div>  
    </div>
<?php endif; ?>