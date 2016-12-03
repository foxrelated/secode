<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: import.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

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

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'index'), $this->translate("Back to Manage Shipping Locations"), array('class' => 'seaocore_icon_back buttonlink')); ?><br/><br/>

<div class="importlisting_form">
	<div>
		<h3><?php echo $this->translate('Import Locations from a CSV file');?></h3>

		<p>
		 <?php echo $this->translate("This tool allows you to import Locations corresponding to the entries from a .csv file. Before starting to use this tool, please read the following points carefully.");?>
		</p>

		<ul class="importlisting_form_list">

			<li>
				<?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done.");?>
			</li>

			<li>
				<?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file . If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in region field for the entries in the CSV file.");?>
			</li>

			<li>
				<?php echo $this->translate("Country and Regions names are required fields for all the location entries in file.");?>
			</li>
      
			<li>
				<?php echo $this->translate("Country name must exactly match with the existing country name.");?>
			</li>      
      
			<li>
				<?php echo $this->translate("You can use 1 OR 0 for enabling / disabling country status and region status for all the location entries in the file.");?>
			</li>        

			<li>
				<?php echo $this->translate("Files must be in the CSV format to be imported. You can also download the demo template below for your reference.");?>
			</li>

		</ul>
		
		<br />
    <a href=<?php echo $this->url(array('action' => 'download')) ?><?php echo '?path=' . urlencode('example_location_import.csv');?> target='downloadframe' class="buttonlink seaocore_icon_download"><?php echo $this->translate('Download the CSV template')?></a>
		
    <a href="javascript:void(0)" class="buttonlink seaocore_icon_view" onclick="Smoothbox.open('<?php echo $this->url(array('action' => 'view-countries-code')) ?>')"><?php echo $this->translate('View Country Codes')?></a>
    
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'import-location-file'), $this->translate("Import Locations"), array('class' => 'smoothbox buttonlink seaocore_icon_import')); ?><br/><br/>

		<br />
	</div>
</div>	