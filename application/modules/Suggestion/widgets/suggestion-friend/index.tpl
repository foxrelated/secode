<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var processFlag = 0;
  var processFlagDiv = '';
</script>

<?php


if( !empty($this->is_welcomeTab_enabled) ) {
	
	?>
	
	<div class="adv_activity_welcome">
		<div class="adv_activity_welcome_num">
		</div>
		<div class="adv_activity_welcome_cont">	
			<div class="adv_activity_welcome_cont_title">
			  <?php
			    $url = $this->url(array(), 'friends_suggestions_viewall', true);
			    echo '<a href="'.$url.'">' . $this->translate('Add People You Know') . '</a>';
			  ?>
			</div>
			<div class="adv_activity_welcome_cont_des">
	
	<?php
}

/*
  If admin set default render widget "Ajax based " then call the "partial.tpl" file. If admin set default render widgets without "Ajax based" then we are calling templatePartial.tpl
 */

if (empty($this->isAjaxEnabled)) {
  echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array('modInfo' => $this->modArray, 'showAllFlag' => 'friend', 'is_activityPlugin' => $this->is_pluginEnabled, 'is_welcomeTab_enabled' => $this->is_welcomeTab_enabled, 'getLayout' => $this->getLayout));
} else {
  echo $this->partial('application/modules/Suggestion/widgets/partial.tpl', array('modInfo' => $this->modArray, 'loadFlage' => $this->loadFlage, 'showAllFlag' => 'friend', 'getWidLimit' => $this->getWidLimit, 'getLayout' => $this->getLayout, 'widId' => $this->identity, 'getWidAjaxEnabled' => $this->isAjaxEnabled, 'ajaxRequest' => 1));
}
?>

<?php	if( !empty($this->is_welcomeTab_enabled) ) { ?>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (!empty($this->isAjaxEnabled)):
  $obj = $this->modArray;
  $getWidLimit = !empty($this->getWidLimit)? $this->getWidLimit: '';
  $mod_type = !empty($this->mod_type)? $this->mod_type: '';
  $getWidAjaxEnabled = $this->isAjaxEnabled;
  $exploreWidgetLimit = !empty($this->limit)? $this->limit: '';
  $getLayout = !empty($this->getLayout)? $this->getLayout: 0;
  ?>
  <script type="text/javascript">  
    window.addEvent('load', function() {
      setTimeout("showSuggestionContent('" + en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?> + "', 'suggestion_friend_widgets', '<?php echo $getWidLimit;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $getWidAjaxEnabled;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $exploreWidgetLimit; ?>', '<?php echo $getLayout; ?>')", 500);  
    });  
  </script>
<?php endif; ?>