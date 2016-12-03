<?php ?>
<script type="text/javascript">
    function commission(value){
        var total;
        var commission = <?php echo $this->commission ?>;
        var count = ((value * commission)/100).toFixed(2) ;
            
        total = parseFloat(value) - parseFloat(count);
        if(!document.getElementById("commission_money")){
            $container = new Element('div', {'id': 'commission_money', 'html': '<?php echo $this->translate('Total found ') ?>'+total});
            $parent_container = $('purse');
            $container.inject($parent_container, 'after');
        }
        else{
            document.getElementById("commission_money").innerHTML = '<?php echo $this->translate('Total found ') ?>'+total;
        }
            
    }
    function updateLabel(elm){
        var label = document.getElementById("purse-label");
        if(elm.value == 1){
            label.innerHTML = 'E-mail';
        }
        else if(elm.value == 2){
            label.innerHTML = 'Purse';
        }
    }
    
</script>    
<div class="headline">
    <h2>
        <?php echo $this->translate('Money'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<?php
echo $this->form->render($this)?>