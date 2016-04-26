<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partial.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
$this->headTranslate(array('Please select at-least one entry above to send suggestion to.', 'Search Members', 'Selected', 'No more suggestions are available.', 'Sorry, no more suggestions.'));
$obj = $this->modInfo;
$recommendedStartFlag = $this->recommendedStartFlag;
$recommendedEndFlag = $this->recommendedEndFlag;
$showAllFlag = $this->showAllFlag;

$exploreWidgetLimit = !empty($this->exploreWidgetLimit)? $this->exploreWidgetLimit: null;
$getWidAjaxEnabled = !empty($this->getWidAjaxEnabled)? $this->getWidAjaxEnabled: null;
$mod_type = !empty($this->mod_type)? $this->mod_type: null;
$resource_type = !empty($this->resource_type)? $this->resource_type: null;
$getWidLimit = !empty($this->getWidLimit)? $this->getWidLimit: null;
$widId = !empty($this->widId)? $this->widId: null;
$getLayout = !empty($this->getLayout)? $this->getLayout: 0;
$ajaxRequest = !empty($this->ajaxRequest)? true: false;
if(empty($ajaxRequest)):
?>
<script type="text/javascript">
  window.addEvent('load', function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $widId) ?>; // en4.core.staticBaseUrl + 'widget/index/mod/suggestion/name/' + '<?php // echo $obj['widget_name']; ?>';    
    var init_div_id = '<?php echo $obj['init_div_id']; ?>';
    var getWidLimit = '<?php echo $getWidLimit;  ?>';
    var resource_type = '<?php echo $mod_type;  ?>';
    var getWidAjaxEnabled = '<?php echo $getWidAjaxEnabled;  ?>';
    var mod_type = '<?php echo $mod_type;  ?>';
    var exploreWidgetLimit = '<?php echo $exploreWidgetLimit; ?>';
    var getLayout = '<?php echo $getLayout; ?>';

    var request = new Request.HTML({
      url : url,
      method: 'get',
      data : {
        format : 'html',
        'loadFlage' : 1,
        'itemCountPerPage': exploreWidgetLimit,
	'resource_type': resource_type,
	'getWidAjaxEnabled': getWidAjaxEnabled,
	'getWidLimit': getWidLimit,
	'mod_type': mod_type,
 	'getLayout': getLayout
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $(init_div_id).className = '';
        $(init_div_id).innerHTML = responseHTML;
        if( $(init_div_id + '_myContent') ) {
          var friendPhotocontainer = $(init_div_id + '_myContent').innerHTML;
          $(init_div_id).innerHTML = friendPhotocontainer;
        }
      }
    });
    request.send();
  });
</script>
  <?php
endif;
if (empty($this->loadFlage)) {
 ?>
  <!-- 	$exploreWidgetLimit: This variable return limit only in the case of explore suggestion because in this widgets, we are take limit from "core_content" table. -->
  <div id="<?php echo $obj['init_div_id']; ?>" class="<?php if (empty($exploreWidgetLimit)) {
    echo 'generic_suggestion_widget';
  } ?>">
		<div class="loader_text"> <?php echo $this->translate('Loading...'); ?> </div>
  </div>
<?php }
?> 
  <?php if (empty($recommendedStartFlag)) {
 ?>
  <div id="<?php echo $obj['init_div_id']; ?>_myContent">
  <?php } ?>
  <?php
  if (!empty($this->loadFlage)) {
  ?>
  <?php
    // Call the template partial.
    echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array('modInfo' => $obj, 'recommendedStartFlag' => $recommendedStartFlag, 'recommendedEndFlag' => $recommendedEndFlag, 'showAllFlag' => $showAllFlag, 'getWidLimit' => $getWidLimit));
  }
  ?>
<?php if ($recommendedStartFlag == $recommendedEndFlag) {
 ?>
    </div>
<?php } ?>

