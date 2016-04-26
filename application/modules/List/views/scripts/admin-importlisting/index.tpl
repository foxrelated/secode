<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin'); ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'log', 'action' => 'index'), $this->translate('Import History'), array('class' => 'buttonlink icon_lists_log')) ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'importlisting', 'action' => 'manage'), $this->translate('Manage CSV Import Files'), array('class' => 'buttonlink icon_list_admin_import_manage')) ?><br/><br/>

<div class="importlisting_form">
	<div>
		<h3><?php echo $this->translate('Import Listings from a CSV file');?></h3>

		<p>
		 <?php echo $this->translate("This tool allows you to import Listings corresponding to the entries from a .csv file. Before starting to use this tool, please read the following points carefully.");?>
		</p>

		<ul class="importlisting_form_list">

			<li>
				<?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done.");?>
			</li>

			<li>
				<?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file . If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in fields like description, price, overview etc. for the entries in the CSV file.");?>
			</li>

			<li>
				<?php echo $this->translate("Listing title and description are the required fields for all the entries in the file.");?>
			</li>

			<li>
				<?php echo $this->translate("Category and sub-category name should exactly match with the existing categories and sub-categories, otherwise category or sub-category will be considered as null for that Listing.");?>
			</li>

			<li>
				<?php echo $this->translate("In case you want to insert more than one tag for an entry, then the tags string should be seperated by hash('#'). For example, if you want to insert 2 tags for an entry - 'tag1' and 'tag2', then tag string for that will be 'tag1#tag2'.");?>
			</li>

			<li>
				<?php echo $this->translate("You can import the maximum of 2000 listings at a time and if you want to import more, you would have to then repeat the whole process. For example, you have to import 3500 listings. Then, you would have to create 2 CSV files - one having 2000 entries and another having 1500 entries corresponding to the Listings. After that, just upload both the files and import listings from them one by one from 'Manage CSV Import Files' section.");?>
			</li>

			<li>
				<?php echo $this->translate("You can also 'Stop' and 'Rollback' the import process. 'Stop' will just stop the import process going on at that time from that file and 'Rollback' will undo or delete all the Listings created from that CSV import file till that time.");?>
			</li>

			<li>
				<?php echo $this->translate("Files must be in the CSV format to be imported. You can also download the demo template below for your reference.");?>
			</li>

		</ul>
		
		<br />

		<a href=<?php echo $this->url(array('action' => 'download')) ?><?php echo '?path=' . urlencode('example_listing_import.csv');?> target='downloadframe' class="buttonlink icon_lists_download_csv"><?php echo $this->translate('Download the CSV template')?></a>
		
		<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin-importlisting', 'action' => 'import'), $this->translate('Import a file'), array('class' => 'smoothbox buttonlink icon_lists_import')) ?>
		
		<br />
		<br />
		
	</div>
</div>		