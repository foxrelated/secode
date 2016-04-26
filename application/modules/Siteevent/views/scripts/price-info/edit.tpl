<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>

<div class="siteevent_form_popup">
    <?php echo $this->form->render($this) ?>
</div>

<?php if ($this->responseHTML): ?>

    <script type="text/javascript">
        window.addEvent('domready', function() {
            parent.$('priceinfo_content').innerHTML = "<?php echo $this->string()->escapeJavascript($this->responseHTML, false) ?>";

            en4.core.runonce.trigger();
            parent.Smoothbox.bind(parent.$('priceinfo_content'));
            parent.Smoothbox.close();

        });
    </script>

<?php else: ?>

    <script type="text/javascript">
        function otherWhereToBuy(value) {
            if (value == 1) {
                $('title-wrapper').style.display = "block";
                $('address-wrapper').style.display = "block";
                $('contact-wrapper').style.display = "block";
            } else {
                $('title-wrapper').style.display = "none";
                $('address-wrapper').style.display = "none";
                $('contact-wrapper').style.display = "none";
            }
        }
        window.addEvent('domready', function() {
            otherWhereToBuy($('wheretobuy_id').value);
        });
    </script>

<?php endif; ?>
