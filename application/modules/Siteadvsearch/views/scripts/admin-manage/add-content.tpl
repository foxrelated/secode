<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-content.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo "Advanced Search  Plugin"; ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div>
  <?php echo $this->htmlLink(array("route" => "admin_default","module" => "siteadvsearch","controller" => "manage"), 'Back to Manage Modules', array('class' => 'icon_siteadvserach_admin_back buttonlink')) ?>
</div>
<br />

<div class="seaocore_settings_form">
	<div class='settings'>
	  <?php echo $this->form->render($this); ?>
	</div>
</div>	

<script type="text/javascript">
  function setModuleName(module_name){
   window.location.href="<?php echo $this->url(array('module'=>'siteadvsearch','controller'=>'manage', 'action'=>'add-content'),'admin_default',true)?>/module_name/"+module_name;
 }
</script>