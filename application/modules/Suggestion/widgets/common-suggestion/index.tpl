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

/*
  If admin set default render widget "Ajax based " then call the "partial.tpl" file. If admin set default render widgets without "Ajax based" then we are calling templatePartial.tpl
 */

if (empty($this->isAjaxEnabled)) {
  echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array('modInfo' => $this->modArray, 'getWidLimit' => $this->getWidLimit));
} else {
  echo $this->partial('application/modules/Suggestion/widgets/partial.tpl', array('modInfo' => $this->modArray, 'loadFlage' => $this->loadFlage, 'getWidLimit' => $this->getWidLimit, 'mod_type' => $this->mod_type, 'getWidAjaxEnabled' => $this->isAjaxEnabled, 'resource_type' => $this->mod_type, 'widId' => $this->identity, 'ajaxRequest' => 1));
}

if (!empty($this->isAjaxEnabled)):
  $obj = $this->modArray;
  $getWidLimit = !empty($this->getWidLimit)? $this->getWidLimit: '';
  $mod_type = !empty($this->mod_type)? $this->mod_type: '';
  $getWidAjaxEnabled = $this->isAjaxEnabled;
  $exploreWidgetLimit = !empty($this->limit)? $this->limit: '';
  $getLayout = !empty($this->getLayout)? $this->getLayout: 0;
  ?>
  <script type="text/javascript">    
    window.addEvent('load', function() {
      setTimeout("showSuggestionContent('" + en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?> + "', '<?php echo $obj['init_div_id']; ?>', '<?php echo $getWidLimit;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $getWidAjaxEnabled;  ?>', '<?php echo $mod_type;  ?>', '<?php echo $exploreWidgetLimit; ?>', '<?php echo $getLayout; ?>')", 200);   
    });  
  </script>
<?php endif; ?>