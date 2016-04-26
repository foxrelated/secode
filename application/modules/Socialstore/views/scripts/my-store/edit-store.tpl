<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_middle">
<?php echo $this->form->render($this);
$this->headScript()
    ->appendFile($this->baseUrl().'/application/modules/Socialstore/externals/scripts/core.js');?>
</div>



<script type="text/javascript">
function removeSubmit(){
   $('execute').hide(); 
}

//$(document).addEvent('domready',function(){initMap(true)});
</script>
