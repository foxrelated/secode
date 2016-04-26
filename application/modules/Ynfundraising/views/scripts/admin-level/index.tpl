<?php
echo $this->partial('_menu_admin.tpl', array('tab_select' => 'ynfundraising_admin_main_level'));
?>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/ynfundraising/level/index/id/'+level_id;
    //alert(level_id);
  }
</script>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>