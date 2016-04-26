<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: set-template.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
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

<div class='seaocore_settings_form siteevent_global_settings'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    var formElements = document.getElementById('template');
    function confirmSubmit(event) {
        if (confirm('<?php echo $this->string()->escapeJavascript("Any previous changes will be lost if you change layout for selected pages. Are you sure that you want to change layout of selected pages?") ?>')) {
            formElements.submit();
        }
    }

    var formElements = document.getElementById('template');
    formElements.addEvent('submit', function(event) {
        event.stop();
        confirmSubmit(event);
    });

</script>    
