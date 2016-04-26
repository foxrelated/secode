<div id="shadow-wrapper" class="form-wrapper">
    <div id="shadow-label" class="form-label">
        <label for="shadow">
            <?php echo $this -> translate("Shadow styles")?>
        </label>
    </div>
    <div id="ynfullslider_edit_shadow">
        <?php for($i = 0; $i<4; $i++): ?>
            <div class="ynfullslider_shadow_item" data-background_shadow_id="<?php echo $i ?>">
                <div class="ynfullslider_shadow_item-thumbs">
                    <img src="application/modules/Ynfullslider/externals/images/shadow-<?php echo $i; ?>.jpg" alt="">
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function() {
        jQuery(".ynfullslider_shadow_item").on('click', function(event) {
            var $this = jQuery(this);
            jQuery('.ynfullslider_shadow_item').each(function(index) {
                this.removeClass("selected");
            });
            $this.addClass("selected");
            jQuery("#background_shadow_id").val($this.data("background_shadow_id")).change();
        });

        if (jQuery(".ynfullslider_shadow_item[data-background_shadow_id=" + jQuery("#background_shadow_id").val() + "]")) {
            jQuery(".ynfullslider_shadow_item[data-background_shadow_id=" + jQuery("#background_shadow_id").val() + "]")[0].addClass('selected');
        }
    });
</script>