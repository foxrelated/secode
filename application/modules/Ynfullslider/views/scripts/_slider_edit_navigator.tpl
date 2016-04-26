<div id="transitions-wrapper" class="form-wrapper">
    <div id="ynfullslider_edit_navigator" class="clearfix">
        <?php for($i = 1; $i<7; $i++): ?>
            <div class="ynfullslider_navigator_item" data-navigator_id="<?php echo $i ?>">
                <div class="ynfullslider_navigator_item-thumbs">
                    <img src="application/modules/Ynfullslider/externals/images/nav-<?php echo $i; ?>.jpg" alt="">
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function() {
        jQuery(".ynfullslider_navigator_item").on('click', function(event) {
            var $this = jQuery(this);
            jQuery('.ynfullslider_navigator_item').each(function(index) {
                this.removeClass("selected");
            });
            $this.addClass("selected");
            jQuery("#navigator_id").val($this.data("navigator_id")).change();
        });

        if (jQuery(".ynfullslider_navigator_item[data-navigator_id=" + jQuery("#navigator_id").val() + "]")) {
            jQuery(".ynfullslider_navigator_item[data-navigator_id=" + jQuery("#navigator_id").val() + "]")[0].addClass('selected');
        }
    });
</script>