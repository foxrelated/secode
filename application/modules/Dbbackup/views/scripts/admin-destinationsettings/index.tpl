<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: index.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<style type="text/css">
.settings form{
	width:930px;
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
  margin-top:10px;
  clear:both;
  width:930px;
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
  margin-left: -1px;
	color:#717171;
	font-size:13pt;
	font-weight:bold;
	letter-spacing:-1px;
	margin:0 0 10px 0;
}
.settings .form-description
{
	max-width:900px;
}
.settings .form-element .description{
	max-width:650px;
}
</style>
<h2><?php echo $this->translate('Backup and Restore') ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='tabs'>
	  <?php
	  //->setUlClass()
	  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	  ?>
	</div>
<?php endif; ?>
<div>

<div class='dbbackup_admin_tabs'>
	<ul>
		<li class="active" id='server_directory_temp'>
			<a href="javascript:void(0);" onclick="showdestination_block('server_directory', 'other_server_directory', 'server_directory_temp', 'other_server_directory_temp')" ><?php echo $this->translate('Server Directory') ?></a>
		</li>
		<li id='other_server_directory_temp'>	
			<a href="javascript:void(0);" onclick="showdestination_block('other_server_directory', 'server_directory', 'other_server_directory_temp', 'server_directory_temp')"><?php echo $this->translate('Other Backup Destinations') ?></a>
		</li>
	</ul>		

</div>
		
</div><br /><br /><br />
<?php echo $this->translate('Destinations are the locations where your backup files are saved.') ?>

<div id="server_directory">
<div class='settings' style="margin-top:10px;float:left;">

	<?php if( $this->message == 1):?>
		<ul class="form-notices" >
			<li style="font-size:12px;">
			  <?php $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();?>
				<?php echo  "Your settings have been saved successfully. You may confirm the password protection for your backup directory by <a href='$base_url/public/$this->currentdirectory/password_check.txt' target='_blank'>clicking here</a>."; ?>
			</li>
		</ul>
	<?php elseif ($this->message == 2):?>
		<ul class="form-notices" >
		<li style="font-size:12px;">
		<?php $base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); ?> 
			<?php echo "Your settings have been saved successfully.";?>
			
		</li>
	</ul>
	
	<?php elseif(!empty( $this->error)): ?>
	<ul class="form-errors" >
			<li style="font-size:12px;">
				<?php echo $this->error; ?>
			</li>
		</ul>
	<?php endif; ?>
	 <?php echo $this->form->render($this); ?>
</div>
</div>

