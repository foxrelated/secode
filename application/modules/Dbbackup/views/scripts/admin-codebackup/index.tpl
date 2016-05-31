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
<script type="text/javascript">
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
</script>
<style type="text/css">
table.admin_table thead tr th ,
table.admin_table tbody tr td {
	padding:7px 6px;
}
</style>
<h2><?php echo $this->translate('Backup and Restore') ?></h2>


  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>


<p><h3><?php echo $this->translate('Files Backup History');?></h3>
  <b><?php echo $this->translate("This page lists all the files backups taken for this site. You can use this page to download the backup files to your computer and delete the backup files.") ?></b><br />
  <?php if(!empty($this->backup_id)) : ?>
  
  <div class="tip" style="margin:10px 0 0;"><span>
  	<?php echo $this->translate('The last files backup was taken '.$this->latesttime)?> ago.
  </span></div> 
  <?php else: ?>

  <?php endif; ?>
</p>
<?php $link = $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'confirm-delete-log'), $this->translate('Delete All Database and File Backups'), array('class' => 'smoothbox buttonlink dbbackup_icon_delete'));?>
<?php $viewlog = $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog'), $this->translate('Delete Selected Database and File Backups'), array('target' => '_blank', 'class' => 'buttonlink dbbackup_icon_delete'));?> 

<?php if( !empty($this->is_filebackup) ) { ?>
<?php if( count($this->paginator) ): ?>
<?php if(!empty($this->logresults)):?>
	<?php echo '<div class="dbbackup_links">';?>
		 <?php echo $this->translate("%1s", "$link");?>
     <?php echo $this->translate("%1s", "$viewlog");?>
  <?php echo '</div>';?>
<?php endif;?>
<table class='admin_table'>
<thead>
  <tr>
    <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
		<?php if($this->order == 'ASC'): ?>
    <th class='admin_table_short'><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'dbbackup', 'order' => 'DESC'), $this->translate('Id')) ?></th>
    <?php else: ?>
       <th class='admin_table_short'><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'dbbackup',  'order' => 'ASC'),$this->translate('Id')) ?></th>
    <?php endif; ?>
    <th style="text-align:left;"><?php echo $this->translate("Filename") ?></th>
    		<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'time','order' => 'DESC'), $this->translate('Date')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'time', 'order' => 'ASC'),$this->translate('Date')) ?></th>
    <?php endif; ?>
   
		<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'method', 'order' => 'DESC'), $this->translate('Method')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'method','order' => 'ASC'),$this->translate('Method')) ?></th>
    <?php endif; ?>
					<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'destinationname', 'order' => 'DESC'), $this->translate('Destination')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'destinationname','order' => 'ASC'),$this->translate('Destination')) ?></th>
    <?php endif; ?>	
    <th style="text-align:left;"><?php echo $this->translate("Type") ?></th>
			<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'time', 'order' => 'DESC'), $this->translate('Age')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'time','order' => 'ASC'),$this->translate('Age')) ?></th>
    <?php endif; ?>	
 			<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'status', 'order' => 'DESC'), $this->translate('Status')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'status','order' => 'ASC'),$this->translate('Status')) ?></th>
    <?php endif; ?>		   
    
    
		<?php if($this->order == 'ASC'): ?>
    <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index', 'id' => 'filesize', 'order' => 'DESC'), $this->translate('Filesize')) ?></th>
    <?php else: ?>
       <th style="text-align:left;"><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'index',  'id' => 'filesize','order' => 'ASC'),$this->translate('Filesize')) ?></th>
    <?php endif; ?>
		<th style="text-align:left;"><?php echo $this->translate("Options") ?></th>

  </tr>
