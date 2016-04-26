<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<div class="layout_left ynfundraising_create_right_menu">
<?php 
	$menu_mini = $this->partial('_menu_create.tpl', array('active_menu'=>'step02','campaign_id'=>$this->campaign_id));  
	echo $menu_mini;
?>
</div>

<?php echo $this->form->render($this) ?>
 <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
      var addMoreFile = window.addMoreFile = function () 
      {
        var fileElement = new Element('input', {
          'type': 'file',
          'name': 'photos[]',
          'multiple': "multiple"
        });
        fileElement.inject($('photos-element'));
      }
    });
    // -->
  </script>