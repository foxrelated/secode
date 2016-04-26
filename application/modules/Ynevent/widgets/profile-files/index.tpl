<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>

<!-- Content -->
<div class="ynevent-profile-module-header">
    <div class="ynevent-profile-header-right">
		<?php
			if(count($this->subFolders) > 0):?>
				<?php echo $this->htmlLink(array(
				          'route' => 'event_extended',
				          'controller' => 'file',
				          'action' => 'list',
				          'subject' => $this->subject()->getGuid(),
				          'tab' => $this->identity,
				        ), '<i class="fa fa-list"></i>'.$this->translate('View All Folders'), array(
				          'class' => 'buttonlink'
				      ));?>
			<?php endif;?>
		 <?php if ($this->canCreate) : ?>
				<?php
					echo $this->htmlLink(
						$this->url(
							array(
								'controller' => 'folder', 
								'action' => 'create',
								'parent_type' => $this->parentType,
								'parent_id' => $this->parentId,
								'subject_id' => $this->parentId,
							), 
							'ynfilesharing_general', 
							true
						), '<i class="fa fa-plus-square"></i>'.$this->translate('Create a new folder'),
						array('class' => 'buttonlink')); 
				?>			
		<?php endif;?>
 	</div>

  	<?php if ($this->canCreate) : ?>
  	<div class="ynevent-profile-header-content ynevent_files_totalupload">
  		<span class="ynevent-numeric"><?php echo $this -> totalUploaded; ?></span>
		<?php 
		if($this -> maxSizeKB && $this -> maxSizeKB > 0)
			echo $this -> translate("MB of %s MB used", $this -> maxSizeKB);
		else
			echo $this -> translate("MB of Unlimited");
		?>
	</div>
	<?php endif; ?>
</div>

<?php if (!empty($this->messages)) : ?>
	<ul class="<?php echo empty($this->error)?'ynfs_notices':'ynfs_fail_notices'?>">
		<?php foreach ($this->messages as $mess) : ?>
			<li><?php echo $mess?></li>
		<?php endforeach;?>
	</ul>
<?php endif?>

<?php 
	echo $this->partial(
		'_browse_folders.tpl', 
		'ynevent', 
		array(
			'subFolders' => $this->subFolders,
			'files' => array(),
			'foldersPermissions' => $this->foldersPermissions, 
			'parentType' => $this->parentType,
			'parentId' => $this->parentId,
			'canCreate' => $this->canCreate,
			'canDeleteRemove' => $this->canDeleteRemove
		)
	);
?>