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
<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->subNavigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    window.addEvent('domready', function() {
        prosconsInReviews('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proscons', 1); ?>');
        hideOwnerReviews('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2); ?>');
    });

    function prosconsInReviews(option) {
        if ($('siteevent_proncons-wrapper')) {
            if (option == 1) {
                $('siteevent_proncons-wrapper').style.display = 'block';
                $('siteevent_limit_proscons-wrapper').style.display = 'block';
            } else {
                $('siteevent_proncons-wrapper').style.display = 'none';
                $('siteevent_limit_proscons-wrapper').style.display = 'none';
            }
        }
    }

    function hideOwnerReviews(option) {
        if ($('siteevent_allowownerreview-wrapper')) {
            if (option == 2 || option == 3) {
                $('siteevent_allowownerreview-wrapper').style.display = 'block';
                $('siteevent_allowreview-wrapper').style.display = 'block';
            } else {
                $('siteevent_allowownerreview-wrapper').style.display = 'none';
                $('siteevent_allowreview-wrapper').style.display = 'none';
            }
        }
    }

</script>