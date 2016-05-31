<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: create.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>

<style type="text/css">
#box { 
	border:1px solid #ccc; 
	width:200px; 
	height:20px;
}
#perc { 
	background:#ccc; 
	height:20px;
}

/* example 2 */
#box2	{ 
	border:1px solid #a8cadc;
	width:220px !important; 
	height:20px;
}
#content-left{
	position:relative;
}
#content-left #box2	{ 
	border:1px solid #a8cadc;
	width:220px !important; 
	height:20px;
	top:-22px;
	position:absolute;
	float:left;
	left:0px;
}
#perc2		{ 
	width:210px; 
	height:20px;
	background-color:#bad8e8;
	float:left;
	display:block;
}
#text{ 
	font-family:tahoma, arial, sans-serif; 
	font-size:11px; 
	color:#000; 
	float:left; 
	padding:0;
	width:225px;
	margin-top:-18px;
	text-align:center; 
}
div, td {
	color:#666666;
	font-family:tahoma,arial,verdana,sans-serif;
	font-size:10pt;
	text-align:left;
}
.dbbackup_form
{
  -moz-border-radius: 7px;
  -webkit-border-radius: 7px;
  border-radius:  7px;
  background-color: #e9f4fa;
  padding: 10px;
  float: left;
  overflow: hidden;
}
.dbbackup_form .dbbackup_form_inner
{
  background: #fff;
  border: 1px solid #d7e8f1;
  overflow: hidden;
  padding: 20px;
}
.settings h3
{
  margin-bottom: 12px;
  margin-left: -1px;
	color:#717171;
	font-size:13pt;
	font-weight:bold;
	letter-spacing:-1px;
	margin:0 0 3px;
}
.dbbackup_success	{
  color: #546d50;
  background-color: #e3f2e1;
  border: 2px solid #d2e5cf;
  font-weight: bold;
  padding: 5px 5px 5px 28px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius:  5px;
  background-img:;
  background-image: url(application/modules/Dbbackup/externals/images/success.png);
  background-repeat:no-repeat;
  background-position:6px 6px;
  margin-top:15px;
  margin-bottom:15px;
}
table.dbbackup_table{
	margin-top:5px;
	border:1px solid #EEEEEE;
	border-bottom:none;
	min-width:450px;
}
table.dbbackup_table tr:nth-child(2n) {
	background-color:#F8F8F8;
}

table.dbbackup_table tr td,
table.dbbackup_progressbar_table tr td {
	border-bottom:1px solid #EEEEEE;
	font-size:0.9em;
	padding:7px 10px;
	vertical-align:top;
	white-space:nowrap;
	color:#000;
}

table.dbbackup_progressbar_table{
	margin-top:15px;
	border:1px solid #EEEEEE;
	border-bottom:none;
	width:450px;
	margin-bottom:15px;
}
table.dbbackup_progressbar_table tr{
	background-color:#F8F8F8;
}
table.dbbackup_table tr td{
	font-size:11px;
}
.alert_message
{
  overflow: hidden;
  clear: both;
}
.alert_message > span
{
  padding:6px;
  border-width:1px;
  border-color:#e40001;
  float: left;
  margin-bottom: 0;
  background-color:#ed3333;
  font-weight:bold;
  color:#fff;
}

