<script type="text/javascript">
    jQuery.noConflict();
    function multiDelete(){
        if ($$('.ynfullslider_btn-delete')[0].hasClass('active'))
            return confirm("<?php echo $this->translate("Are you sure you want to delete the selected slides?") ?>");
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

    function ynfullsliderSwitchSlideShow(slideId, el) {
        var $el = jQuery(el).parents('#'+slideId+'.ynfullslider_manage_slides-item')[0];
        var show = $el.hasClass('ynfullslider_slide_disabled')? 1 : 0;

        var request = new Request.JSON({
            'format' : 'json',
            'url' : '<?php echo $this->url(Array('module'=>'ynfullslider', 'controller' => 'slides', 'action'=>'toggle-show-slide'), 'admin_default') ?>',
            'data': {
                'show' : show,
                'slide_id' : slideId
            },
            'onSuccess' : function(responseJSON) {
                jQuery(el).attr('title', show ? '<?php echo $this->translate("Press to disable this slide in slider") ?>' : '<?php echo $this->translate("Press to enable this slide in slider") ?>');
                return false;
            }
        });
        request.send();

        $el.toggleClass('ynfullslider_slide_disabled');
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
    <h2 class="ynfullslider_title">
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'index'),
            $this->translate("Manage Sliders"), array())
        ?>
        &nbsp;/&nbsp;
        <?php
        if ($this->slider->slider_id)
            echo $this->slider->getTitle();
        ?>
    </h2>
</div>

<?php $total_slide = $this->slider->getSlidecount() ?>

<div class="ynfullslider_manage_slides">
    <?php if($total_slide): ?>
        <div class="ynfullslider_manage_sliders-search-btn ynfullslider_manage-search-btn clearfix">
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slide', 'action' => 'general', 'slider_id' => $this->slider->slider_id),
                '<i class="fa fa-plus"></i>&nbsp;'.$this->translate("Add new slide"),
                array('class' => 'ynfullslider_btn ynfullslider_btn-add'))
            ?>

            <a class="ynfullslider_btn ynfullslider_btn-delete" href="javascript:deleteSelected();">
                <i class="fa fa-trash"></i>&nbsp;<?php echo $this->translate("Delete selected") ?>
            </a>

            <div class="ynfullslider_manage_sliders-search ynfullslider_manage-search">
                <?php echo $this->form->render($this) ?>
            </div>
        </div>

        <?php if(count($this->slides)): ?>
            <div class="ynfullslider_manage_sliders-select-all ynfullslider_manage-select-all">
                <input id="ynfullslider_checkall" onclick='selectAll()' type="checkbox">
                <label for="ynfullslider_checkall">
                    <span><?php echo $this->translate('Select all') ?>&nbsp;<span><?php echo $this->translate(array('(%1$s slide)', '(%1$s slides)', count($this->slides)), $this->locale()->toNumber(count($this->slides))) ?></span></span>
                </label>
            </div>

            <?php if($this->formParams['title']): ?>
                <div class="ynfullslider_manage_sliders-description-status">
                    <p><?php echo $this->translate("Reorder slides is not available while searching") ?>.</p>
                </div>
                <br/>
            <?php endif; ?>

            <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
                <ul id="ynfullslider_manage_slides" class="ynfullslider_manage_slides-items ynfullslider_manage-items clearfix">
                    <?php foreach($this->slides as $slide): ?>
                    <li id="<?php echo $slide->getIdentity() ?>" class="ynfullslider_manage_slides-item ynfullslider_manage-item <?php if(!$slide->show_slide) echo 'ynfullslider_slide_disabled' ?>">
                            <?php echo $this->partial('_slide_item.tpl', 'ynfullslider', array('slide'=>$slide, 'isSearch' => $this->formParams['title'])) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </form>

            <a class = "ynfullslider_btn ynfullslider_btn-delete" href="javascript:deleteSelected();">
                <i class="fa fa-trash"></i>&nbsp;<?php echo $this->translate("Delete selected") ?>
            </a>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate("No slides found") ?>
                </span>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="ynfullslider_empty_slider">

            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slide', 'action' => 'general', 'slider_id' => $this->slider->slider_id),
                '<i class="fa fa-plus"></i>&nbsp;'.$this->translate("Add a new slide"),
                array('class' => 'ynfullslider_btn ynfullslider_btn-add'))
            ?>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function(){
        var isSearch = "<?php echo $this->formParams['title'] ?>";
        if (!isSearch)
            jQuery('#ynfullslider_manage_slides').sortable({
                handle: 'div.ynfullslider_manage_slides-dragdrop',
                stop: reorderSlides
            });
    });

    jQuery(document).ready(function() {
        var isSearch = "<?php echo $this->formParams['title'] ?>";
        if (!isSearch)
        reorderSlides();
    });

    function reorderSlides(){
        var order = '';
        jQuery('.ynfullslider_manage_slides-item').each(function(){
            order += this.id + ',';
        });

        var request = new Request.JSON({
            'format' : 'json',
            'url' : '<?php echo $this->url(Array('module'=>'ynfullslider', 'controller' => 'slider', 'action'=>'update-slide-order'), 'admin_default') ?>',
            'data': {
                'order' : order
            },
            'onSuccess' : function(responseJSON) {
                return false;
            }
        });

        request.send();
    }

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