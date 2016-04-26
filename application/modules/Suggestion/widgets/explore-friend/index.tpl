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
if( empty($this->noSuggestionAvailable) ){
if( !empty($this->is_welcomeTab_enabled) ){
	
	?>
	
	<div class="adv_activity_welcome">
		<div class="adv_activity_welcome_num">
		</div>
		<div class="adv_activity_welcome_cont">	
			<div class="adv_activity_welcome_cont_title">
				<?php 
				  $url = $this->url(array(), 'sugg_explore_friend', true);
				  echo '<a href="'.$url.'">' . $this->translate('Explore Suggestions') . '</a>';
				?>
			</div>
			<div class="adv_activity_welcome_cont_des">
	
	<?php 
	
}

/*
  If admin set default render widget "Ajax based " then call the "partial.tpl" file. If admin set default render widgets without "Ajax based" then we are calling templatePartial.tpl
 */

$recommendedFlag = 0;
if (empty($this->isAjaxEnabled)) { // If Widgets is not ajax based.
    $recommendedEndFlag = @COUNT($this->modArray) - 1;
    if( !empty($this->is_welcomeTab_enabled) && empty($recommendedEndFlag) ){ $recommendedEndFlag = 1; }
  foreach ($this->modArray as $modArray) {
    echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array('modInfo' => $modArray, 'recommendedStartFlag' => $recommendedFlag, 'recommendedEndFlag' => $recommendedEndFlag, 'is_welcomeTab_enabled' => $this->is_welcomeTab_enabled));
    $recommendedFlag++;
  }
} else { // If Widgets is ajax based.
  if (!empty($this->loadFlage)) {// In Ajaxed based when code run second time( When exicution with content ).
    $recommendedEndFlag = @COUNT($this->modArray) - 1;
    foreach ($this->modArray as $modArray) {
      $modArray = array_merge($modArray, $this->ModInfoArray);
      echo $this->partial('application/modules/Suggestion/widgets/partial.tpl', array('modInfo' => $modArray, 'recommendedStartFlag' => $recommendedFlag, 'recommendedEndFlag' => $recommendedEndFlag, 'loadFlage' => $this->loadFlage, 'exploreWidgetLimit' => $this->limit, 'widId' => $this->identity, 'ajaxRequest' => 1));
      $recommendedFlag++;
    }
  } else {// In Ajaxed based when code run first time( When exicution without content ).
    echo $this->partial('application/modules/Suggestion/widgets/partial.tpl', array('modInfo' => $this->ModInfoArray, 'loadFlage' => $this->loadFlage, 'exploreWidgetLimit' => $this->limit, 'widId' => $this->identity, 'ajaxRequest' => 1, 'ajaxRequest' => 1));
  }
}
?>

<?php	if( !empty($this->is_welcomeTab_enabled) ) { ?>
			</div>
		</div>
	</div>
<?php } 
}else{
    ?>
<div class="tip">
    <span>
        <?php echo $this->translate("You do not have any suggestions."); ?>
    </span>
</div>
        <?php
}
?>


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
      setTimeout("showSuggestionContent('" + en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?> + "', 'suggestion_explore_widgets', '<?php echo $getWidLimit;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $getWidAjaxEnabled;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $exploreWidgetLimit; ?>', '<?php echo $getLayout; ?>')", 400);  
    });  
  </script>
<?php endif; ?>