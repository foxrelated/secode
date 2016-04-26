<div id="ynfullslider_dialog_button">
    <form id="ynfullslider_form_button">
        <textarea name="body"></textarea>
        <fieldset class="ynfullslider_form_button-linkto">
            <label><input type="text" name="link_to" placeholder='<?php echo $this->translate("Link to") ?>'></label>
        </fieldset>
        <fieldset>
            <label><?php echo $this->translate("Roundness") ?><input type="number" min="0" max="1000" name="css_border-radius"></label>
            <label><?php echo $this->translate("Background color") ?><input type="text" name="css_background-color" class="color"></label>
            <label><?php echo $this->translate("Button border") ?><input type="text" name="css_border-color" class="color"></label>
            <p><?php echo $this->translate("Color") ?></p>
            <label><?php echo $this->translate("Border width") ?><input type="number" min="0" max="1000" name="css_border-width"></label>
            <p><?php echo $this->translate("Thickness (Ex: 1)") ?></p>
        </fieldset>
        <?php echo $this->partial('_slide_edit_transition_display.tpl', 'ynfullslider') ?>
    </form>
</div>