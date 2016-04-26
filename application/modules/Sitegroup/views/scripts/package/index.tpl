<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: packages.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.view', 1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
?>
<?php if (!empty($this->couponmodules_enabled)) :?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecoupon/views/scripts/getcode.tpl'; ?>
<?php endif; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl'; ?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;

	?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle sitegroup_create_wrapper clr">
	<h3><?php echo $this->translate("Create New Group") ?></h3>
	<p><?php echo $this->translate("Create a group using these quick, easy steps and get going.");?></p>	
	<h4 class="sitegroup_create_step fleft"><?php echo $this->translate('1. Choose a Group Package');?></h4>
	
	<?php //start coupon plugin work. ?>
	<?php if (!empty($this->modules_enabled) && in_array("sitegroup_package", $this->modules_enabled)) : ?>
	<h4 class="sitegroup_create_step fright"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon" onclick="javascript:preview('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'sitegroup_package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></h4>
	<?php endif; ?>
	
	<div class='sitegroup_package_group'>
		<?php if( count($this->paginator) ): ?>
			
        
        <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
                                <?php if (empty($package_view)): ?>
            <ul class="sitegroup_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to groups created under them. You can change your package anytime later.');?></span>
				</li>
		 		<?php   foreach ($this->paginator as $item): ?>
					<li>
			 			<div class="sitegroup_package_list_title">
	            <div class="sitegroup_create_link">
								<?php if (!empty($this->parent_id)): ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'parent_id' => $this->parent_id), 'sitegroup_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Group'); ?> &raquo;</a>
								<?php elseif(!empty($this->page_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'page_id' => $this->page_id), 'sitegroup_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Group'); ?> &raquo;</a>
								<?php elseif(!empty($this->business_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'business_id' => $this->business_id), 'sitegroup_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Group'); ?> &raquo;</a>
								<?php elseif(!empty($this->store_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'store_id' => $this->store_id), 'sitegroup_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Group'); ?> &raquo;</a>
								<?php else: ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id), 'sitegroup_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Group'); ?> &raquo;</a>
								<?php endif; ?>
	           </div>
           	 <h3>             
                <a href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitegroup_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
              </h3>            	 
			 			</div>
             <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_packageInfo.tpl'; ?>
	          
			 		</li>
		    <?php endforeach; ?>
				<br />
				<div>
				  <?php echo $this->paginationControl($this->paginator); ?>
				</div>
			</ul>	
                                <?php else:?>
                                        <ul class="seaocore_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to groups created under them. You can change your package anytime later.');?></span>
				</li>
                                        <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_verticalPackageInfo.tpl'; ?>
                                </ul>
                                        <?php endif;?>
		<?php else: ?>
		 <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no packages yet.") ?>
		    </span>
		  </div>
		<?php endif; ?>
  </div>
</div>
	
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>