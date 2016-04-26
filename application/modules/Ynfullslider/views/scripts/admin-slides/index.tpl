<script type="text/javascript">
    function multiDelete(){
        if ($$('.ynfullslider_btn-delete')[0].hasClass('active'))
            return confirm("<?php echo $this->translate("Are you sure you want to delete the selected sliders?") ?>");
        else
            return false;
    }

    function selectAll(){
        var i;
        var multidelete_form = $('multidelete_form');
        var checkAll = $('ynfullslider_checkall');
        var inputs = multidelete_form.elements;
        for (i = 0; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = checkAll.checked;
            }
        }
        if (checkAll.checked)
            $$('.ynfullslider_btn-delete').addClass('active');
        else
            $$('.ynfullslider_btn-delete').removeClass('active');
    }

    function deleteSelected(){
        if (multiDelete()){
            $('multidelete_form').submit();
        }
    }
</script>

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
    <h2 class="ynfullslider_title"><?php echo $this->translate("Manage sliders") ?></h2>
    <div class="ynfullslider_manage_sliders-search-btn ynfullslider_manage-search-btn clearfix">
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'general'),
            '<i class="fa fa-plus"></i>&nbsp;'.$this->translate("Add new slider"),
            array('class' => 'ynfullslider_btn ynfullslider_btn-add'))
        ?>

        <a class="ynfullslider_btn ynfullslider_btn-delete" href="javascript:deleteSelected();">
            <i class="fa fa-trash"></i>&nbsp;<?php echo $this->translate("Delete selected") ?>
        </a>

        <div class="ynfullslider_manage_sliders-search ynfullslider_manage-search">
            <?php echo $this->form->render($this) ?>
        </div>
    </div>

    <div class="ynfullslider_manage_sliders-select-all ynfullslider_manage-select-all">
        <input id="ynfullslider_checkall" onclick='selectAll()' type="checkbox">
        <label for="ynfullslider_checkall"><span><?php echo $this->translate('Select all sliders') ?></span></label>
    </div>

    <?php if( count($this->paginator) ): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
        <ul class="ynfullslider_manage_sliders-items ynfullslider_manage-items clearfix">
            <?php foreach( $this->paginator as $slider): ?>
            <li class="ynfullslider_manage_sliders-item ynfullslider_manage-item">
                <?php echo $this->partial('_slider_item.tpl', 'ynfullslider', array('slider' => $slider)) ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </form>
    
    <a class = "ynfullslider_btn ynfullslider_btn-delete" href="javascript:deleteSelected();">
        <i class="fa fa-trash"></i>&nbsp;<?php echo $this->translate("Delete selected") ?>
    </a>

    <div class="ynfullslider_pagigation">
        <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        )); ?>
    </div>
    <?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no sliders created yet.") ?>
        </span>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $$('.ynfullslider_manage-action-btn').removeEvents('click').addEvent('click',function(){
        var parent = this.getParent('.ynfullslider_manage-action-block');
        var popup = parent.getElement('.ynfullslider_manage-actions');

        $$('.ynfullslider_manage-actions').each(function(el) {
            if (el != popup) el.hide();
        });       
         $$('.ynfullslider_manage-action-block').each(function(el) {
            if (el != parent) el.removeClass('open');
        });

        popup.toggle();
        parent.toggleClass('open');
    });

    $$('.ynfullslider_checkbox').removeEvents('click').addEvent('click', function(){
        var checkedItems = $$('.ynfullslider_checkbox:checked');
        var numberItems = $$('.ynfullslider_checkbox');
        if (checkedItems.length) {
            $$('.ynfullslider_btn-delete').addClass('active');
            $('ynfullslider_checkall').checked = checkedItems.length == numberItems.length;
        }
        else {
            $$('.ynfullslider_btn-delete').removeClass('active');
            $('ynfullslider_checkall').checked = false;
        }
    });
</script>