<h2>
    <?php echo $this->translate('YouNet Full Slider') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<?php $slide = $this->slide ?>
<?php $slider = $this->slider ?>

<div class="ynfullslider_manage_sliders">
    <h2 class="ynfullslider_title">
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'index'),
            $this->translate("Manage Sliders"), array())
        ?>
        &nbsp;/&nbsp;
        <?php echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'manage-slides', 'id'=>$slider->getIdentity()),
        $slider->getTitle(), array())
        ?>
        &nbsp;/&nbsp;
        <?php
            if ($this->slide->slide_id)
                echo $this->slide->getTitle();
            else
                echo $this->translate("Add New Slide");
        ?>
    </h2>
</div>

<div class='settings ynfullslider_form ynfullslider_form_addslide'>
    <?php echo $this->form->render($this); ?>
</div>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function() {
        ynfullslider_set_color('slide_background');
        updateFields();
    });

    function updateFields() {
        $$('div[id=background_image_setting-wrapper]').hide();
        $$('div[id=background_image_url-wrapper]').hide();

        $$('div[id=background_colors-wrapper]').hide();

        $$('div[id=file-wrapper]').hide();
        $$('div[id=background_video_setting-wrapper]').hide();


        var new_value = $$('input[name=background_option]:checked')[0].get('value');
        $$('input[name=background_option]').getParent('li').removeClass('selected');
        $$('input[name=background_option]:checked')[0].getParent('li').addClass('selected');

        if (0 == new_value) {
            $$('div[id=background_colors-wrapper]').show();
        }
        else if (1 == new_value) {
            $$('div[id=background_image_setting-wrapper]').show();
            $$('div[id=background_image_url-wrapper]').show();
        }
        else if (2 == new_value) {
            $$('div[id=file-wrapper]').show();
            $$('div[id=background_video_setting-wrapper]').show();
        }
    }
</script>