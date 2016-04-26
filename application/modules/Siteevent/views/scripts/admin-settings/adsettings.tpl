<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsettings.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">

    window.addEvent('domready', function() {
        showads('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1); ?>');
    });

    function showads(option) {
        if (option == 1) {
            if ($('siteevent_adalbumcreate-wrapper'))
                $('siteevent_adalbumcreate-wrapper').style.display = 'block';
            if ($('siteevent_addiscussionview-wrapper'))
                $('siteevent_addiscussionview-wrapper').style.display = 'block';
            if ($('siteevent_addiscussioncreate-wrapper'))
                $('siteevent_addiscussioncreate-wrapper').style.display = 'block';
            if ($('siteevent_addiscussionreply-wrapper'))
                $('siteevent_addiscussionreply-wrapper').style.display = 'block';
            if ($('siteevent_adtopicview-wrapper'))
                $('siteevent_adtopicview-wrapper').style.display = 'block';
            if ($('siteevent_advideocreate-wrapper'))
                $('siteevent_advideocreate-wrapper').style.display = 'block';
            if ($('siteevent_advideoedit-wrapper'))
                $('siteevent_advideoedit-wrapper').style.display = 'block';
            if ($('siteevent_advideodelete-wrapper'))
                $('siteevent_advideodelete-wrapper').style.display = 'block';
            if ($('siteevent_adtagview-wrapper'))
                $('siteevent_adtagview-wrapper').style.display = 'block';
        }
        else {
            if ($('siteevent_adalbumcreate-wrapper'))
                $('siteevent_adalbumcreate-wrapper').style.display = 'none';
            if ($('siteevent_addiscussionview-wrapper'))
                $('siteevent_addiscussionview-wrapper').style.display = 'none';
            if ($('siteevent_addiscussioncreate-wrapper'))
                $('siteevent_addiscussioncreate-wrapper').style.display = 'none';
            if ($('siteevent_addiscussionreply-wrapper'))
                $('siteevent_addiscussionreply-wrapper').style.display = 'none';
            if ($('siteevent_adtopicview-wrapper'))
                $('siteevent_adtopicview-wrapper').style.display = 'none';
            if ($('siteevent_advideocreate-wrapper'))
                $('siteevent_advideocreate-wrapper').style.display = 'none';
            if ($('siteevent_advideoedit-wrapper'))
                $('siteevent_advideoedit-wrapper').style.display = 'none';
            if ($('siteevent_advideodelete-wrapper'))
                $('siteevent_advideodelete-wrapper').style.display = 'none';
            if ($('siteevent_adtagview-wrapper'))
                $('siteevent_adtagview-wrapper').style.display = 'none';
        }
    }
</script>