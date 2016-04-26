<div class="ynfullslider_global_content">
    <h2>
        <?php echo $this->translate('YouNet Full Slider Plugin') ?>
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

    <div class="ynfullslider_manage_sliders">
        <h2 class="ynfullslider_title">
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'index'),
                $this->translate("Manage Sliders"), array())
            ?>
            &nbsp;/&nbsp;
            <?php
            if ($this->slider->slider_id)
                echo $this->slider->getTitle();
            else
                echo $this->translate("Add New Slider");
            ?>
        </h2>
    </div>
</div>


<div id="preview_container">
    <?php echo $this->partial('_slider_preview.tpl', array('params' => $this->slider->getParams())) ?>
</div>

<?php echo $this->partial('_slider_edit_steps.tpl', array('steps'=>$this->steps, 'currentStepIndex'=>$this->currentStepIndex, 'slider_id'=>$this->slider->getIdentity())) ?>

<div class="ynfullslider_global_content">
    <div class='settings ynfullslider_form ynfullslider_form_navigator'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function() {
        ynfullslider_set_color('navigator');
        jQuery('form.global_form').find('input[type=hidden],input[type=text],select').change(ynfullsliderRefreshPreviewSlider);
    });

    function ynfullsliderPreviewSlider() {
        var currentParams = <?php echo json_encode($this->slider->getParams()) ?>;
        var previewSliderUrl = "<?php echo $this->url(array('module'=>'ynfullslider', 'controller' => 'sliders', 'action'=>'preview-slider'), 'admin_default') ?>";
        ynfullsliderReloadSlider(currentParams, previewSliderUrl);
    }
</script>