<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mainPhotoCoverContent.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="seaocore_profile_cover_head_section_inner" id="seaocore_profile_cover_head_section_inner">
    <?php if (is_array($this->showContent) && (($this->profile_like_button == 1) || in_array('inviteGuest', $this->showContent) || in_array('joinButton', $this->showContent) || in_array('updateInfoButton', $this->showContent) || in_array('inviteRsvpButton', $this->showContent) || (in_array('optionsButton', $this->showContent) ))): ?>
        <div class="seaocore_profile_coverinfo_buttons">

            <?php if ($this->profile_like_button == 1) : ?>
                <div>
                    <?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
                </div>	
            <?php endif; ?>

            <?php
            //if the event is past event then we will not show the invite link
            $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->subject()->event_id, 'DESC', $occurrence_id);
            if (is_array($this->showContent) && (in_array('inviteGuest', $this->showContent)) && strtotime($endDate) > time()):
                ?>
                <?php if (Engine_Api::_()->hasModuleBootstrap('siteeventinvite')): ?>
                    <div class="seaocore_like_button">
                        <a href ="<?php echo $this->url(array('controller' => 'index', 'action' => 'friendseventinvite', 'siteevent_id' => $this->subject()->getIdentity(), 'occurrence_id' => $occurrence_id, 'user_id' => $this->subject()->owner_id), 'siteeventinvite_invite', true); ?>">
                            <i class="seaocore_invite_guests"></i>
                            <span><?php echo $this->translate('Invite Guests') ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="seaocore_like_button">
                        <a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $this->subject()->getIdentity(), 'occurrence_id' => $occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")'>
                            <i class="seaocore_invite_guests"></i>
                            <span><?php echo $this->translate('Invite Guests') ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (is_array($this->showContent) && in_array('joinButton', $this->showContent)): ?>
                <div id="event_membership" class="seaocore_like_button">
                    <?php echo $this->eventLinks($this->subject()) ?>
                </div>
            <?php endif; ?>

            <?php if (is_array($this->showContent) && in_array('updateInfoButton', $this->showContent) && $this->can_edit) : ?>
                <div class="seaocore_button">
                    <a href="<?php echo $this->url(array('action' => 'edit', $this->tablePrimaryFieldName => $this->subject()->getIdentity()), $moduleName . "_specific", true); ?>">
                        <span><?php echo $this->translate("Dashboard"); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (is_array($this->showContent) && in_array('inviteRsvpButton', $this->showContent)) : ?>
                <div>
                    <?php echo $this->content()->renderWidget("siteevent.invite-rsvp-siteevent"); ?>
                </div>
            <?php endif; ?>

            <?php if (is_array($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
                <?php $this->navigationProfile = $coreMenus->getNavigation($moduleName . "_gutter"); ?>
                <?php if (count($this->navigationProfile) > 0): ?>
                    <div class="seaocore_button seaocore_profile_option_btn prelative">
                        <a href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
                        <ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
                            <li>
                                <?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setUlClass('navigation siteevents_gutter_options')->render(); ?>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <div class="seaocore_profile_coverinfo_status">
        <?php if (is_array($this->showContent) && in_array('title', $this->showContent)): ?>
            <?php if (empty($this->cover_photo_preview)): ?>
                <a href="<?php echo $this->subject()->getHref(); ?>"><h2><?php echo $this->subject()->getTitle(); ?></h2></a>
            <?php else: ?>
                <h2><?php echo $this->translate("Event Title") ?></h2>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (is_array($this->showContent) && in_array('venue', $this->showContent) && !$this->subject()->is_online && $this->subject()->venue_name): ?> 
            <div class="seaocore_profile_coverinfo_stats seaocore_txt_light">
                <?php echo $this->translate('Venue'); ?> -     
                <?php echo $this->subject()->venue_name; ?>
            </div> 
        <?php endif; ?>

        <?php if (is_array($this->showContent) && !in_array('dateTime', $this->showContent)): ?> 
            <div class="seaocore_profile_coverinfo_stats seaocore_txt_light">    
                <?php
                // Convert the dates for the viewer
                $startDateObject = new Zend_Date(strtotime($this->subject()->starttime));
                $endDateObject = new Zend_Date(strtotime($this->subject()->endtime));
                if ($this->viewer() && $this->viewer()->getIdentity()) {
                    $tz = $this->viewer()->timezone;
                    $startDateObject->setTimezone($tz);
                    $endDateObject->setTimezone($tz);
                }
                ?>
                <?php if ($this->subject()->starttime == $this->subject()->endtime): ?>
                    <?php echo $this->translate('Date') ?>
                    <?php echo $this->locale()->toDate($startDateObject) ?>
                    <?php echo $this->translate('Time') ?>
                    <?php echo $this->locale()->toTime($startDateObject) ?>

                <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>

                    <?php echo $this->translate('Date') ?>
                    <?php echo $this->locale()->toDate($startDateObject) ?>
                    <?php echo $this->translate('Time') ?>
                    <?php echo $this->locale()->toTime($startDateObject) ?>
                    -
                    <?php echo $this->locale()->toTime($endDateObject) ?>
                <?php else: ?>  
                    <?php
                    echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
                    )
                    ?>
                    <?php echo $this->translate("-"); ?>
                    <?php
                    echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
                    )
                    ?>
                <?php endif ?>
            </div>  
        <?php endif ?>
    </div>
    <?php $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse'); ?>
    <?php if ($fbmodule && !empty($fbmodule->enabled) && ($this->profile_like_button == 2)) : ?>
        <div class="seaocore_profile_cover_fb_like_button"> 
            <?php echo $this->content()->renderWidget("Facebookse.facebookse-commonlike", array('subject' => $this->subject()->getGuid())); ?>
        </div>	
    <?php endif; ?>
</div>