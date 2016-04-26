<?php
$slider = $this->slider;
$sliderId = $slider->getIdentity();
$state = $slider->getCurrentState();
?>
<div class="ynfullslider_manage_sliders-thumb ynfullslider_manage-thumb <?php echo $state ?>" style="<?php echo $slider->getThumbnailStyle() ?>;">
    <?php if($state) :?>
    <span class="ynfullslider_slider_status"><span><?php echo $state ?></span></span>
    <?php endif; ?>
    <div class="ynfullslider_manage_sliders-checkbox ynfullslider_manage-checkbox">
        <input type="checkbox" class="ynfullslider_checkbox"  name='delete_<?php echo $sliderId ?>' value='<?php echo $sliderId ?>' >
    </div>

    <?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'manage-slides', 'id' => $sliderId),
    '', array('class'=>'ynfullslider_manage-link'))
    ?>

    <div class="ynfullslider_manage_sliders-title-count">

        <div class="ynfullslider_manage_sliders-title">
            <?php echo $slider->title ?>
        </div>

        <div class="ynfullslider_manage_sliders-count">
            <?php echo $this->translate(array('%1$s slide', '%1$s slides', $slider->getSlidecount()), $this->locale()->toNumber($slider->getSlidecount())) ?>
        </div>
    </div>

    <div class="ynfullslider_manage_sliders-action-block ynfullslider_manage-action-block">
        <span title="<?php echo $this->translate('Press to do some actions on this slider') ?>" class="ynfullslider_manage_sliders-action-btn ynfullslider_manage-action-btn"><i class="fa fa-pencil"></i></span>

        <ul class="ynfullslider_manage_sliders-actions ynfullslider_manage-actions">
            <li>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'general', 'id' => $sliderId),
                    '<i class="fa fa-cog"></i>'.$this->translate("Setting"), array())
                ?>
            </li>

            <li>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'manage-slides', 'id' => $sliderId),
                    '<i class="fa fa-th-large"></i>'.$this->translate("Manage Slides"), array())
                ?>
            </li>

            <li>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'clone', 'id' => $sliderId),
                    '<i class="fa fa-files-o"></i>'.$this->translate("Clone"), array('class' => 'smoothbox'))
                ?>
            </li>

            <li>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'delete', 'id' => $sliderId),
                    '<i class="fa fa-trash"></i>'.$this->translate("Delete"),
                    array('class' => 'smoothbox')) 
                ?>
            </li>
        </ul>
    </div>

</div>