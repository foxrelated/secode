<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<div class="layout_middle">
  <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>

  <div class="sitegroup_edit_content">
    <div class="sitegroup_edit_header">
      <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()),$this->translate('VIEW_GROUP')) ?>
      <h3><?php echo $this->translate('Dashboard: ') . $this->sitegroup->title; ?></h3>
    </div>
    <div id="show_tab_content">
      <?php if (!empty($this->success)): ?>
        <ul class="form-notices" >
          <li>
            <?php echo $this->translate($this->success); ?>
          </li>
        </ul>
      <?php endif; ?>
      <div class="sitegroup_overview_editor">
      	<?php echo $this->form->render($this); ?>
      </div>	
    </div>
  </div>
</div>
     </div>
</div>
<script type="text/javascript">
  window.addEvent('domready', function () {
		 
    if ($('body-label')) {
      var catdiv1 = $('body-label');
      var catarea1 = catdiv1.parentNode;
      catarea1.removeChild(catdiv1);
    }
    if ($('save-label')) {
      var catdiv2 = $('save-label');  	
      var catarea2 = catdiv2.parentNode;
      catarea2.removeChild(catdiv2);
    }
  });
		
</script>