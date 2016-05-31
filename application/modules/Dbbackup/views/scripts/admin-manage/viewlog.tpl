<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: viewlog.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Backup and Restore') ?></h2>

<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()

  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<h3> <?php echo $this->translate('Backup Logs') ?></h3>
<h4><?php echo $this->translate(' Backup logs enable you to concisely see all the backups taken for your site. You can see the status of the various backups and other parameters like filesize, destination, etc. You can filter the logs based on the backup type and mode, and old logs can also be deleted.') ?> </h4><br />
<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
        <select id="log_type" name="type">
          <option value="0" ></option>
          <option value="1" <?php if( $this->type == 1) echo "selected";?>><?php echo $this->translate("Database") ?></option>
          <option value="2" <?php if( $this->type == 2) echo "selected";?>><?php echo $this->translate("File") ?></option>
         </select>
      </div>
      <div>
        <select id="log_length" name="method">
          <option value="0" ></option>
          <option value="1"  <?php if( $this->method == 1) echo "selected";?>><?php echo $this->translate("Manual") ?></option>
          <option value="2"  <?php if( $this->method == 2) echo "selected";?>><?php echo $this->translate("Automatic") ?></option>
       </select>
      </div>
			<div>
        <select id="log_status" name="status">
          <option value="0" ></option>
          <option value="1"  <?php if( $this->status == 1) echo "selected";?>><?php echo $this->translate("Success") ?></option>
          <option value="2"  <?php if( $this->status == 2) echo "selected";?>><?php echo $this->translate("Fail") ?></option>
       </select>
      </div>
      <div>
        <button type="submit" name="submit" ><?php echo $this->translate("View Log") ?></button>
        <button  type="submit" name="clear" onclick="emptyLog();return false;"><?php echo $this->translate("Empty Log") ?></button>
      </div>
    </form>
  </div>
</div>
<?php $link = $this->htmlLink(
            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'confirm-delete-log'), $this->translate('Delete All Database and File Backups'), array('class' => 'smoothbox buttonlink dbbackup_icon_delete'));?>
<div class='clear'>
  <br />
  <div class='settings'>
<?php $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;?>
 <?php if( !empty($this->is_dbbackup_viewlog) ) { ?>
<?php if(count($this->paginator)>0): ?>
		<div class="dbbackup_links">
			<?php echo $this->translate("%1s", "$link");?>
		</div>
    <table class='admin_table'>
      <thead>
        <tr>
          
          <?php if($this->order == 'ASC'): ?>
			    <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'type', 'order' => 'DESC'), $this->translate('Backup Type')) ?></th>
			    <?php else: ?>
			       <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'type',  'order' => 'ASC'),$this->translate('Backup Type')) ?></th>
			    <?php endif; ?>