<div id='other_server_directory' style="display:none;">
<div class="dbbackup_form">
	<div class="dbbackup_form_inner">
		<h3>
			<?php echo $this->translate('Other Backup Destinations') ?>
		</h3>
	 		<p class="form-description">
 		<?php //echo $this->translate('Destinations are the locations where you can save your database backups. There are 3 types of locations : Email, FTP Server and MySQL Database. FTP Server, Email and MySQL Database destinations can be used for saving database backups.') ?> 
	 	<?php echo $this->translate('Destinations are the locations where you can save your database and file backups. There are 3 types of locations : Email, FTP Server and MySQL Database. FTP Server destinations can be used for saving both database and file backups, whereas Email and MySQL Database locations can be used for saving database backups.') ?>
	 		</p>
 		<div class="dbbackup_create_destination">
	  	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'destinationsettings', 'action' => 'destination'), $this->translate('Create a Destination'))	?>
		</div>
		<div class='clear' style="margin-top:10px;float:left;clear:both;">
	  	<div class='settings'>
		    <?php if (count($this->paginator) != 0): ?>
		      <table class='admin_table' style="width:890px;" >
		        <thead>
		          <tr>
		            <th style="text-align:left;"> <?php echo $this->translate('Name') ?></th>
		            <th style="text-align:left;"> <?php echo $this->translate('Type') ?> </th>
		            <th style="text-align:left;"> <?php echo $this->translate('Location') ?> </th>
		            <th style="text-align:center;">
		              <?php echo $this->translate('Options') ?>
		            </th>
		          </tr>
		        </thead>
				    <tbody>
				    	<?php foreach ($this->paginator as $item): ?>
				      	<tr>
			            <td title="<?php echo $item->destinationname ?>"style="white-space:normal;">
			            	<div style="width:70px;overflow:hidden;">
			            		<?php echo $item->destinationname ?>
			            	</div>
			          	</td>
			          	<td>
				            <?php
				            switch ($item->destination_mode) {
				            
				              case 1:  echo $this->htmlImage('application/modules/Dbbackup/externals/images/email.png', '',array('title'=> $this->translate('Email')));
				                break;
				              case 2:  echo $this->htmlImage('application/modules/Dbbackup/externals/images/ftp.png', '',array('title'=> $this->translate('FTP Directory')));
				                break;
				              case 3:  echo $this->htmlImage('application/modules/Dbbackup/externals/images/database.png', '',array('title'=> $this->translate('MySQL Database')));
				                break;
				            } ?>
				
				          </td>
				          <td>
				            <div title="<?php
				            switch ($item->destination_mode) {
				              case 0: echo "$item->dbbackup_directoryname";
				                break;
				              case 1: echo "$item->email";
				                break;
				              case 2: echo "ftp://$item->ftpuser@$item->ftphost:$item->ftpportno/$item->ftppath";
				                break;
				                 
				              case 3:   echo "mysqli://$item->dbuser@$item->dbhost/$item->dbname";
				                break;
				            } ?>" style="width:400px;">
					            <?php
					            switch ($item->destination_mode) {
					              case 0: echo "$item->dbbackup_directoryname";
					                break;
					              case 1: echo "$item->email";
					                break;
					              case 2: echo substr(strip_tags( "ftp://$item->ftpuser@$item->ftphost:$item->ftpportno/$item->ftppath"), 0, 80); if (strlen("ftp://$item->ftpuser@$item->ftphost:$item->ftpportno/$item->ftppath")>80) echo "...";
					                break;
					                 echo substr(strip_tags( "mysqli://$item->dbuser@$item->dbhost/$item->dbname"), 0, 80); if (strlen("mysqli://$item->dbuser@$item->dbhost/$item->dbname")>80) echo "...";
					              case 3:   echo substr(strip_tags( "mysqli://$item->dbuser@$item->dbhost/$item->dbname"), 0, 80); if (strlen("mysqli://$item->dbuser@$item->dbhost/$item->dbname")>80) echo "...";
					                break;
					            } ?>
					            </div>
				          </td>
		
		           		<td class="admin_table_centered">
				            <?php if($item->destination_mode!=0):
				            echo $this->htmlLink(
				                    array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'destinationsettings', 'action' => 'edit', 'id' => $item->destinations_id ,'show' =>1),
				                    $this->translate('Edit'),
				                    array()
				            );
				          endif;
				            ?>
		              	|
				            <?php
				            echo $this->htmlLink(
				                    array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'destinationsettings', 'action' => 'delete', 'id' => $item->destinations_id , 'show' =>1),
				                    $this->translate('Delete'),
				                    array('class' => 'smoothbox')
				            )
				            ?>
		          		</td>
								</tr>
							<?php endforeach; ?>
		      	</tbody>
		      </table>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
//HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  window.addEvent('domready', function() {
	showhide(<?php echo $this->backup_enable; ?>);
	
	var show_block = '<?php echo $this->destination_block; ?>'

	if(show_block == 1) {
		$('other_server_directory').style.display='block';
		$('server_directory').style.display='none';
	}
	else {
		$('other_server_directory').style.display='none';
		$('server_directory').style.display='block';
	}
	if(show_block == 1) {
	showdestination_block('other_server_directory', 'server_directory', 'other_server_directory_temp', 'server_directory_temp')
	} else {
		showdestination_block('server_directory', 'other_server_directory', 'server_directory_temp', 'other_server_directory_temp')
	}
});

function showhide(option) {

	if(option == 1) {

	 $('htusername-wrapper').style.display='block';
	 $('htpassword-wrapper').style.display='block';
	} 
	else {

	 $('htusername-wrapper').style.display='none';
	 $('htpassword-wrapper').style.display='none';
	}
}


function showdestination_block(showid, hideid, id1, id2) {
	
  $(id1).set('class', 'active');
  $(id2).erase('class'); 
	$(showid).style.display='block';
	$(hideid).style.display='none';

}




</script>