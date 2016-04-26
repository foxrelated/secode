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

<?php $this->tinyMCESEAO()->addJS(); ?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php
        echo $this->form->render($this);
        ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {

        showAnnouncements('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcementeditor', 1) ?>');
        showDescription('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1); ?>');
        hideHostInfo('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1); ?>');
        showInviteRSVP('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.option', 1) ?>');
        showInviteOtherGuest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1) ?>');

    });

    function showAnnouncements(option) {
        if ($('siteevent_announcementeditor-wrapper')) {
            if (option == 1) {
                $('siteevent_announcementeditor-wrapper').style.display = 'block';
            } else {
                $('siteevent_announcementeditor-wrapper').style.display = 'none';
            }
        }
    }

    function showInviteRSVP(option) {
        if ($('siteevent_invite_rsvp_automatically-wrapper')) {
            if (option == 1) {
                $('siteevent_invite_rsvp_automatically-wrapper').style.display = 'none';
            } else {
                $('siteevent_invite_rsvp_automatically-wrapper').style.display = 'block';
            }
        }
    }

    function showInviteOtherGuest(option) {
        if ($('siteevent_invite_other_automatically-wrapper')) {
            if (option == 1) {
                $('siteevent_invite_other_automatically-wrapper').style.display = 'none';
            } else {
                $('siteevent_invite_other_automatically-wrapper').style.display = 'block';
            }
        }
    }

    function hideHostInfo(value) {
        if (value == 1) {
            $('siteevent_hostinfo-wrapper').style.display = "block";
            $('siteevent_hostOptions-wrapper').style.display = "block";
        } else {
            $('siteevent_hostinfo-wrapper').style.display = "none";
            $('siteevent_hostOptions-wrapper').style.display = "none";
        }
    }

    function showDescription(option) {
        if ($('siteevent_bodyrequired-wrapper')) {
            if (option == 1) {
                $('siteevent_bodyrequired-wrapper').style.display = 'block';
                $('siteevent_hostOptions-wrapper').style.display = "block";
            } else {
                $('siteevent_bodyrequired-wrapper').style.display = 'none';
                $('siteevent_hostOptions-wrapper').style.display = "none";
            }
        }
    }

    var makeRichTextarea = function(element_id) {
        <?php
        echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
            'language' => $this->language,
            'directionality' => $this->directionality,
            'upload_url' => $this->upload_url));
        ?>
    }

    
</script>