a.button
{
  -moz-border-radius:3px;
  -webkit-border-radius:3px;
  border-radius:3px;
	padding: 5px;
  font-weight: bold;
  border: none;
  background-color: #619dbe;
  border: 1px solid #50809b;
  color: #fff;
  background-image: url(application/modules/Core/externals/images/buttonbg.png);
  background-repeat: repeat-x;
  background-position: 0px 1px;
  font-family: tahoma, verdana, arial, sans-serif;
}
a.button:hover
{
  background-color: #7eb6d5;
  cursor: pointer;
  text-decoration:none;
}
</style>
<?php
  $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
	$tmpPath_files = APPLICATION_PATH;
	$files = array($tmpPath_files . DIRECTORY_SEPARATOR . 'application',
			$tmpPath_files .  DIRECTORY_SEPARATOR . 'externals',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'install',
			$tmpPath_files .  DIRECTORY_SEPARATOR . 'public',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'temporary',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'changelog.html',
			$tmpPath_files . DIRECTORY_SEPARATOR  . 'crossdomain.xml',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'index.php',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'package.json',
			$tmpPath_files .DIRECTORY_SEPARATOR . 'README.html',
			$tmpPath_files . DIRECTORY_SEPARATOR . 'rpx_xdcomm.html',
			$tmpPath_files .DIRECTORY_SEPARATOR . 'xd_receiver.htm',
			$tmpPath_files .DIRECTORY_SEPARATOR . '.htaccess',
			$tmpPath_files .DIRECTORY_SEPARATOR . '.',
			$tmpPath_files .DIRECTORY_SEPARATOR . '..',
	); 
	$archiveSourcePaths = APPLICATION_PATH . DIRECTORY_SEPARATOR;
  $its = new DirectoryIterator($archiveSourcePaths);
  $pathnames = array();
	foreach ($its as $file) {
		$pathname = $file->getPathname();
		if (in_array($pathname, $files)) {
			continue;
		} else {
      $pathnames[] = $file->getPathname();
    }
	}
  $imploded_string="";
  if(!empty($pathnames)) {
		foreach ($pathnames as $filevalue) {
			$skippedfile_value = str_replace($archiveSourcePaths, "", $filevalue);
			$resultsrootfiles[$skippedfile_value] = str_replace($archiveSourcePaths, "", $skippedfile_value);
		}
		$imploded_string = implode(", ", $resultsrootfiles);
  }
?>
<?php if(!$this->flage && $this->destination_mode1 != 3 && $this->backup_completecode != 1 && !empty($imploded_string)): ?>
	<div class="tip">
		<span>
			<?php
				echo $this->translate("Note: If you are getting any 'Error' during the Backup Process then you should exclude some directories or files before clicking on 'Backup Now !' button by using 'File Directories to Backup in $base_url/(ROOT_DIRECTORY)' option. For example, you may exclude these directories/files: $imploded_string.");
			?>
		</span>
	</div>
<?php endif;?>
<?php $admin_reauthenticate = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.admin.reauthenticate', 0);?>
<?php if(!empty($admin_reauthenticate) && !$this->flage):?>
  <div class="tip">
		<span>
			<?php
				echo $this->translate('Note: You are using the "Password" and "Timeout" fields from the "Settings" > "Admin Password" section of your site\'s Admin Panel. Thus, if the backup process requires more time to complete than the Timeout set by you, then you will get an Error and the backup will not complete. Thus, before you take a manual backup, please choose "Do not require reauthentication." option in "Settings" > "Admin Password".');
			?>
		</span>
  </div>
<?php endif;?>
<div class="settings">
	<div class="dbbackup_form">
		<div class="dbbackup_form_inner">
		<?php if(!$this->flage): ?>

			<h3> <?php echo $this->translate('Backup in Progress')?></h3>
			<div class='alert_message' style="margin-top:15px;">
				<span>  
					<?php echo $this->translate('Please do not close this page or navigate to another page till you see a backup completion or error message.')?>
				</span>
			</div>
			 <table class="dbbackup_progressbar_table">
			    <tr>
			      <td>
			      	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/sign.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>
			    		<?php echo $this->translate('Initializing backup process.')?>
			    	</td>	
			    </tr>
			    <?php if ($this->initial_code == 0): ?>
			      <tr style="font-weight:bold;">
			      	<td>
			      		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/arrow-right.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>&nbsp;
			        <?php elseif($this->initial_code >0): ?>
			          <tr>
			          	<td>
			          	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/sign.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>
			        <?php else: ?>
			       <tr>
			       	<td>
			         <?php endif; ?>
			         <?php if($this->destination_mode1 == 3 ): ?> 
			      	<?php echo $this->translate('Connecting to the database.')?>
			      	<?php else: ?>
			      	<?php echo $this->translate('Preparing the archive.')?>
			      	<?php endif; ?>
			      </td>
			    </tr>
			    <?php  if ($this->backup_completecode == 1 ):?>
			      <?php   if ($this->initial_code >0): ?>
			      <tr style="font-weight:bold;">
			      	<td>
			      		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/arrow-right.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>&nbsp;
			        <?php else: ?>
			       <tr>
			       	<td>
			       		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/sign.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>
			         <?php endif; ?>
			      	<?php echo $this->translate('Backing up database tables.')?>
			      </td>
			    </tr>
			    <?php endif; ?>
			      <?php  if ($this->backup_completecode == 2 ):?>
			      <?php   if ($this->initial_code >0): ?>
			      <tr style="font-weight:bold;">
			      	<td>
			      	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/arrow-right.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>&nbsp;
			        <?php else: ?>
			       <tr>
			       	<td>
			       		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/sign.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>
			         <?php endif; ?>
			      <?php echo $this->translate('Backing up database and files.')?></td>
			    </tr>
			    <?php endif; ?>
			      <?php  if ( $this->backup_completecode == 0):?>
			      <?php   if ($this->initial_code >0 && $this->start_code): ?>
			      <tr style="font-weight:bold;">
			      	<td>
			      		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/arrow-right.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>&nbsp;
			        <?php else: ?>
			       <tr>
			       	<td>
			       		<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/sign.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle;')) ?>
			         <?php endif; ?>
			      	<?php echo $this->translate('Backing up files.')?></td>
			    </tr>
			     <?php endif; ?>

         
			    <tr>
			      <td><?php echo $this->translate('Finishing backup process.')?></td>
			    </tr>
			</table>
			
		
			<?php  if ( ($this->backup_completecode == 0 || $this->backup_code == 4 ) && $this->initial_code >0 && $this->start_code):?>
				<div style="display:block;height:15px;width:128px;background-image:url(application/modules/Dbbackup/externals/images/backup-uploading.gif);background-position:0 0;float:left;font-size:0px;">
					<img src="application/modules/Dbbackup/externals/images/backup-uploading.gif" alt="" style="height:15px;width:128px;" />
      	</div>
			<?php endif; ?>
			

