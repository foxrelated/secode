<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: readme.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<style type="text/css">
	.dbbackup_faq li div{
		clear:both;
		border-left:3px solid #ccc;
		padding:5px 5px 5px 10px;
		margin:5px;
		font-family:arial;
		line:height:18px; 
	}
</style>

<h2><?php echo $this->translate('Backup and Restore') ?></h2>



		
<div class="tabs">
    <ul class="navigation">
    <li class="active">
       <a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() .'/admin/dbbackup/manage/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>

    </li>
</ul></div>		
		<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>



<div class="admin_files_wrapper" style="width:70%;">
	<ul class="admin_files dbbackup_faq" style="max-height:2500px;">
		 
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q : What are backups? Why do I need them?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Imagine that you are installing a new plugin on your site, or making changes on your site, and your database gets messed up. Or, imagine that your hosting server crashed, and you lost everything. These are only some of the many accidents that can occur with your site. Like they say, it's better to be safe than sorry.");?>
				<br style="clear:both;" />
				<?php echo $this->translate("Backups are like insurance for your site. You need them the most when your site is in trouble. They are copies of your data which may be used to restore your site after mishappenings like data loss, data corruption, server crash, site attack, etc. Backups can also be used to migrate your site to another server.");?>
				
				<br style="clear:both;" />
				<?php echo $this->translate("Your site's data and content are its life. The Backup and Restore plugin helps you protect them.");?>
							
			</div>
		</li>	
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q : What different types of backups does this plugin enable for my site?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("This plugin enables you to backup all the content of your site. Thus, it allows you to take both:");?>
				<br style="clear:both;" />
				<?php echo $this->translate("a) Database backup, b) Files backup."); ?> 
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q : What is Files Backup? Why is it needed?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Files backup allows you to backup the code of your site, and the files uploaded on your site. Files uploaded on your site can be user profile photos, album photos, group photos, music content, etc. Files backup is needed so that you do not loose any customizations done to you site, or content uploaded on your site during a server crash, or any other accident.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q : What are backup destinations? What are their different types?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate('Backup destinations are the locations where your backup files are saved. This plugin allows you to create multiple destinations of the following types:');?>
				<br style="clear:both;" />
				<?php echo $this->translate("a) Email: Database backups can be emailed as attachments.") ?>
				<br style="clear:both;" />
				<?php echo $this->translate("b) FTP Server: Backups can be directly saved on external FTP servers.") ?> 
				<br style="clear:both;" />
				<?php echo $this->translate("c) MySQL database: Database backups can be taken on other MySQL databases on the server.
Additionally, there is a backup directory on the site server as well.") ?> 
				<br style="clear:both;" />
				<?php echo $this->translate("Backup files can also be downloaded to your computer and saved on storage discs like hard discs, CDs, DVDs, etc.") ?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q : I am not able to create a \"FTP Server\" destination. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("You will be able to create a \"FTP Server\" destination only if your site's server has permissions to make outgoing FTP connections with other FTP servers for transfers. Please contact your server administrator for this.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q : After submitting the form for creating a \"FTP Server\" destination, I get a blank page. Why is this so?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("If this happens, then it means that your site's server does not have permissions to make outgoing FTP connections with other FTP servers for transfers. Please contact your server administrator for this.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('Q : What is automatic backup?');?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate("You can schedule backups to be automatically performed after specified intervals (like every 6 hours, 12 hours, 1 day, 2 days, 1 week, etc.). If your site is inactive, no auto-backups will be taken till there is activity on site. Automatic backups can be configured from the \"Backup Settings\" section in the Admin Panel.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Q : How will I know when an automatic backup has completed?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo $this->translate("In the \"Backup Settings\" section, you can set email notifications for completion of automatic backups.");?>
			</div>
		</li>
		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Q : I want to take automatic backup of my site's database. Are there any points that I should take care of?");?></a>
			<div class='faq' style='display: none;' id='faq_9'>
				<?php echo $this->translate("Yes, below are some recommended points that you should take care of while going for automatic backups of your site's database:");?>
				<br style="clear:both;" />
				<?php echo $this->translate("1) Your site should have good server resources.") ?>
				<br style="clear:both;" />
				<?php echo $this->translate("2) Your site should be active.") ?> 
				<br style="clear:both;" />
				<?php echo $this->translate("3) If you have activated automatic database backups for your site, you should regularly check the backup logs for the success / failure status of the backups.") ?> 
				<br style="clear:both;" />
				<?php echo $this->translate("4) If you have activated automatic database backups for your site, you should also sometimes take manual backups.") ?>				
				<br style="clear:both;" />
				<?php echo $this->translate("5) The time interval selected by you for automatic database backup should be more than 5 times the duration taken for a manual backup on your site. You may take a manual backup in Take Backup section for finding its time duration. For example, if your site's manual backup takes 4 hours to execute, then the time interval chosen by you here must be greater than 20 hours.") ?>
			</div>
		</li>		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Q : What limit should be entered for the automatic deletion of backups in the Backup Settings?");?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo $this->translate("The limit that you enter should be according to the size of your backup files, and the space on your server. Normally, you may choose a value of 3 for this, but if the space on your server is less, you may choose 1.");?>
			</div>
		</li>		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Q : Does this plugin provide automatic files backup also?");?></a>
			<div class='faq' style='display: none;' id='faq_11'>
				<?php echo $this->translate("No, this plugin does not provide automatic files backup. Automatic database backups can be taken. Automatic files backups are not provided by this plugin because they may be very large for your site, as they also contain media files of your site (user profile pictures, album pictures, music, etc).");?>
			</div>
		</li>
				
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate('Q : When I am trying to download a backup, I am being asked a username and password. Where do I get these from?');?></a>
			<div class='faq' style='display: none;' id='faq_12'>
				<?php echo $this->translate("Your downloadable backups are stored in your server's backup directory. This directory is password protected, which can be configured by you. You can view / edit these credentials in the \"Server Directory\" tab of the \"Destinations\" section.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate('Q : Are there any precautions that I should take while restoring my database?');?></a>
			<div class='faq' style='display: none;' id='faq_13'>
				<?php echo $this->translate("Yes, a failed restore can destroy your database. Always test database backup files on a non-production, test environment first.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate('Q : Can I use other tools for restoring the database using the backup created by this plugin?');?></a>
			<div class='faq' style='display: none;' id='faq_14'>
				<?php echo $this->translate("Yes, the database backup files created by this plugin are a list of SQL statements which can also be imported / executed by other database handling tools like phpMyAdmin or the mysql command-line client.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate('Q : In the Database Restore section, can I restore the database by uploading a backup / SQL file taken from another database backup source?');?></a>
			<div class='faq' style='display: none;' id='faq_15'>
				<?php echo $this->translate("No, the Restore Database functionality of this plugin requires a backup file generated only by this plugin for restoring your database.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate('Q : What will happen if I restore my database from a backup not having all the database tables?');?></a>
			<div class='faq' style='display: none;' id='faq_16'>
				<?php echo $this->translate("Only the database tables available in your backup will be restored; the others will remain as they are.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate('Q : Does this plugin provide both database and files restore functionalities?');?></a>
			<div class='faq' style='display: none;' id='faq_17'>
				<?php echo $this->translate("No, this plugin only restores the database. To restore the files of your site from a files backup, you could simply use an FTP client which you would have used for uploading SocialEngine code on your server.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate('Q : My site is in Maintenance Mode, and I do not remember the Access Code. How can I get it online again?');?></a>
			<div class='faq' style='display: none;' id='faq_18'>
				<?php echo $this->translate("You may follow the following steps:");?>
				<br style="clear:both; "/>
				<?php echo $this->translate("1) On your site's server, go to the directory: \"application/settings\".");?>
				<br style="clear:both; "/>
				<?php echo $this->translate("2) Open the file: \"general.php\".");?>
				<br style="clear:both; "/>
				<?php echo $this->translate("3) In this file, change the line: \"'enabled' => true\" to: \"'enabled' => false\".");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate('Q : How can this plugin help me in migrating my site?');?></a>
			<div class='faq' style='display: none;' id='faq_19'>
				<?php echo $this->translate("The Backup and Restore Plugin is very useful if you want to migrate your website. You may follow the below steps:");?>
				<br style="clear:both;" />
				<?php echo $this->translate("1) Take recent Database and Files Backups of your site using the Backup and Restore Plugin.");?>
				<br style="clear:both;" />
				<?php echo $this->translate("2) In order to install your site, you will need the following four pieces of information. If you don't have any of these, please contact your hosting provider and ask them for assistance.");?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- MySQL Server Address (often \"localhost\", \"127.0.0.1\", or the server IP address)"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- MySQL Database Name"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- MySQL Username"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- MySQL Password"); ?>
				<br style="clear:both;" />
				<?php echo $this->translate("3) On your new server, create a database with a name of your choice.");?>
				<br style="clear:both;" />
				<?php echo $this->translate("4) Use a database handling tool like phpMyAdmin or the mysql command line to import the database backup file into the new database.");?>
				<br style="clear:both;" />
				<?php echo $this->translate("5) Extract the zipped Files backup on your computer and upload all of the files to your hosting account (it can exist either in the root HTML directory, or a subdirectory).");?>
				<br style="clear:both;" />
				<?php echo $this->translate("6) If you are using a Unix server (or Unix variant, like Linux, OS X, FreeBSD, etc.) you must set the permissions (CHMOD) of the following directories and files to 777:");?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /install/config/ (recursively; all directories and files contained within this must also be changed)");?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /temporary/ (recursively; all directories and files contained within this must also be changed)"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /public/ (recursively; all directories and files contained within this must also be changed)"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /application/themes/ (recursively; all directories and files contained within this should also be changed)"); ?>
				&nbsp;&nbsp;<?php echo $this->translate("- /application/packages/ (recursively; all directories and files contained within this should also be changed)"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /application/languages/ (recursively; all directories and files contained within this must also be changed)"); ?>
				<br style="clear:both;" />
				&nbsp;&nbsp;<?php echo $this->translate("- /application/settings/ (recursively; all files contained within this must also be changed)"); ?>
				<br style="clear:both;" />
				<?php echo $this->translate("7) Update the settings of your new server in the file: \"/application/settings/database.php\" to point to your new database. ");?>

			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate('Q : The CSS of this plugin is not coming on my site. What should I do?');?></a>
			<div class='faq' style='display: none;' id='faq_20'>
				<?php echo $this->translate("Please enable the \"Development Mode\" system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to \"Production Mode\".");?>
			</div>
		</li>
		
</ul>
	</div><br />
		<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
</ul>
	</div>
	<br />
	
</div>
	
<script type="text/javascript" >

function form_submit() {
	
	var url='<?php echo $this->url(array('module' => 'dbbackup', 'controller' => 'autobackupsettings', 'action' => 'index'), 'admin_default', true) ?>';
	window.location.href=url;
}

</script>