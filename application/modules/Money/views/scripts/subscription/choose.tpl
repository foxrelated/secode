<?php ?>
<script type="text/javascript">
    function commission(value){
        var total;
        var commission = <?php echo $this->commission ?>;
        var count = ((value * commission)/100).toFixed(2) ;
            
        total = parseFloat(value) + parseFloat(count);
        if(!document.getElementById("commission_money")){
            $container = new Element('div', {'id': 'commission_money', 'html': '<?php echo $this->translate('Total to pay ') ?>'+total});
            $parent_container = $('amount');
            $container.inject($parent_container, 'after');
        }
        else{
            document.getElementById("commission_money").innerHTML = '<?php echo $this->translate('Total to pay ') ?>'+total;
        } 
    }
    
    var updateRadioButton = function(elm)
    {
        var amount_element = document.getElementById("plan-0");
        amount_element.getParent().style.display = "none";
        

        if (elm.value == 3)
        {
            amount_element.getParent().style.display = "none";
            return;
        }
        else{
            amount_element.getParent().style.display = "block";
            return;
        }
    }

    en4.core.runonce.add(updateRadioButton);
        
 
    var updateTextFields = function(plan)
    {
        var amount_element = document.getElementById("amount-wrapper");
        amount_element.style.display = "none";

        if (plan.value == 0)
        {
            amount_element.style.display = "block";
            return;
        }
        else{
            amount_element.style.display = "none";
            return;
        }
    }

    en4.core.runonce.add(updateTextFields);
      
</script> 
<?php ?>
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


<?php echo $this->form->render($this); ?>