<?php endif; ?>


<?php

if($this->initial){
	$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
  $parameter = '<form action="'.$base_url.'/admin/dbbackup/backupsettings/create?backup_options='.$this->backup_options.'&backup_completecode='.$this->backup_completecode.'&destination_id='.$this->destination_id.'&lockoption='.$this->lockoption.'&code_destination_id='.$this->code_destination_id.'" method="POST" name="admin_dbbackup1" id="admin_dbbackup1">';
  	$parameter .= '<input type="hidden" name="backup_options" value='.$this->backup_options.'>';
    	$parameter .= '</form>';
		$page_submit = $parameter.'<script language="javascript" type="text/javascript">setTimeout("document.admin_dbbackup1.submit()", 1);</script>';

		echo $page_submit;
}
if( $this->backup_initial != 0 ) {



	if($this->initial_code<=($this->num_selected_table) ) {
	$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		echo '<div id="box2"><div id="perc2"></div></div><div id="text"></div><div id="content-left"><div id="put-bar-here2"></div>
		     </div>';
		$parameter = '<form action="'.$base_url.'/admin/dbbackup/backupsettings/create?backup_options='.$this->backup_options.'&backup_completecode='.$this->backup_completecode.'&destination_id='.$this->destination_id.'&lockoption='.$this->lockoption.'&code_destination_id='.$this->code_destination_id.'" method="POST" name="admin_dbbackup1" id="admin_dbbackup1">';
		$parameter .= '<input type="hidden" name="backup_options" value='.$this->backup_options.'>';
		$parameter .= '<input type="hidden" name="backup_initial" value='.$this->backup_initial . '>';
		$parameter .= '<input type="hidden" name="initial_code" value='."$this->initial_code".'>';
		$parameter .= '<input type="hidden" name="filename_compressed_form" value='."$this->filename_compressed_form".'>';
		$parameter .= '<input type="hidden" name="starting_row_point" value='."$this->starting_row_point".'>';
		$parameter .= '<input type="hidden" name="row_limit" value='."$this->row_limit".'>';
		$parameter .= '<input type="hidden" name="percentage" value='."$this->percentage".'>';
		$parameter .= '<input type="hidden" name="change_table" value='."$this->change_table".'>';
		$parameter .= '<input type="hidden" name="max_time" value='."$this->max_time".'>';
		$parameter .= '<input type="hidden" name="script_time" value='."$this->script_time".'>';
		$parameter .= '<input type="hidden" name="speed_up" value='."$this->speed_up".'>';
		$parameter .= '<input type="hidden" name="refresh_page" value='."$this->refresh_page".'>';
		$parameter .= '<input type="hidden" name="addtional_time" value='."$this->addtional_time".'>';
		$parameter .= '<input type="hidden" name="backup_filepath" value='."$this->backup_filepath".'>';
		$parameter .= '<input type="hidden" name="backup_completecode" value='."$this->backup_completecode".'>';
    $parameter .= '<input type="hidden" name="file_size" value='."$this->fileSize".'>';
    $parameter .= '<input type="hidden" name="start_time" value='."$this->start_time".'>';
		$parameter .= '</form>';
		$page_submit = $parameter.'<script language="javascript" type="text/javascript">setTimeout("document.admin_dbbackup1.submit()", 1);</script>';
		
		echo $page_submit;
	}
	else {
		$backup_time = time();
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$file_name = $this->dir_name_temp;
		if($this->backup_options == 0) {
			$backup_options = 'Server Backup Directory & Download';
			$success_message =  'Congratulations!! You database has been successfully backed up.<br />'.'Backup file generated: ';
			$success_message .= "<a href=$base_url/public/$file_name/$this->filename_compressed_form>$this->filename_compressed_form</a>";
		}
		else {
			$backup_options = 'Download';
			$success_message =  'Congratulations!! You database has been successfully backed up.<br />'.'Backup file generated: '.$this->filename_compressed_form.'.'; ?>

			<?php
		}
    
	}
} 
?>
			<?php	if($this->flage):?>
    	<h3> <?php echo $this->translate('Backup Completed Successfully')?></h3>
			<div class="dbbackup_success">
			 <?php if($this->backup_completecode == 0): ?>
				<?php echo $this->translate('Congratulations! Your files have been successfully backed up.')?>
			 <?php elseif($this->backup_completecode == 1): ?>
			 	<?php echo $this->translate('Congratulations! Your database has been successfully backed up.')?>
			 <?php elseif($this->backup_completecode == 2): ?>
			 	<?php echo $this->translate('Congratulations! Your database and files have been successfully backed up.')?>		
			 	<?php endif; ?>	
			 </div>
	    <div style="background:#E9F4FA;padding:5px;"><b><?php echo $this->translate('100% backup complete!')?></b></div>
	    <?php $base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
	    <?php $file_name = $this->dir_name_temp; ?>




	    <?php if($this->backup_completecode == 0): ?>
	    <br />
			<div><b><?php echo $this->translate('Files backup information')?></b></div>
	    <table class="dbbackup_table">
		    <tr>
		    	<td>
		       	<u><?php echo $this->translate('File size')?></u>: <?php echo $this->code_filesize ?>
		      </td> 
        </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->codeDuration  ?>
		      </td>
		    </tr>
		    <tr>
		    	<td title="<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): echo $this->translate('Download backup file'); endif;?>">
		    	
		      	<u><?php echo $this->translate('Backup file generated')?></u>:
		      	
		      	<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): ?>
		      	<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($this->code_filename) ? '?path=' . urlencode($this->code_filename) : '' ?> target='downloadframe'><?php echo $this->translate($this->code_filename)?></a>&nbsp;
		      	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/download.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle')) ?>
          <?php else:?>
         <?php  echo "$this->code_filename"?>&nbsp;
         <?php endif;  ?>		      	
		      </td>
		    </tr>
		  </table>  

		    <?php endif;?>
		    <?php if($this->backup_completecode ==1 && $this->backup_code != 4 ): ?>
		    <br />
		  <div><b><?php echo $this->translate('Database backup information')?></b></div>
		  <table class="dbbackup_table">  
		    <tr>
		    	<td>
		       
		       <u><?php echo $this->translate('File size')?></u>: <?php echo $this->database_filesize ?>
		      </td> 
		    </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->databaseDuration ?>
		      </td>
		    </tr>
		    <tr>
		     	<td title="<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->database_filename) ): echo $this->translate('Download backup file'); endif;?>">
		       <u><?php echo $this->translate('Backup file generated')?></u>:
		      <?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->database_filename) ): ?>
					<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($this->database_filename) ? '?path=' . urlencode($this->database_filename) : '' ?> target='downloadframe'><?php echo $this->translate($this->database_filename)?></a>&nbsp;
          <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/download.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle')) ?>
          <?php else:?>
         <?php  echo "$this->database_filename"?>&nbsp;
         <?php endif;  ?>
          </td>
		    </tr>
		  </table> 
		 

	    <?php endif;?>
	    <?php  if($this->backup_completecode ==2 && $this->backup_code != 4): ?>
			<br />
	    <div><b><?php echo $this->translate('Database backup information')?></b></div>

	    <table class="dbbackup_table">  
		    <tr>
		     	<td>
		       	<u><?php echo $this->translate('File size')?></u>: <?php echo $this->database_filesize ?>
		      </td> 	
		    </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->databaseDuration  ?>
		      </td>
		    </tr>
		    <tr>
		    	<td title="<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->database_filename) ): echo $this->translate('Download backup file'); endif;?>">
		      <u><?php echo $this->translate('Backup file generated')?></u>:
		      <?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->database_filename) ): ?>
         	<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($this->database_filename) ? '?path=' . urlencode($this->database_filename) : '' ?> target='downloadframe'><?php echo $this->translate($this->database_filename)?></a>&nbsp;
         	
         <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/download.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle')) ?>
         <?php else:?>
        <?php  echo "$this->database_filename"?>&nbsp;

         <?php endif;  ?>
          </td>
		    </tr>
		  </table>  
			<br />
		  <div><b><?php echo $this->translate('Files backup information')?></b></div>
		  <table class="dbbackup_table">    
		    <tr>
		    	<td>
		      	<u><?php echo $this->translate('File size')?></u>: <?php echo $this->code_filesize ?>
		      </td>	
		    </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->codeDuration ?>
		      </td>
		    </tr>
		    <tr>
		    	<td title="<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): echo $this->translate('Download backup file'); endif;?>">
		      	<u><?php echo $this->translate('Backup file generated')?></u>:
		      	
		      	<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): ?>
		      	<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($this->code_filename) ? '?path=' . urlencode($this->code_filename) : '' ?> target='downloadframe'><?php echo $this->translate($this->code_filename)?></a>&nbsp;
		      	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/download.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle')) ?>
		      	<?php else:?>
		        <?php  echo "$this->code_filename"?>&nbsp;
		
		         <?php endif;  ?>
		      </td>	
		    </tr>
		 	</table>
      

		    <?php endif;?>



      <?php  if($this->backup_code == 4): ?>
			<br />
	    <div><b><?php echo $this->translate('Database backup information')?></b></div>

	    <table class="dbbackup_table">
		    <tr>
		     	<td>
		       	<u><?php echo $this->translate('Database Name')?></u>: <?php echo $this->database_name ?>
		      </td>
		    </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->databaseDuration  ?>
		      </td>
		    </tr>
		  </table>
           <?php  if($this->backup_completecode == 2): ?>
			<br />
		  <div><b><?php echo $this->translate('Files backup information')?></b></div>
		  <table class="dbbackup_table">
		    <tr>
		    	<td>
		      	<u><?php echo $this->translate('File size')?></u>: <?php echo $this->code_filesize ?>
		      </td>
		    </tr>
        <tr>
		     	<td>
		       	<u><?php echo $this->translate('Time Taken')?></u>: <?php echo $this->codeDuration ?>
		      </td>
		    </tr>
		    <tr>
		    	<td title="<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): echo $this->translate('Download backup file'); endif;?>">
		      	<u><?php echo $this->translate('Backup file generated')?></u>:

		      	<?php if(file_exists(APPLICATION_PATH.'/public/'.$file_name.'/'.$this->code_filename) ): ?>
		      	<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($this->code_filename) ? '?path=' . urlencode($this->code_filename) : '' ?> target='downloadframe'><?php echo $this->translate($this->code_filename)?></a>&nbsp;
		      	<?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/download.png', '', array('class' => 'icon', 'style'=>'vertical-align:middle')) ?>
		      	<?php else:?>
		        <?php  echo "$this->code_filename"?>&nbsp;

		         <?php endif;  ?>
		      </td>
		    </tr>
		 	</table>
          <?php endif;  ?>

		    <?php endif;?>






		 
		  <div style="margin-top:15px;clear:both;">  
	    	
        <?php if($this->backup_completecode ==0): ?>
        <?php  echo $this->htmlLink( array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'codebackup', 'action' => 'index'),	$this->translate('View Files Backup History'), array('target'=>'_parent', 'class'=>'button'))?>
		    <?php endif;?>
        <?php if($this->backup_completecode ==1): ?>
        <?php  echo $this->htmlLink( array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'manage', 'action' => 'index'), $this->translate('View Database Backup History'), array('target'=>'_parent', 'class'=>'button'))?>&nbsp;
      	<?php endif;?>
        <?php  if($this->backup_completecode ==2): ?>
      	<?php  echo $this->htmlLink( array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'manage', 'action' => 'index'), $this->translate('View Database Backup History'), array('target'=>'_parent', 'class'=>'button'))?>&nbsp;
      	<?php  echo $this->htmlLink( array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'codebackup', 'action' => 'index'),	$this->translate('View Files Backup History'), array('target'=>'_parent', 'class'=>'button'))?>
        <?php endif;?>
      </div>


  
		    <?php if($this->destination_id == 0  && ($this->backup_completecode==1 || $this->backup_completecode==2 ) ) :  ?>
		    <iframe id="savetocomputer" src="about:blank" style="visibility:hidden;border:none;height:1em;width:1px;"></iframe>
		  	<script type="text/javascript">
          
						<?php
		    		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
						$file_name = $this->dir_name_temp; ?>
					var backup = document.getElementById("savetocomputer")
					<?php $file = "$base_url/public/$file_name/$this->database_filename";?>
		    	backup.src = "<?php echo $file?>"
		    </script>
		    <?php endif;?>

         <?php if($this->code_destination_id == 0 && ($this->backup_completecode==0 || $this->backup_completecode==2 )) :  ?>

		    <iframe id="savetocomputer1" src="about:blank" style="visibility:hidden;border:none;height:1em;width:1px;"></iframe>
				<script type="text/javascript">
        
						<?php
		    		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
						$file_name = $this->dir_name_temp; ?>
					var backup1 = document.getElementById("savetocomputer1")
					<?php $file1 = "$base_url/public/$file_name/$this->code_filename";?>
		    	backup1.src = "<?php echo $file1?>"
		    </script>
		    <?php endif;?>



