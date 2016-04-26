<!-- FOR TEXT ELEMENT -->
<div id="ynfullslider_dialog_text">
    <form id="ynfullslider_form_text">
        <textarea name="body"></textarea>
        <fieldset class="ynfullslider_form_text_mce">
            <label><?php echo $this->translate("Background color") ?><input type="text" name="css_background" class="color"></label>
            <label><?php echo $this->translate("Background roundness") ?><input type="number" min="0" max="1000" name="css_border-radius"></label>
            <label><?php echo $this->translate("Letter-spacing") ?><input type="text" name="css_letter-spacing"></label>
            <p><?php echo $this->translate("Defines an extra space between characters (Ex: 1)") ?></p>
        </fieldset>
        <?php echo $this->partial('_slide_edit_transition_display.tpl', 'ynfullslider') ?>
    </form>
</div>