<?php if($this->order == 'ASC'): ?>
			    <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'method', 'order' => 'DESC'), $this->translate("Mode")) ?></th>
			    <?php else: ?>
			       <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'method',  'order' => 'ASC'),$this->translate("Mode")) ?></th>
			    <?php endif; ?>
          <?php if($this->order == 'ASC'): ?>
			    <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'destination_method', 'order' => 'DESC'), $this->translate('Destination')) ?></th>
			    <?php else: ?>
			       <th class='admin_table_short'><?php echo $this->htmlLink(
			            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'destination_method',  'order' => 'ASC'),$this->translate('Destination')) ?></th>
			    <?php endif; ?>
          
						<?php if($this->order == 'ASC'): ?>
				    <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'destination_method', 'order' => 'DESC'), $this->translate('Method')) ?></th>
				    <?php else: ?>
				       <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'destination_method',  'order' => 'ASC'),$this->translate('Method')) ?></th>
				    <?php endif; ?>
	          <th>
            <?php echo $this->translate('File'); ?>
          </th>
						<?php if($this->order == 'ASC'): ?>
				    <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'size', 'order' => 'DESC'), $this->translate('Size')) ?></th>
				    <?php else: ?>
				       <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'size',  'order' => 'ASC'),$this->translate('Size')) ?></th>
				    <?php endif; ?>

	<?php if($this->order == 'ASC'): ?>
				    <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'start_time', 'order' => 'DESC'), $this->translate('Start')) ?></th>
				    <?php else: ?>
				       <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'start_time',  'order' => 'ASC'),$this->translate('Start')) ?></th>
				    <?php endif; ?>
	<?php if($this->order == 'ASC'): ?>
				    <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'end_time', 'order' => 'DESC'), $this->translate('Finish')) ?></th>
				    <?php else: ?>
				       <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'end_time',  'order' => 'ASC'),$this->translate('Finish')) ?></th>
				    <?php endif; ?>
						<?php if($this->order == 'ASC'): ?>
				    <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'status', 'order' => 'DESC'), $this->translate('Status')) ?></th>
				    <?php else: ?>
				       <th class='admin_table_short'><?php echo $this->htmlLink(
				            array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'viewlog', 'id' => 'status',  'order' => 'ASC'),$this->translate('Status')) ?></th>
				    <?php endif; ?>
            <th class='admin_table_short'><?php echo $this->translate('Options');?></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($this->paginator as $item): ?>
              <tr>
                <td>
            <?php echo $this->translate($item->type); ?>
            </td>
            <td>
            <?php echo $this->translate($item->method); ?>
            </td>
            <td style="white-space:normal;" title="<?php echo $item->destination_name ?>">
            	<div style="width:100px;text-align:center;overflow:hidden;">
            	 <?php echo $this->translate($item->destination_name); ?>
            	</div>
            </td>
            <?php if($item->method == 'Automatic'): ?>
             <td style="text-align:center;" title="<?php if($item->destination_method == 'Download'): ?><?php echo 'Downloaded to computer' ?><?php elseif($item->destination_method == 'Server Backup Directory & Download'): echo 'Backed up on Server';?><?php else: echo $item->destination_method?><?php endif;?>">
            <?php else: ?>
						 <td style="text-align:center;" title="<?php if($item->destination_method == 'Download'): ?><?php echo 'Downloaded to computer' ?><?php elseif($item->destination_method == 'Server Backup Directory & Download'): echo 'Backed up on Server & Downloaded to Computer';?><?php else: echo $item->destination_method?><?php endif;?>">   
						<?php endif; ?>         
						<?php if($item->destination_method == 'Download'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/computer.png', '') ?>
            <?php elseif($item->destination_method == 'Server Backup Directory & Download'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/server.png', '') ?>
            <?php elseif($item->destination_method == 'Email'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/email.png', '') ?>
            <?php elseif($item->destination_method == 'Database'): ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/database.png', '') ?>
            <?php else: ?>
            <?php echo $this->htmlImage('application/modules/Dbbackup/externals/images/ftp.png', '') ?>
            <?php  endif;?>
            </td>
            <td title="<?php echo $this->translate($item->filename) ?> ">
            <?php echo $this->translate(Engine_String::strlen($item->filename) > 22 ? Engine_String::substr($item->filename, 0, 22) . '..' : $item->filename); ?>
            </td>
            <td>
            
            <?php if($item->destination_method != 'Database'):  echo $this->translate($item->size);  else:  echo $this->translate('N.A.'); endif; ?>
            </td>
            <td title="<?php echo str_replace("+0000", "", $item->start_time) ?>" style="white-space:normal;">
              <div style="width:90px;">
              <?php echo $this->translate(str_replace("+0000", "", $item->start_time)); ?>
            </div>
          </td>
          <td title="<?php echo str_replace("+0000", "", $item->end_time) ?>" style="white-space:normal;">
            <div style="width:90px;">
					<?php if(!empty($item->end_time)):?>
						<?php echo $this->translate(str_replace("+0000", "", $item->end_time)); ?>
					<?php  else:?>
						<?php echo $this->translate('N.A.'); ?>
					<?php  endif; ?>
            </div>
          </td>
					<td>
						<?php echo $this->translate($item->status); ?>
					</td>

          <td>
            <?php if(file_exists(APPLICATION_PATH.'/public/'.$dir_name_temp.'/'.$item->filename)):   
						echo	$this->htmlLink(
							array('route' => 'default', 'module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'delete-file', 'id' => $item->backuplog_id),
							$this->translate("delete"),
							array('class' => 'smoothbox')); 
           ?>
           <?php  else:?>
             <?php echo $this->translate('-'); ?>
           <?php  endif; ?>
          </td>
          </tr>
        <?php endforeach; ?>
            </tbody>
          </table>

        </div>

      </div>
      <div>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php    else : ?>
  <div class="tip" style="margin:10px 0 0;">
    <span>
      <?php echo $this->translate("No log entries could be found.") ?>
    </span>
  </div>
              
        <?php endif; } ?>

</div>




<script type="text/javascript">

function emptyLog() {

   Smoothbox.open('<?php echo $this->url(array('module' => 'dbbackup', 'controller' => 'admin-manage', 'action' => 'confirm-delete-log'), 'default') ?>');
return true;
}



</script>