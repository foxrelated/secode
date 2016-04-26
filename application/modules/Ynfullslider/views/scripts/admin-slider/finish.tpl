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

<div class="ynfullslider_global_content">
    <div class="ynfullslider_form_finish">
        <p>
            <?php echo $this->translate("You have finished creating the basic layout of slider. This layout have been saved.") ?><br/>
            <?php echo $this->translate("And you can edit this slider anytime you want.") ?><br/>
            <?php echo $this->translate("However, don’t forget to add some slides to it before publishing.") ?>
        </p>
        
        <div class="ynfullslider_form_finish-button">
            <?php echo $this->htmlLink(array(
                    'route'=>'admin_default',
                    'module'=>'ynfullslider',
                    'controller'=>'slider',
                    'action'=>'general',
                    'id'=>$this->slider->getIdentity()
                ),'<i class="fa fa-pencil"></i>'.$this->translate("Edit Slider"),
                array('class' => '')
            ) ?>

            <?php echo $this->htmlLink(array(
                    'route'=>'admin_default',
                    'module'=>'ynfullslider',
                    'controller'=>'slide',
                    'action'=>'general',
                    'slider_id'=>$this->slider->getIdentity()
                ),'<i class="fa fa-plus"></i>'.$this->translate("Add Slide"),
                array('class' => '')
            ) ?>
        </div>
    </div>
</div>
