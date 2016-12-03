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

<script type="text/javascript">

  function multiDelete()
  {
    return confirm("<?php echo $this->translate("Are you sure you want to delete the selected Countries?") ?>");
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='settings clr'>
	<h3><?php echo $this->translate("Manage Shipping Locations"); ?></h3>
	<p class="description"><?php echo $this->translate('Below, you can add and manage various countries and their regions / states. Sellers and buyers of your site will be able to sell and purchase products only in the locations configured by you here. You can add new locations by clicking on "Add Locations" link below. You can also enable / disable locations and manage their regions / states. You can also import locations via the csv file by using the "Import a file" link below.');?></p>
</div>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'add-location'), $this->translate("Add Location"), array('class' => 'smoothbox buttonlink seaocore_icon_add')); ?>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'import'), $this->translate("Import Locations"), array('class' => 'buttonlink seaocore_icon_import')); ?>

<br /><br />

<?php if (count($this->paginator)): ?>
  <?php foreach ($this->paginator as $item): ?>
    <?php $countriesName[$item->region_id] = Zend_Locale::getTranslation($item->country, 'country'); ?>
    <?php $locationArray[$item->region_id] = $item; ?>
  <?php endforeach; asort($countriesName);?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
    <table class='admin_table' style="width: 50%;">
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class=""><?php echo $this->translate("Country"); ?></th>
          <th class="admin_table_centered"><?php echo $this->translate("Regions / States"); ?></th>
          <th class="admin_table_centered"><?php echo $this->translate("Status") ?></th>
          <th class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($countriesName as $region_id => $item): ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $locationArray[$region_id]->country ?>' value="<?php echo $locationArray[$region_id]->country ?>" /></td>
            <td class=""><?php echo $item ?></td>
            <td class='admin_table_bold admin-txt-normal admin_table_centered'>
              <?php
                $getEmptyRegionCount = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getEmptyRegionCount($locationArray[$region_id]->country);
                
                $regionCount = $locationArray[$region_id]->regions;
                $allRegionEnabled = false;
                if( !empty($getEmptyRegionCount) ){
                  $regionCount = $locationArray[$region_id]->regions - $getEmptyRegionCount;
                  if( $regionCount <= 0 ){
                    $regionCount = 'ALL';
                    $allRegionEnabled = true;
                  }
                }
                                
                echo $regionCount;
              ?>
            </td>
            <?php if (!empty($locationArray[$region_id]->country_status)): ?>
              <td align="center" class="admin_table_centered">
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'countryenable', 'country' => $locationArray[$region_id]->country, 'current_status' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Country')))) ?>
              </td>
            <?php else: ?>
              <td align="center" class="admin_table_centered">
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'countryenable', 'country' => $locationArray[$region_id]->country, 'current_status' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Country')))) ?>
              </td>
            <?php endif; ?>
            <td align="left" class="admin_table_centered">
              <?php
                if(empty($allRegionEnabled)):
                  echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'manage-region', 'country' => $locationArray[$region_id]->country), $this->translate("manage regions / states"));
                else:
                  echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'delete-location', 'id' => $locationArray[$region_id]->region_id), $this->translate("delete"), array('class' => 'smoothbox'));
                endif;
                      ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons fleft clr mtop10'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>

  <br />
  
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
  
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No locations selected yet.") ?>
    </span>
  </div>
<?php endif; ?>