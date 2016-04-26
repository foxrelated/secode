<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _Subcategory.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php 
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$module = $request->getModuleName();
	$action = $request->getActionName();
?>

<?php if($module == 'list' && ($action == 'home' || $action == 'manage' || $action == 'index')):?>
<?php
echo "
	<li id='subcategory_id-label' style='display:none;' > 
		<span >" . $this->translate('Subcategory') . "</span>
		<select name='subcategory_id' id='subcategory_id' onchange='subcate(this.value,0);' ></select>
	</li>";
?>

<?php
echo "
	<li id='subsubcategory_id-label' style='display:none;'>
		<span >" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</span>
		<select name='subsubcategory_id' id='subsubcategory_id' onchange='changesubsubcategory(this.value);'></select>
	</li>";
?>
<?php else:?>
<?php echo "
		<div id='subcategory_id-label' class='form-wrapper' style='display:none;'>
			<div class='form-label'><label>" . $this->translate('Subcategory', "<sup>rd</sup>") . "</label></div>
			<div class='form-element'>
				<select name='subcategory_id' id='subcategory_id' onchange='subcate(this.value,0);'>
      </select>
      </div>
		</div>";
?>
<?php echo "
		<div id='subsubcategory_id-label' class='form-wrapper' style='display:none;'>
			<div class='form-label'><label>" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</label></div>
			<div class='form-element'>
				<select name='subsubcategory_id' id='subsubcategory_id' onchange='changesubsubcategory(this.value,0);'>
				</select>
      </div>
		</div>";
?>
<?php endif;?>

<script type="text/javascript">
  function subcate(subcate, subsubcate) {
  	$('subcategory').value = subcate;
    changesubcategory(subcate, subsubcate);
  }

  function changesubsubcategory(subsubcate) {
    if($('subsubcategory'))
  	$('subsubcategory').value = subsubcate;
   }

  if($('subcategory_id'))
    $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
    $('subcategory_id-label').style.display = 'none';
    if($('subsubcategory_id'))
    $('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
    $('subsubcategory_id-label').style.display = 'none';
</script>