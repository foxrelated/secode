<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>

<div class="layout_left ynfundraising_create_right_menu ">
<?php
$menu_create = $this->partial('_menu_create.tpl', array('active_menu'=>'step07','campaign_id'=>$this->campaign_id));
echo $menu_create;

?>
</div>
<div class='layout_middle'>
<form class="global_form_popup" method="POST">
	<h3><?php echo $this->translate("Publish Campaign")?></h3>
	<div style="padding-bottom: 15px; padding-top: 15px">
		<?php echo $this->translate("YNFUNDRAISING_PUBLISH_STEP_SEVEN");?>
	</div>
	<button name="submit"><?php echo $this->translate("Publish")?></button> <span style="padding-top: 5px;"> <?php echo $this->translate("or ")?></span>
    <a href="<?php echo $this->url(array('controller'=>'campaign'),"ynfundraising_extended")?>"> <?php echo $this->translate("cancel")?></a>
    </form>
</div>