</thead>
<tbody>
<?php $base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<?php foreach( $this->paginator as $item): ?>
          <tr>
          
         <?php $backup_filename1 = strip_tags($item->backup_filename1);
				$backup_filename2 = Engine_String::strlen($backup_filename1) > 15 ? Engine_String::substr($backup_filename1, 0, 15) . '..' : $backup_filename1;
     ?>
     <?php //if(file_exists(APPLICATION_PATH.'/public/'.$this->dir_name_temp.'/'.$backup_filename1)): ?>	
            <td><input type='checkbox' class='checkbox' value="<?php echo $item->dbbackup_id  ?>"/></td>
            <td><?php echo $item->dbbackup_id  ?></td>
             <?php if(file_exists(APPLICATION_PATH.'/public/'.$this->dir_name_temp.'/'.$item->backup_filename1)): ?>
            <td title="Download :<?php echo $item->backup_filename1 ?>">
	            <a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($item->backup_filename1) ? '?path=' . urlencode($item->backup_filename1) : '' ?> target='downloadframe'><?php echo $this->translate($backup_filename2)?>
	            </a>           
            
            </td>
            <?php else: ?>
            <td title="<?php echo $item->backup_filename1 ?>"><?php echo $backup_filename2 ?></td>            
            
            <?php endif; ?>
            
            <td title="<?php echo str_replace("+0000", "", $item->backup_timedescription) ?>" style="white-space:normal;">
            	<div style="width:90px;">
            		<?php echo str_replace("+0000", "", $item->backup_timedescription); ?>
            	</div>
            </td>   
            <?php if(empty($item->backup_auto)):?>
            <td style="text-align:center;" title="<?php if($item->backup_method == 'Download'): ?><?php echo 'Downloaded to computer' ?><?php elseif($item->backup_method == 'Server Backup Directory & Download'): echo 'Backed up on Server & Downloaded to Computer';?><?php else: echo $item->backup_method?><?php endif;?>">
            <?php else: ?>
            <td style="text-align:center;" title="<?php if($item->backup_method == 'Download'): ?><?php echo 'Downloaded to computer' ?><?php elseif($item->backup_method == 'Server Backup Directory & Download'): echo 'Backed up on Server';?><?php else: echo $item->backup_method?><?php endif;?>">
						<?php endif;?>
						<?php if($item->backup_method == 'Download'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/computer.png', '') ?>
            <?php elseif($item->backup_method == 'Server Backup Directory & Download'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/server.png', '') ?>
            <?php else: ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/ftp.png', '') ?>
            <?php  endif;?>
            </td>
             <td title="<?php echo $item->destination_name ?>" style="white-space:normal;"> 
            	<div style="width:100px;text-align:center;overflow:hidden;">	
            		<?php echo $item->destination_name ?> 
            	</div>
            </td>
             <td style="white-space:normal;"> 
            	
            		<?php if(empty($item->backup_auto)):?><?php echo $this->translate('Manual'); ?> <?php else: echo $this->translate('Automatic'); endif; ?> 
            	
            </td>              
              <td style="white-space:normal;">
            	<div style="width:70px;"> 
            		
            		<?php echo Engine_Api::_()->dbbackup()->time_since($item->backup_time); ?>
            	</div>	  
            </td>
            <td> <?php 
           
            if(empty($item->backup_status)) { 
            	echo $this->translate('Failed'); 
            } else {
            	if ( file_exists(APPLICATION_PATH.'/public/'.$this->dir_name_temp.'/'.$item->backup_filename1)  && ($item->backup_method == 'FTP')) {
            		echo $this->translate('Failed');
            	} else {
            		echo $this->translate('Success'); 
            	}}
            	?>  </td>

            <td><?php echo $item->backup_filesize1  ?></td>
            
            <td>
            <?php if(file_exists(APPLICATION_PATH.'/public/'.$this->dir_name_temp.'/'.$item->backup_filename1)): ?>
							<a href=<?php echo $this->url(array('action' => 'download', 'controller' => 'manage')) ?><?php echo !empty($item->backup_filename) ? '?path=' . urlencode($item->backup_filename1) : '' ?> target='downloadframe'><?php echo $this->translate('download')?>
							</a>
						<?php echo ' |'; endif; ?>
                    		
      
            <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-codebackup', 'action' => 'delete', 'id' => $item->dbbackup_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
           </td>
           

          </tr>
                   <?php //else: ?> 

         <?php  //continue; ?>     
         <?php //endif;?> 
<?php endforeach; ?>
</tbody>
</table><br /> 

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>  

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>
<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else:?>
  <div class="tip" style="margin:10px 0 0;">
    <span>
      <?php echo $this->translate("No files backups could be found.") ?>
    </span>
  </div>
<?php endif; } ?>