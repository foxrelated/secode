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
<?php $title = $this->viewer->getTitle(); ?>
<?php $link = $this->viewer->getHref(); ?>
<?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>

<div class="seaocore_button prelative seaocore_cover_rsvp_button o_hidden" id="siteevent-member-rsvp">
    <a href="javascript:void(0);" onclick="en4.siteevent.member.showDropDown();"  <?php if ($this->rsvp == 2): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> id="going-rsvp" >

        <span><?php echo $this->translate('Attending') ?></span><i class="icon_down"></i>
    </a>
    <a href="javascript:void(0);" onclick="en4.siteevent.member.showDropDown();" <?php if ($this->rsvp == 1): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> id="maybe-going-rsvp">

        <span><?php echo $this->translate('Maybe Attending') ?></span><i class="icon_down"></i>
    </a>
    <a href="javascript:void(0);" onclick="en4.siteevent.member.showDropDown();" <?php if ($this->rsvp == 0): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?>  id="not-going-rsvp">

        <span><?php echo $this->translate('Not Attending') ?></span>
        <i class="icon_down"></i>
    </a>
    <select onchange="en4.siteevent.member.selectRsvp(this.value, '<?php echo $this->rsvp; ?>', '<?php echo $this->subject()->getIdentity(); ?>', '<?php echo $occurrence_id; ?>')">
        <option value='2' <?php if ($this->rsvp == 2): ?> selected = 'selected' <?php endif; ?>><?php echo $this->translate('Attending'); ?></option>
        <option value='1' <?php if ($this->rsvp == 1): ?> selected = 'selected' <?php endif; ?>><?php echo $this->translate('Maybe Attending'); ?></option>
        <option value='0' <?php if ($this->rsvp == 0): ?> selected = 'selected' <?php endif; ?>><?php echo $this->translate('Not Attending'); ?></option>
    </select>
</div>

<div class="seaocore_cover_member_tip" id="siteevent-member-tip" style="display:none;">
    <div class="seaocore_cover_member_tip_a">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tip-arrow-top.png" />
    </div>
    <h4><?php echo $this->translate("Tell Friends Why You're Attending"); ?></h4>
    <div class="o_hidden">
        <div class="fleft seaocore_cover_member_tip_photo">
            <?php
            echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon', $this->viewer->getTitle()), array()
            )
            ?>
        </div>
        <div class="o_hidden">
            <div class="mbot5">
                <?php echo $this->translate("%s will be attending the event:", "<a class='bold' href='$link'>$title</a>"); ?>
            </div>
            <div class="clr">
                <form method="post">
                    <input class="mbot5" type="text" id="rsvp-going" name="body" value="" placeholder="<?php echo $this->translate("Share your reason for attending."); ?>" style="width:220px;"><br />
                    <button type="submit" id="submit" name="submit" onclick="en4.siteevent.member.sendActivityRsvpGoing('<?php echo $occurrence_id; ?>');
            return false;"><?php echo $this->translate("Post"); ?></button>
                    <span id="siteevent_loading_image"></span>
                    <?php echo $this->translate(" or "); ?>   
                    <a onclick="hideSiteeventMemberTip();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="seaocore_cover_member_tip" id="siteevent-member-tip-maybe-going" style="display:none;">
    <div class="seaocore_cover_member_tip_a">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tip-arrow-top.png" />
    </div>
    <h4><?php echo $this->translate("Tell Friends Why You Maybe Attending"); ?></h4>
    <div class="o_hidden">
        <div class="fleft seaocore_cover_member_tip_photo">
            <?php
            echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon', $this->viewer->getTitle()), array()
            )
            ?>
        </div>
        <div class="o_hidden">
            <div class="mbot5">
                <?php echo $this->translate("%s maybe attending the event:", "<a class='bold' href='$link'>$title</a>"); ?>
            </div>
            <div class="clr">
                <form method="post">
                    <input class="mbot5" type="text" id="rsvp-maybe-going" name="body" value="" placeholder="<?php echo $this->translate("Share your reason for maybe attending."); ?>" style="width:230px;"><br />
                    <button type="submit" id="submit" name="submit" onclick="en4.siteevent.member.sendActivityRsvpMayBeGoing('<?php echo $occurrence_id; ?>');
            return false;"><?php echo $this->translate("Post"); ?></button>
                    <span id="siteevent_maybe_going_loading_image"></span>
                    <?php echo $this->translate(" or "); ?>   
                    <a onclick="hideSiteeventMemberTipMayBeGoing();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="seaocore_cover_member_tip" id="siteevent-member-tip-not-going" style="display:none;">
    <div class="seaocore_cover_member_tip_a">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tip-arrow-top.png" />
    </div>
    <h4><?php echo $this->translate("Tell Friends Why You're Not Attending"); ?></h4>
    <div class="o_hidden">
        <div class="fleft seaocore_cover_member_tip_photo">
            <?php
            echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon', $this->viewer->getTitle()), array()
            )
            ?>
        </div>
        <div class="o_hidden">
            <div class="mbot5">
                <?php echo $this->translate("%s will not be attending the event:", "<a class='bold' href='$link'>$title</a>"); ?>
            </div>
            <div class="clr">
                <form method="post">
                    <input class="mbot5" type="text" id="rsvp-not-going" name="body" value="" placeholder="<?php echo $this->translate("Share your reason for not attending."); ?>" style="width:220px;"><br />
                    <button type="submit" id="submit" name="submit" onclick="en4.siteevent.member.sendActivityRsvpNotGoing('<?php echo $occurrence_id; ?>');
            return false;"><?php echo $this->translate("Post"); ?></button>
                    <span id="siteevent_not_going_loading_image"></span>
                    <?php echo $this->translate(" or "); ?>   
                    <a onclick="hideSiteeventMemberTipNotGoing();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

        function hideSiteeventMemberTip() {
            $('rsvp-going').value = '';
            $('siteevent-member-tip').style.display = "none";
            if ($('tip-loaded'))
                $('tip-loaded').destroy();
        }

        function hideSiteeventMemberTipNotGoing() {
            $('rsvp-not-going').value = '';
            $('siteevent-member-tip-not-going').style.display = "none";
            if ($('tip-loaded'))
                $('tip-loaded').destroy();
        }
        function hideSiteeventMemberTipMayBeGoing() {
            $('rsvp-maybe-going').value = '';
            $('siteevent-member-tip-maybe-going').style.display = "none";
            if ($('tip-loaded'))
                $('tip-loaded').destroy();
        }
</script>