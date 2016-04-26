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
<?php if (in_array('photo', $this->showInfo)): ?>
    <div id="profile_photo" class="mbot5">
        <?php echo $this->itemPhoto($this->organizer, 'thumb.profile') ?>
    </div>
<?php endif; ?>
<div class="o_hidden">
    <?php if (in_array('title', $this->showInfo)): ?>
        <h2><?php echo $this->organizer->getTitle(); ?></h2>
    <?php endif; ?>

    <?php if (in_array('creator', $this->showInfo)): ?>   
        <div class="o_hidden clr mbot10 f_small seaocore_txt_light">
            <?php echo $this->translate("Added by %s", $this->htmlLink($this->organizer->getOwner()->getHref(), $this->organizer->getOwner()->getTitle())); ?>
        </div>
    <?php endif; ?>

    <?php if (in_array('options', $this->showInfo) && $this->viewer_id && ($this->viewer_id == $this->organizer->creator_id || $this->level_id == 1)): ?>
        <div class="clr mbot10 host_options">
            <a href="<?php echo $this->url(array('action' => 'edit', 'controller' => 'organizer', 'type' => $this->organizer->getType(), 'organizer_id' => $this->organizer->getIdentity()), 'siteevent_extended', true); ?>" class="buttonlink seaocore_icon_edit smoothbox mright5">
                <?php echo $this->translate("Edit") ?>
            </a>

            <a href="<?php echo $this->url(array('action' => 'delete', 'controller' => 'organizer', 'type' => $this->organizer->getType(), 'organizer_id' => $this->organizer->getIdentity()), 'siteevent_extended', true); ?>" class="buttonlink seaocore_icon_delete smoothbox">
                <?php echo $this->translate("Delete Host") ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($this->showInfo && (in_array('totalevent', $this->showInfo) || in_array('totalguest', $this->showInfo) || in_array('totalrating', $this->showInfo) || in_array('description', $this->showInfo))): ?> 
        <div class="o_hidden host_info_stats mbot10">
            <?php if (in_array('totalevent', $this->showInfo)): ?> 
                <div class="mbot5 clr">
                    <?php $countOrganizedEvent = $this->organizer->countOrganizedEvent(); ?> 
                    <?php echo $this->translate(array('<b>%s</b> event hosted', '<b>%s</b> events hosted', $countOrganizedEvent), $this->locale()->toNumber($countOrganizedEvent)); ?><?php if (in_array('totalguest', $this->showInfo)): ?>,
                        <?php echo $this->translate(array('<b>%s</b> guest joined', '<b>%s</b> guests joined', $this->totalGuest), $this->locale()->toNumber($this->totalGuest)); ?>  
                    <?php endif; ?>  
                </div>
            <?php endif; ?>
            <?php if ($this->totalRating && in_array('totalrating', $this->showInfo)): ?>
                <div class="mbot10 clr">
                    <div class="mright5">
                        <?php echo $this->translate("Total ratings:"); ?>
                    </div>
                    <div>
                        <?php echo $this->showRatingStarSiteevent($this->totalRating, 'overall', 'big-star'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array('body', $this->allowedInfo) && in_array('description', $this->showInfo)): ?> 
                <div class='clr show_content_body'>
                    <?php echo $this->organizer->description; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (in_array('sociallinks', $this->allowedInfo) && in_array('links', $this->showInfo) && ($this->organizer->facebook_url || $this->organizer->twitter_url || $this->organizer->web_url)): ?> 
        <div class="host_contact_options mbot10 fleft widthfull">
            <?php if ($this->organizer->facebook_url): ?>
                <div class="mbot5">
                    <i class="siteevent_icon_facebook siteevent_icon siteevent_icon_strip fleft mright5"></i>
                    <div class="o_hidden dblock">
                        <a href='https://facebook.com/<?php echo $this->organizer->facebook_url ?>' target="_blank" title="<?php echo $this->organizer->facebook_url ?>">facebook.com/<?php echo $this->organizer->facebook_url ?></a>
                    </div>   
                </div>
            <?php endif; ?>

            <?php if ($this->organizer->twitter_url): ?>
                <div class="mbot5">
                    <i class="siteevent_icon_twitter siteevent_icon siteevent_icon_strip fleft mright5"></i>
                    <div class="o_hidden dblock">
                        <a href='https://twitter.com/<?php echo $this->organizer->twitter_url ?>' target="_blank" title="@<?php echo $this->organizer->twitter_url; ?>">twitter.com/<?php echo $this->organizer->twitter_url; ?></a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->organizer->web_url): ?>
                <?php
                //CHECK IF HTTP IS ADDED OR NOT IN THIS URL.
                $suffix = '';

                if (strpos($this->organizer->web_url, "http") === false)
                    $suffix = "http://";
                ?>

                <div class="mtop5">
                    <i class="siteevent_icon_website siteevent_icon siteevent_icon_strip fleft mright5"></i>
                    <div class="o_hidden dblock">
                        <a href='<?php echo $suffix . $this->organizer->web_url ?>' target="_blank" title="<?php echo $this->organizer->web_url; ?>"><?php echo $this->organizer->web_url; ?></a>
                    </div>
                </div>
            <?php endif; ?>  
        </div> 
    <?php endif; ?>
</div>


