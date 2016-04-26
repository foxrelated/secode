<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: notification-settings.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    var notification = '<?php echo $this->notification; ?>';
    var email = '<?php echo $this->email; ?>';
    en4.core.runonce.add(function() {
        if (notification == 1) {
            notificationSettings('block');
        } else if (notification == '' || notification == 0) {
            notificationSettings('none');
        }

        if (email == 1) {
            emailSettings('block');
        } else if (email == '' || email == 0) {
            emailSettings('none');
        }
    });

    window.addEvent('domready', function() {
        if (notification == 1) {
            notificationSettings('block');
        } else if (notification == '' || notification == 0) {
            notificationSettings('none');
        }

        if (email == 1) {
            emailSettings('block');
        } else if (email == '' || email == 0) {
            emailSettings('none');
        }
    });

    function showNotificationAction() {
        if ($('notification').checked == true) {
            notificationSettings('block');
             if($('action_notification-posted'))
            $('action_notification-posted').checked = true;
             if($('action_notification-created'))
            $('action_notification-created').checked = true;
            if($('action_notification-follow'))
            $('action_notification-follow').checked = true;
            if($('action_notification-like'))
            $('action_notification-like').checked = true;
            if($('action_notification-comment'))
            $('action_notification-comment').checked = true;
            if($('action_notification-joined'))
            $('action_notification-joined').checked = true;
            if($('action_notification-rsvp'))
            $('action_notification-rsvp').checked = true;
        
            if($('action_notification-title'))
            $('action_notification-title').checked = true;            
            if($('action_notification-location'))
            $('action_notification-location').checked = true;            
            if($('action_notification-venue'))
            $('action_notification-venue').checked = true;            
            if($('action_notification-time'))
            $('action_notification-time').checked = true;
        
        } else {
            notificationSettings('none');
            if($('action_notification-posted'))
            $('action_notification-posted').checked = false;
            if($('action_notification-created'))
            $('action_notification-created').checked = false;
            if($('action_notification-follow'))
            $('action_notification-follow').checked = false;
            if($('action_notification-like'))
            $('action_notification-like').checked = false;
            if($('action_notification-comment'))
            $('action_notification-comment').checked = false;
            if($('action_notification-joined'))
            $('action_notification-joined').checked = false;
            if($('action_notification-rsvp'))
            $('action_notification-rsvp').checked = false;
            if($('action_notification-title'))
            $('action_notification-title').checked = false;            
            if($('action_notification-location'))
            $('action_notification-location').checked = false;            
            if($('action_notification-venue'))
            $('action_notification-venue').checked = false;            
            if($('action_notification-time'))
            $('action_notification-time').checked = false;
        }
    }

    function showEmailAction() {
        if ($('email').checked == true) {
            emailSettings('block');
            if($('action_email-posted'))
            $('action_email-posted').checked = true;
            if($('action_email-created'))
            $('action_email-created').checked = true;
            if($('action_email-joined'))
            $('action_email-joined').checked = true;
            if($('action_email-rsvp'))
            $('action_email-rsvp').checked = true;
        } else {
            emailSettings('none');
            if($('action_email-posted'))
            $('action_email-posted').checked = false;
            if($('action_email-created'))
            $('action_email-created').checked = false;
            if($('action_email-joined'))
            $('action_email-joined').checked = false;
            if($('action_email-rsvp'))
            $('action_email-rsvp').checked = false;
        }
    }

    function notificationSettings(option) {
        setTimeout(function() {
            if ($('action_notification-wrapper')) {
                if (option == 'block') {
                    $('action_notification-wrapper').style.display = 'block';
                } else if (option == 'none') {
                    $('action_notification-wrapper').style.display = 'none';
                }
            }
        }, '50');
    }

    function emailSettings(option) {
        setTimeout(function() {
            if ($('action_email-wrapper')) {
                if (option == 'block') {
                    $('action_email-wrapper').style.display = 'block';
                } else if (option == 'none') {
                    $('action_email-wrapper').style.display = 'none';
                }
            }
        }, '50');
    }

</script>

<script type="text/javascript" >
    var submitformajax = 1;
    //var manage_admin_formsubmit = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
        <?php echo $this->form->render($this); ?>
    </div>
    <div id="show_tab_content_child"></div>
</div>
</div>