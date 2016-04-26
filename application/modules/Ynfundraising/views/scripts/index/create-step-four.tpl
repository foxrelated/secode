<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>

<div class="layout_left ynfundraising_create_right_menu ">
<?php 
$menu_create = $this->partial('_menu_create.tpl', array('active_menu'=>'step04','campaign_id'=>$this->campaign_id));  
echo $menu_create;

?>
</div>

<div class="layout_middle"> 
<?php echo $this->form->render($this);?> 
</div>