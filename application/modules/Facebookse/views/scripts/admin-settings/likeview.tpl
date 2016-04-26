<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likeview.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
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
<?php   $newStyleButtonUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.button.likeupdatefile', 1);
 if (empty($newStyleButtonUpdate)): ?>
<div class="tip">
  <span>
    <?php echo $this->translate('Note: If you want to minimize the size of Advanced Facebook Likes Plugin CSS, then please give write permission (chmod 777) to the file "/application/modules/Facebookse/externals/styles/likesettings.css".');?>
  </span>
</div>
<?php endif; ?>
<?php  $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');?>
<div class='seaocore_settings_form'>
  <div class='settings'>
   <?php if ($fbLikeButton == 'default'):?>
    <div class="tip"> 
			<span> 
				Form here you can customize the Custom Facebook Like Button. However you have enabled the Normal Facebook Like Button on your site. So, if you want to enable Custom Facebook Like Button then please go <a href="admin/facebookse/settings#fblike_type-wrapper">here</a>. 
			</span> 
		</div>
   <?php else: ?> 
    
    <?php
      //RENDEDR THE FORM		
      echo $this->form->render($this);
    ?>
    <?php endif;?>
  </div>
</div>

<script type="text/javascript">
  function updateTextFields(option) {
 
  if($('fbbutton_likeicon_preview-element')) {
    $('fbbutton_likeicon_preview-element').innerHTML = "<img src='" + option + "' width='13' height='13' >" ;
  }
}


function updateTextFields1(option1) {  
  if($('fbbutton_unlikeicon_preview-element')) {
    $('fbbutton_unlikeicon_preview-element').innerHTML = "<img src='" + option1 + "' width='13' height='13' >" ;
  }
}
  
</script>  