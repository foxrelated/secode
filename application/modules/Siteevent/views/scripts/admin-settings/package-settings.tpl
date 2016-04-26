<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    showUiOption();
  });
  
  function showUiOption() 
  {
    if($('siteevent_package_view-wrapper')) {
      if($('siteevent_package_setting-1').checked) { 
        $('siteevent_package_view-wrapper').style.display='block';	
      }
      else{
        $('siteevent_package_view-wrapper').style.display='none';
      }		
    }
    if($('siteevent_package_description-wrapper')) {
      if($('siteevent_package_setting-1').checked) { 
        $('siteevent_package_description-wrapper').style.display='block';	
      }
      else{
        $('siteevent_package_description-wrapper').style.display='none';
      }		
    }  
    if($('siteevent_package_information-wrapper')) {
      if($('siteevent_package_setting-1').checked) { 
        $('siteevent_package_information-wrapper').style.display='block';	
      }
      else{
        $('siteevent_package_information-wrapper').style.display='none';
      }		
    }  
  }
  </script>
<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php
        echo $this->form->render($this);
        ?>
    </div>
</div>

