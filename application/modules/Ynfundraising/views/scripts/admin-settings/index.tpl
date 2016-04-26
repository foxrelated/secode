<?php
echo $this->partial('_menu_admin.tpl', array('tab_select' => 'ynfundraising_admin_main_settings'));

?>
<div class='settings'>
	<?php echo $this -> form -> render($this);?>
</div>