<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: index.tpl 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Pokes Plugin') ?>
</h2>
<script type="text/javascript">
var fetchLevelSettings =function(level_id){
  window.location.href= en4.core.baseUrl+'admin/poke/level/index/id/'+level_id;
  //alert(level_id);
}
window.addEvent('domready', function() {
	showoptions(<?php echo $this->send ?>);
});
function showoptions(options) {
	if(options == 1) 
		$('auth_view-wrapper').style.display='block';
	else 
		$('auth_view-wrapper').style.display='none';
}
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>