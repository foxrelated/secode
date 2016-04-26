<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admodule-create.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function setModuleName(module_name){
   window.location.href="<?php echo $this->url(array('module'=>'facebookse','controller'=>'manage', 'action'=>'module-create'),'admin_default',true)?>/module_name/"+module_name;
 }
 
 var show_hideform = function (thisobj) { 
    if (thisobj) {
      
    	thisobj.getParent('.form-wrapper').getAllNext(':not([id^=execute])').setStyle('display', ($('streampublishenable-1').checked == 1?'block':'none'));
    	
    	if ($('activityfeed_type').value == 0) { 
    	  $('activityfeed_type').getParent('.form-wrapper').getAllNext(':not([id^=execute])').setStyle('display', 'none');
    	}
    	$( 'cancel' ) .removeProperty ( 'style' );
    	
    	   	
    	
    }
}

var fetchtypeSettings = function (thisobj) {
  
  thisobj.getParent('.form-wrapper').getAllNext(':not([id^=execute])').setStyle('display', (thisobj.value == 0?'none':'block'));
  $( 'cancel' ) .removeProperty ( 'style' );
}

window.addEvent('domready', function () {  
	show_hideform ($('streampublishenable-1'));

});

var checkfeedvalidate = function () { 
  if ($('streampublishenable-1').checked == 1 && $('activityfeed_type').value == 0) {
    en4.core.showError('If you have selected "Yes" for Facebook Feed Publisher then you must need to select the "Activity Type" also.');
    return false;
    
  }
  else 
    return true;
  
}
</script>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph') ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'index'), $this->translate("Back to Manage Modules for Advanced Facebook Integration"), array('class'=>'sitelike_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />
<div class="seaocore_settings_form">
	<div class='settings'>
		<?php		echo $this->form->render($this); ?>
	</div>
</div>
<style type="text/css">
.sitelike_icon_back{
	background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/back.png);
}
</style>