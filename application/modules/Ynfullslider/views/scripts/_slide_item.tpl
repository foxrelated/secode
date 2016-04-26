<?php $slide = $this->slide ?>
<?php $slideId = $slide->getIdentity() ?>
<?php $params = $slide->getParams() ?>

<div>
    <div class="ynfullslider_manage_slides-thumb ynfullslider_manage-thumb" style="<?php echo $slide->getThumbnailStyle() ?>;">
        <?php if($params['background_option'] == 2): ?>
            <i class="fa fa-film"></i>
        <?php endif; ?>
        <div class="ynfullslider_manage_slides-checkbox ynfullslider_manage-checkbox">
            <input type="checkbox" class="ynfullslider_checkbox"  name='delete_<?php echo $slideId ?>' value='<?php echo $slideId ?>' >
        </div>
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slide', 'action' => 'editor', 'id' => $slideId),
            '', array('class'=>'ynfullslider_manage-link'))
        ?>
        <div class="ynfullslider_bg_disable"></div>
    </div>

    <div class="ynfullslider_manage_slides-infomation">

        <div class="ynfullslider_manage_slides-dragdrop" title="<?php echo $this->isSearch ? $this->translate('Reorder slides is not available while searching') : $this->translate('Drag and drop to re-order slides') ?>">
            <i class="fa fa-bars"></i>
        </div>

        <div class="ynfullslider_manage-title">
            <?php echo $slide->title ?>
        </div>

        <div class="ynfullslider_manage_slides-action-block ynfullslider_manage-action-block">
            <span title="<?php echo $this->translate('Press to do some actions on this slide') ?>" class="ynfullslider_manage_slides-action-btn ynfullslider_manage-action-btn"><i class="fa fa-pencil-square-o"></i></span>

            <ul class="ynfullslider_manage_slides-actions ynfullslider_manage-actions">
                <li>
                    <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slide', 'action' => 'general', 'id' => $slideId),
                    '<i class="fa fa-cog"></i>'.$this->translate("Setting"), array())
                    ?>
                </li>

                <li>
                    <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slide', 'action' => 'clone', 'id' => $slideId),
                '<i class="fa fa-files-o"></i>'.$this->translate("Clone"), array('class' => 'smoothbox'))
                    ?>
                </li>

                <li>
                    <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slides', 'action' => 'delete', 'id' => $slideId),
                    '<i class="fa fa-trash"></i>'.$this->translate("Delete"),
                    array('class' => 'smoothbox'))
                    ?>
                </li>
            </ul>
        </div>

        <div class="ynfullslider_manage-views" title="<?php echo($slide->show_slide ? $this->translate('Press to disable this slide in slider') : $this->translate('Press to enable this slide in slider')) ?>" onclick="ynfullsliderSwitchSlideShow(<?php echo $slideId ?>, this)">
            <i class="fa fa-eye"></i>
            <i class="fa fa-eye-slash ynfullslider_disable" style="display: none"></i>
        </div>
    </div>
</div>
