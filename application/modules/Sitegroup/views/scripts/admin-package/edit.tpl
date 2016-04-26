<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( !empty($this->siteStoreEnable) ): ?>
<script type="text/javascript">
    window.addEvent('domready', function() {
    showComissionType();
    if(document.getElementById('modules-sitestore')){
      storeEnable();
      document.getElementById('modules-sitestore').addEvent('click',function() {
        storeEnable();
      });
    }
	});   
  
  function storeEnable(){
    if (document.getElementById('modules-sitestore').checked == true) {
          document.getElementById('max_product-wrapper').style.display = 'block';
          document.getElementById('comission_handling-wrapper').style.display = 'block';
          document.getElementById('transfer_threshold-wrapper').style.display = 'block';
          showComissionType();
        } else {
          document.getElementById('max_product-wrapper').style.display = 'none';
          document.getElementById('comission_handling-wrapper').style.display = 'none';
          document.getElementById('comission_rate-wrapper').style.display = 'none';
          document.getElementById('comission_fee-wrapper').style.display = 'none';
          document.getElementById('transfer_threshold-wrapper').style.display = 'none';
        } 
  }
  function showComissionType(){
    if(document.getElementById('comission_handling')){
          if(document.getElementById('comission_handling').value == 1) {
            document.getElementById('comission_fee-wrapper').style.display = 'none';
            document.getElementById('comission_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('comission_fee-wrapper').style.display = 'block';
            document.getElementById('comission_rate-wrapper').style.display = 'none';
          }
        }
  }
  
  
</script>
<?php endif; ?>
<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)) { ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php } ?>

<div class="sitegroup_pakage_form">
	<div class="settings">
	  <?php echo $this->form->render($this) ?>
	</div>
</div>	

<script type="text/javascript">
  function setRenewBefore(){

    if($('duration-select').value=="forever"|| $('duration-select').value=="lifetime" || ($('recurrence-select').value!=="forever" && $('recurrence-select').value!=="lifetime")){
      $('renew-wrapper').setStyle('display', 'none');
      $('renew_before-wrapper').setStyle('display', 'none');
    }else{
      $('renew-wrapper').setStyle('display', 'block');
      if($('renew').checked)
        $('renew_before-wrapper').setStyle('display', 'block');
      else
        $('renew_before-wrapper').setStyle('display', 'none');
    }
  }
  $('duration-select').addEvent('change', function(){
    setRenewBefore();
  });
  window.addEvent('domready', function() {
    setRenewBefore();
  });
</script>
<style type="text/css">
    
    #modules-sitegroupmember {
        display:none;
    } 
    
    label[for="modules-sitegroupmember"] { display:none; }
</style>