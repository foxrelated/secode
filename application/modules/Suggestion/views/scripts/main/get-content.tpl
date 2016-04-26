<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-content.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
<?php echo $this->modName . '_wid_content_count'; ?> = '<?php echo $this->widgetCount; ?>';
</script>

<?php
$is_recommended = $this->modName;
$modInfo = $this->modInfo;
if (!empty($this->widgetCount)) {
  echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array('modInfo' => $this->modArray, 'isAjax' => 1, 'div_id' => $this->div_id, 'is_recommended' => $is_recommended));
} else {
  if( !strstr($this->modName, 'explore') && empty( $this->is_middleLayoutEnabled ) ){
    echo "<div class='tip'><span> " . $this->translate("You do not have any more %s suggestions", strtolower($modInfo[$this->modName]['displayName'])) . "</span></div>";
  }else {
    echo '<div class="seaocore_tip">' . $this->translate("Sorry, no more suggestions.") . '</div>';
  }
}
?>
<script type="text/javascript">
 processFlagDiv = '<?php echo $this->div_id; ?>';
</script>