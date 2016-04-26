<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
 	var sitemailtemplates = '<?php echo $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>';
  window.addEvent('domready', function() { 
    var e1 = $('sitegroup_insightemail-1');
    var e2 = $('sitegroup_demo');
    $('sitegroup_insightmail_options-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    if(sitemailtemplates == 0) {
			$('sitegroup_header_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitegroup_bg_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitegroup_title_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitegroup_site_title-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    }
    $('sitegroup_demo-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    $('sitegroup_admin-wrapper').setStyle('display', (e2.checked && e1.checked ?'block':'none'));
 
 
 	  
    $('sitegroup_insightemail-0').addEvent('click', function(){
      $('sitegroup_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      if(sitemailtemplates == 0) {
				$('sitegroup_header_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitegroup_bg_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitegroup_title_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitegroup_site_title-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      }
      $('sitegroup_demo-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      $('sitegroup_admin-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
    });
 
    $('sitegroup_insightemail-1').addEvent('click', function(){
      $('sitegroup_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      if(sitemailtemplates == 0) {
				$('sitegroup_header_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitegroup_bg_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitegroup_title_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitegroup_site_title-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      }
      $('sitegroup_demo-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      $('sitegroup_admin-wrapper').setStyle('display', (e2.checked && $(this).checked ?'block':'none'));
    });
       
    $('sitegroup_demo').addEvent('click', function(){
      $('sitegroup_admin-wrapper').setStyle('display', ($(this).checked && e1.checked ?'block':'none'));
    });
  });
</script>

<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>