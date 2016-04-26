<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: updatepackage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.view', 1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
?>
<?php $sitecouponEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon'); ?>
<?php if (!empty($sitecouponEnabled)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitecoupon/views/scripts/getcode.tpl'; ?>
<?php endif;?>

<?php if (empty($this->is_ajax)) : ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

 <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
  <div class="sitegroup_edit_content">
    <div class="sitegroup_edit_header">
	   <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()),$this->translate('VIEW_GROUP')) ?>
	    <h3><?php echo $this->translate('Dashboard: ').$this->sitegroup->title; ?></h3>
    </div>

<div id="show_tab_content">
<?php endif; ?> 

    <div class="sitegroup_package_group">
      
      <ul class="sitegroup_package_list">        
        <li>
         
          <div class="sitegroup_package_list_title">
            <div class="sitegroup_create_link">
              <?php if (Engine_Api::_()->sitegroup()->canShowPaymentLink($this->sitegroup->group_id)): ?>
              <div class="fleft mright10">
                <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->sitegroup->group_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitegroup_session_payment', true) ?>">
                <input type="hidden" name="group_id_session" id="group_id_session" />
                </form>
              </div>
              <?php endif; ?>
              <?php if (Engine_Api::_()->sitegroup()->canShowRenewLink($this->sitegroup->group_id)): ?>
              <div class="fleft mright10">
                <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->sitegroup->group_id ?>)"><?php echo $this->translate('Renew'); ?></a>
                <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitegroup_session_payment', true) ?>">
                <input type="hidden" name="group_id_session" id="group_id_session" />
                </form>
              </div>
              <?php endif; ?>
              
              <!--Start Cancel Plan-->
              <?php if (Engine_Api::_()->sitegroup()->canShowCancelLink($this->sitegroup->group_id)): ?>
                <div class="fleft mright10">
                  
                  <a href='<?php echo $this->url(array('action' => 'cancel', 'package_id' => $this->package->package_id, 'group_id' => $this->sitegroup->group_id, 'format' => 'smoothbox'), "sitegroup_packages", true); ?>' class="smoothbox" >
                    <?php echo $this->translate('Cancel Package') ?>
                  </a>
                </div>
              <?php endif; ?>
              <!--End Cancel Plan-->
              
            </div>
            <h3><?php echo $this->translate("Current Package: " ).$this->translate(ucfirst($this->package->title)); ?></h3>
          </div>
          <?php $item=$this->package;?>
          <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_packageInfo.tpl'; ?>
        </li>
      </ul>
    </div>
    <div class='sitegroup_package_group mtop15'>
      <?php if (count($this->paginator)): ?>
        
        <ul class="sitegroup_package_list">
          <li>
						<h3><?php echo $this->translate('Other available Packages') ?></h3>
					
						
					
          <span>  <?php echo $this->translate('If you want to change the package for your group, please select one package from the following list of available packages.'); ?></span>
          <?php //start coupon plugin work. ?>
						<?php if (!empty($this->modules_enabled) && in_array("sitegroup_package", $this->modules_enabled)) : ?>
						<div style="margin-top:10px;"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon" onclick="javascript:preview('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'sitegroup_package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></div>
						<?php endif; ?>
          </li>
          <li>
          <div class="tip">
            <span>
              <?php echo $this->translate("Note: Once you change package for your group, all the settings of the group will be applied according to the new package, including apps available, features available, price, etc.");?>
            </span>
          </div>
          </li>
         
          <?php foreach ($this->paginator as $item): ?>
            <li>
                <?php if (empty($package_view)): ?>
              <div class="sitegroup_package_list_title">
                <div class="sitegroup_create_link">
                <?php
                 echo $this->htmlLink(
                array('route' => 'sitegroup_packages', 'action' =>'update-confirmation', "group_id"=> $this->group_id, "package_id"=> $item->package_id),
                $this->translate('Change Package'), array('onclick' => 'owner(this);return false' ,'title' => $this->translate('Change Package')));
                 ?>
                </div>
                <h3>             
                  <a href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitegroup_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                </h3>                 
              </div>
              <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_packageInfo.tpl'; ?>
                <?php else: ?>
                <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_verticalPackageInfo.tpl'; ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
          <br />
          <div>
          <?php echo $this->paginationControl($this->paginator); ?>
          </div>
        </ul>
       
      <?php else: ?>
        <div class="tip">
          <span>
          <?php echo $this->translate("There are no other packages yet.") ?>
          </span>
        </div>
      <?php endif; ?>
      </div>
    <?php if (empty($this->is_ajax)) : ?>		
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">
    
  function submitSession(id){
    document.getElementById("group_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }

</script>
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>