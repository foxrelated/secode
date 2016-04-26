<div id="ynfullslider_dialog_image">
    <form id="ynfullslider_form_image" class="ynfullslider_form">
        <div class="form-error"></div>
        <fieldset class="ynfullslider_form_image-selectimg">
            <input type="file" accept="image/*" id="imageToUpload" name="imageToUpload" onchange="ynfullsliderImageSelected();">
            <input type="hidden" id="file_id" name="file_id">
            <input type="hidden" id="image_path" name="image_path">
            <label for="imageToUpload"><?php echo $this->translate("Choose file...") ?></label>
        </fieldset>
        <fieldset class="ynfullslider_image_form_image-block">
            <div id="ynfullslider_image_form_image">
            </div>
        </fieldset>

        <fieldset class="ynfullslider_form_image-linkto"><input type="text" name="link_to" placeholder="<?php echo $this->translate("Link to") ?>"></fieldset>

        <fieldset class="a">
            <label><?php echo $this->translate("Roundness") ?><input type="number" min="0" max="1000" name="css_border-radius"></label>
            <label><?php echo $this->translate("Image border") ?><input type="text" name="css_border-color" class="color"></label>
            <p><?php echo $this->translate("Color") ?></p>
            <label><?php echo $this->translate("Border width") ?><input type="number" min="0" max="1000" name="css_border-width"></label>
            <p><?php echo $this->translate("Thickness (Ex: 1)") ?></p>
        </fieldset>
        <?php echo $this->partial('_slide_edit_transition_display.tpl', 'ynfullslider') ?>
    </form>
</div>