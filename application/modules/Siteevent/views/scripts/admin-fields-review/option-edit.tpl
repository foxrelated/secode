<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: option-edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->form): ?>
    <?php echo $this->form->render($this) ?>
<?php else: ?>
    <div class="global_form_popup_message">
        <?php echo $this->translate("Your changes have been saved.") ?>
    </div>

    <script type="text/javascript">
        parent.onOptionEdit(
    <?php echo Zend_Json::encode($this->option) ?>,
    <?php echo Zend_Json::encode($this->htmlArr) ?>
        );
        (function() {
            parent.Smoothbox.close();
        }).delay(1000);
    </script>

<?php endif; ?>