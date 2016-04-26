<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<div class='clear'>
<div class = "settings">
<?php echo $this->form->render();?>
</div>

</div>

<script type="text/javascript">
function removeSubmit(){
   $('execute').hide(); 
}

//$(document).addEvent('domready',function(){initMap(true)});
</script>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
</style>