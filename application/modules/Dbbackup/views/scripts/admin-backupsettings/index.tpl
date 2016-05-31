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
.settings #submit-label{
	display:block;
}
#backup_options-element{
	width:650px;
}
.settings form.global_form{
	width:930px;
}
.settings .form-element .description{
	max-width:650px
}
.settings form p.form-description
{
	max-width:900px;
}
</style>
<h2><?php echo $this->translate('Backup and Restore') ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
		<?php if(empty($this->tables) ): ?>
		<?php  $tables_index = $this->tables_temp; ?>
    <?php echo $this->form->render($this); ?>
    <?php endif; ?>
  </div>
  <?php if($this->values['backup_completecode'] == 1 || $this->values['backup_completecode'] == 2 || $this->values['backup_completecode']==0) :?>
	  <?php if( !empty($this->tables)):?>
	    <iframe name='admin_iframe_database_backup' id='admin_iframe_database_backup' src="<?php echo ($this->base_url); ?>/admin/dbbackup/backupsettings/create?backup_options=<?php echo ($this->values['backup_options']); ?>&backup_completecode=<?php echo ($this->values['backup_completecode']); ?>&destination_id=<?php echo $this->destination_id ?>&lockoption=<?php echo $this->lockoption ?>&code_destination_id=<?php echo $this->code_destination_id ?>" scrolling='no' frameborder='0' style='width:800px;height:500px;'></iframe>
		<?php endif; ?>		
  <?php endif; ?>
	<div id="addlink" style="margin-top:10px;">
	<a href="javascript: void(0);" onclick="return advanceOption();" id="advanceOptionLink">
		
		</a>
</div>
</div>
<script type="text/javascript">

window.addEvent('domready', function() {
	showlocation(<?php echo $this->backup_completecodes; ?>);	
	showrootfilesOption(<?php echo $this->backup_rootfiles; ?>);
	showfilesOption(<?php echo $this->backup_files; ?>);
	advanceOption(<?php echo $this->backup_tables; ?>);
	showmodulesfilesOption(<?php echo $this->backup_modulesfiles; ?>);
});

  
//  HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
function showlocation(option) {

	if(option == 0) {
		if($('backup_options-wrapper')) {
			$('backup_options-wrapper').style.display='block';
		}
		if($('destination_id-wrapper')) {
			$('destination_id-wrapper').style.display='none';
		}
		if($('backup_tables-wrapper')) {
			$('backup_tables-wrapper').style.display='none';
		}
	
		if($('dbbackup_tablelock-wrapper')) {
			$('dbbackup_tablelock-wrapper').style.display='none';
		}
		
		
		if($('hide')) {
			$('hide').style.display='none';
		} 
		
	
		
		if($('backup_rootfiles-wrapper')) {
			$('backup_rootfiles-wrapper').style.display='block';
		}
		if($('hiderootfiles') && $('backup_rootfiles-1').checked == false) {
			$('hiderootfiles').style.display='block';
		} 

		if($('backup_files-wrapper')) {
			$('backup_files-wrapper').style.display='block';
		}
		if($('hidefiles') && $('backup_files-1').checked == false) {
			$('hidefiles').style.display='block';
		} 
		
		if($('backup_modulesfiles-wrapper')) {
			$('backup_modulesfiles-wrapper').style.display='block';
		}
		if($('hidemodulefiles') && $('backup_modulesfiles-1').checked == false) {
			$('hidemodulefiles').style.display='block';
		} 	
			
	} 
	else if(option == 1) {
		if($('backup_options-wrapper')) {
			$('backup_options-wrapper').style.display='none';
		}
		if($('destination_id-wrapper')) {
			$('destination_id-wrapper').style.display='block';
		}
		if($('backup_tables-wrapper')) {
			$('backup_tables-wrapper').style.display='block';
		}
		if($('dbbackup_tablelock-wrapper')) {
			$('dbbackup_tablelock-wrapper').style.display='block';
		}
		 

		if($('hiderootfiles')) {
		 $('hiderootfiles').style.display='none';
		}
		if($('backup_rootfiles-wrapper')) {
			$('backup_rootfiles-wrapper').style.display='none';
		}	
		if($('hidefiles')) {
		 $('hidefiles').style.display='none';
		}
		if($('backup_files-wrapper')) {
			$('backup_files-wrapper').style.display='none';
		}		
		
		if($('hidemodulefiles')) {
		  $('hidemodulefiles').style.display='none';
		}		
		if($('backup_modulesfiles-wrapper')) {
			$('backup_modulesfiles-wrapper').style.display='none';
		}			
	} 
	else {
		
		if($('backup_options-wrapper')) {
			$('backup_options-wrapper').style.display='block';
		}
		if($('destination_id-wrapper')) {
			$('destination_id-wrapper').style.display='block';
		}
		if($('backup_tables-wrapper')) {
			$('backup_tables-wrapper').style.display='block';
		}

		if($('dbbackup_tablelock-wrapper')) {
			$('dbbackup_tablelock-wrapper').style.display='block';
		}
		if($('hide') && $('backup_tables-1').checked == false) {
			$('hide').style.display='block';
		} 	
		if($('backup_files-wrapper')) {
			$('backup_files-wrapper').style.display='block';
		}

		if($('hidefiles') && $('backup_files-1').checked == false) {
			$('hidefiles').style.display='block';
		} 
		
		if($('backup_rootfiles-wrapper')) {
			$('backup_rootfiles-wrapper').style.display='block';
		}
		if($('hiderootfiles') && $('backup_rootfiles-1').checked == false) {
			$('hiderootfiles').style.display='block';
		} 
		
		if($('backup_modulesfiles-wrapper')) {
			$('backup_modulesfiles-wrapper').style.display='block';
		}
		if($('hidemodulefiles') && $('backup_modulesfiles-1').checked == false) {
			$('hidemodulefiles').style.display='block';
		} 		
	}
}

function advanceOption(options) {
	
	if($('hide')) {
		if(options == 1) {
			$('hide').style.display='none';
		} 
		else {
			$('hide').style.display='block';
		}
	}
}


function showfilesOption(optionsfiles) {

	if($('hidefiles')) {
		if(optionsfiles == 1) {
			$('hidefiles').style.display='none';
		} 
		else {
			$('hidefiles').style.display='block';
		}
	}
}

function showrootfilesOption(optionsrootfiles) {

	if($('hiderootfiles')) {
		if(optionsrootfiles == 1) {
			$('hiderootfiles').style.display='none';
		} 
		else {
			$('hiderootfiles').style.display='block';
		}
	}
}

function showmodulesfilesOption(optionsmodulefiles) {
	if($('hidemodulefiles')) {
		if(optionsmodulefiles == 1) {
			$('hidemodulefiles').style.display='none';
		} 
		else {
			$('hidemodulefiles').style.display='block';
		}
	}
}

</script>