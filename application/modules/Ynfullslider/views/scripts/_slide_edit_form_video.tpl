<div id="ynfullslider_dialog_video">
    <form id="ynfullslider_form_video">
        <div class="form-error"></div>
        <fieldset class="ynfullslider_form_video-tabs">
            <input type="radio" name="video_type" value="html5" id="video_type_htm5">
            <label for="video_type_htm5"><?php echo $this->translate("HTML5 Video") ?></label>

            <input type="radio" name="video_type" value="youtube" id="video_type_youtube">
            <label for="video_type_youtube"><?php echo $this->translate("Youtube") ?></label>
        </fieldset>

        <?php echo $this->partial('_Html5Upload.tpl', 'ynfullslider') ?>
        <input type="hidden" id="photo_id" name="photo_id">
        <input type="hidden" id="temp_photo_path" name="temp_photo_path">
        <input type="hidden" id="video_file_id" name="video_file_id">
        <input type="hidden" id="video_file_path" name="video_file_path">

        <fieldset class="ynfullslider-nopadding">
            <input type="text" name="youtube_iframe" placeholder="<?php echo $this->translate('YouTube embedded code') ?>">
        </fieldset>

        <fieldset class="ynfullslider_form_video-border">
            <label><?php echo $this->translate("Roundness") ?><input type="number" min="0" max="1000" name="css_border-radius"></label>
            <label><?php echo $this->translate("Video border") ?><input type="text" name="css_border-color" class="color"></label>
            <p><?php echo $this->translate("Color") ?></p>
            <label><?php echo $this->translate("Border width") ?><input type="number" min="0" max="1000" name="css_border-width"></label>
            <p><?php echo $this->translate("Thickness (Ex: 1)") ?></p>
        </fieldset>
        <?php echo $this->partial('_slide_edit_transition_display.tpl', 'ynfullslider') ?>
    </form>
</div>