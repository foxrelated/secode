<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitestoreproduct_specification_sitestoreproduct')
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>

<?php if($this->showContent): ?>
	<div class='sr_sitestoreproduct_pro_specs'>
		<?php if(!empty($this->otherDetails)): ?>
			<?php echo Engine_Api::_()->sitestoreproduct()->removeMapLink($this->fieldValueLoop($this->sitestoreproduct, $this->fieldStructure)) ?>
	  <?php else: ?>
	    <div class="tip">
        <span ><?php echo$this->translate("There no any information.");  ?></span>
	    </div>
		<?php endif; ?>
	</div>
<?php endif; ?>