<?php endif; ?>
	  </div>
	</div>
</div>
<script language="javascript" type="text/javascript">			
	window.addEvent("domready", function(event) {
			pb2 = new dwProgressBar({
			container: $("put-bar-here2"),
			startPercentage: 10,
			speed:1,
			boxID: "box2",
			percentageID: "perc2",
			displayID: "text",
			displayText: true,
			step:15,
			onComplete: function() {
			},
			onChange: function() {
			}
		});
		
		pb2.set('<?php echo $this->percentage;?>');
	});
									
	//INITIALIZING PROGRESS BAR CLASS.
	var dwProgressBar = new Class({
	
		//implements
		Implements: [Events, Options],
		
		//options
		options: {
		container: $(document.body),
		boxID:"",
		percentageID:"",
		displayID:"",
		startPercentage: 0,
		displayText: true,
		speed:1,
		step:2,
		allowMore: false
		},
		
		//initialization
		initialize: function(options) {
		//set options
		
		this.setOptions(options);
		//create elements
		this.createElements();
		},
	
		//creates the box and percentage elements
		createElements: function() {
		
		var box = new Element("div", { 
		
		id:this.options.boxID 
		});
		
		var perc = new Element("div", { 
		id:this.options.percentageID, 
		"style":"width:0px;" 
		});
		
	
		perc.inject(box);
		if (this.options.container != null) {
		box.inject(this.options.container);
		if(this.options.displayText) { 
		var text = new Element("div", { 
		id:this.options.displayID 
		});
		text.inject(this.options.container);
		}
		}
		this.set(this.options.startPercentage);
		},
	
		//calculates width in pixels from percentage
		calculate: function(percentage) { 
		return ($(this.options.boxID).getStyle("width").replace("px"," ") * (percentage / 100)).toInt()
		},
		
		//animates the change in percentage
		animate: function(go) {
		
		var run = false;
		if(!this.options.allowMore && go > 100) { 
		go = 100; 
		}
		this.to = go.toInt();
		if($(this.options.percentageID)) {
			$(this.options.percentageID).set("morph", { 
			duration: this.options.speed,
			link:"cancel",
			onComplete: this.fireEvent(go == 100 ? "complete" : "change", [], this.options.speed)
			}).morph({
			width:this.calculate(go)
			});
		}
		if($(this.options.displayID)) {
		if(this.options.displayText) { 
			
		$(this.options.displayID).set("text", 'Backup Progress: ' + this.to + '%'); 
		}
		}},
		
	
		//sets the percentage from its current state to desired percentage
		set: function(to) {
		//var event = new Event(e);alert(event);
		this.animate(to);
		}
		});
</script>