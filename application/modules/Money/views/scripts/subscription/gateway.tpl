<?php ?>
<script type="text/javascript">

       
        var updateField = window.updateField = function(){
            var file = document.getElementById("cart-wrapper");
            var gateway = document.getElementById("gateway");
            
            
            
            if(gateway.value == 1){
                file.style.display = "block";
            }
            else{
                file.style.display = "none";
            }
        }
 
</script>    
<?php echo $this->form->render($this); ?>
  <script type="text/javascript">
	if($('cart-wrapper'))
   $('cart-wrapper').style.display = 'none';
</script>
