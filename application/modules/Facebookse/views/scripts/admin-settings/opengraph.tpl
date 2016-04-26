<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: opengraph.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<style type="text/css">
.fbconnect_ogs_userthumb{
	margin-left:195px;
	margin-top:10px;
}
.tip{  
  position: relative;
}
#fieldset-group .form-wrapper{
	border:none;
	padding:0px;
}
</style>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if($this->showTip): ?>

<div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img alt="Notice" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png">
		</div>

		<div class="seaocore-notice-text ">
			<?php echo $this->translate("You need to apply a minor change to the HeadMeta View Helper file as described below. This is required to enable your site to support new Open Graph Meta tags. In Global Settings, if you have chosen the 'Yes, automatically...' option, and still the modification does not happen, then it would be because of file permission not being available, and so the change would need to be done manually. :<br />OPEN the file: application/libraries/Zend/View/Helper/HeadMeta.php<br /><br />FIND (around line 42):<br /><div class='code'>protected $_typeKeys = array('name', 'http-equiv');</div><br /><br />REPLACE this with:<br /><div class='code'>protected $_typeKeys     = array('name', 'http-equiv', 'property');</div><br />");?>		</div>	
	</div>

<?php endif;?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>

<script type="text/javascript">
//<![CDATA[
var fetchLevelSettings =function(pagelevel_id) {
	if (pagelevel_id != 0) {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/opengraph/'+pagelevel_id;
	}
	else {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/opengraph/';
	}
    
  }

window.addEvent('domready', function() {
  var enable = '<?php echo $this->enable;?>';
  var showTip = '<?php echo $this->showTip;?>';
   if (enable != 1 && $('opengraph_enable-0')) {
    $('opengraph_enable-0').getParent('.form-wrapper').getAllNext().setStyle('display','none');
    $('submit-wrapper').setStyle('display', 'block');
   }

    $$('input[type=radio]:([name=opengraph_enable])').addEvent('click', function(e){ 
      if ($(this).id == 'opengraph_enable-0' || $(this).id == 'opengraph_enable-1') {
        $(this).getParent('.form-wrapper').getAllNext().setStyle('display', ($(this).get('value')>0?'block':'none'));
        $('submit-wrapper').setStyle('display', 'block');
      }
    });
    
    if (showTip == 1) {
      $('pagelevel_id').disabled = true;
      $('submit').disabled = true;
    }
    
//    var disable_contentadmin = '<?php echo $this->enable_contentcommenttype;?>';
//    if (disable_contentadmin != 0) {
//      $('fbadmin_appid-1').checked = false;
//      $('fbadmin_appid-1').disabled = 'disabled';
//      $('fbadmin_appid-0').checked = false;
//      $('fbadmin_appid-0').disabled = 'disabled';
//    }

  });

//]]>
</script>