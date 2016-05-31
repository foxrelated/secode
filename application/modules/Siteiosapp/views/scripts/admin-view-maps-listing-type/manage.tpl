<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2>
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) { echo $this->translate('Reviews & Ratings - Multiple Listing Types Plugin'); } else { echo $this->translate('Reviews & Ratings Plugin'); }?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <div>
      <h3><?php echo $this->translate("Multiple Listing Type Layout Settings") ?> </h3>
      <p class="form-description">
        <?php echo $this->translate("Below, you can configure the browse pages' & profile pages' layout of respective listing type, for your app.") ?>
      </p>
    </div>
  </div>
</div>

<form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' => 'manage')) ?>' style="overflow:hidden;">
	<input type='hidden'  name='order' id="order" value=''/>
	<div class="seaocore_admin_order_list" style="width:100%;">
        
		<div class="list_head">     
			<div style="width:3%;">
				<?php echo $this->translate("ID") ?>
			</div>
			<div style="width:15%;">
				<?php echo $this->translate("Listing Type") ?>
			</div>
                        <div style="width:25%;">
				<?php echo $this->translate("Browse View Type") ?>
			</div>
			<div style="width:25%;">
				<?php echo $this->translate("Profile View Type") ?>
			</div>
			<div style="width:20%;">
				<?php echo $this->translate("Options") ?>
			</div>      
		</div>
    
		<div id='order-element'>
			<ul>
				<?php foreach ( $this->listingTypes as $listingType) :?>
					<li>
						<input type='hidden'  name='order[]' value='<?php echo $listingType->listingtype_id; ?>'>
						<div style="width:3%;">
							<?php echo $listingType->listingtype_id ?>
						</div>
						<div style="width:15%;">
              <?php echo $this->translate($listingType->title_plural) ?>
						</div>
                                                <div style="width:25%;">
              <?php echo $this->browseMapViewArray[$listingType->listingtype_id] ?>
						</div>
                                                <div style="width:25%;">
              <?php echo $this->profileMapViewArray[$listingType->listingtype_id] ?>
						</div>
           
            
						<div style="width:10%;">
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteiosapp', 'controller' => 'view-maps-listing-type', 'action' => 'map', 'listingtype_id' => $listingType->listingtype_id), $this->translate('Edit'),array(
	                            'class' => 'smoothbox',
	                          )) ?> 
						</div>               
					</li>
				<?php endforeach; ?>
	    </ul>
    </div>
  </div>
</form>
<br />

