<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: _formCheckbox.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<style type="text/css">
.form-options-wrapper li{
	padding:0px;
}
</style> 
<div id="hiderootfiles" class="form-wrapper" style="border-top:none;padding:0px;margin-top:-15px;">

	<div class="form-label" >
	
		<label>
			&nbsp;
		</label>
	</div>
	<div class="form-element">
		<p class="description">
			
		</p>
		<ul class="form-options-wrapper">
			<li><input type='checkbox' value=1 id='rootfiles' name='Uncheck_rootfilesall'  onclick="doCheckAllFiles();" /> <?php echo 'Check all' ?></li>
			<li>
				<?php
					$session = new Zend_Session_Namespace('backup');
					$resultsrootfiles = $session->resultsrootfiles;
					foreach($resultsrootfiles as $values) {
						$name = explode(" ",$values);
            $name = str_replace(".", "_DBBACKUP_DOT_", $name);
						//foreach($values as $result_table) { ?>	
						<div style="clear:both;">
							<input type='checkbox' value=1 onclick="checkboxroots('<?php echo $values ?>', this.checked);" id='fileroots_all' name="<?php echo $name[0] ?>"   /> <?php echo $values ?>
						</div>
							<?php
						}
					//}
				?>
			</li>
		</ul>	
	</div>
</div>
<script type="text/javascript">
	var checkboxcount1 = 0;
	

  function doCheckAllFiles() {
	  if($('hidemodulefiles')) {
			$('hidemodulefiles').style.display='none';
		}		
		if($('backup_modulesfiles-wrapper')) {
			$('backup_modulesfiles-wrapper').style.display='none';
		}	
  	if(checkboxcount1 == 0) {
			$$('.global_form').each(function(elements) {
    	for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox' && elements[i].id != 'backup_includedirectory' && elements[i].id != 'backup_optionsettings' && elements[i].id != 'tables_all' && elements[i].id != 'tables' && elements[i].id != 'files_all' && elements[i].id != 'files' && elements[i].id != 'filemodules_all' && elements[i].id != 'modulesfiles') {
      elements[i].checked = true;
      
      }}
      checkboxcount1 = checkboxcount1 + 1;
      }
      );
    } 
    else {
    	
     $$('.global_form').each(function(elements) {
  	 for (var i=0; i < elements.length; i++) {
     if (elements[i].type == 'checkbox' && elements[i].id != 'backup_includedirectory' && elements[i].id != 'backup_optionsettings' && elements[i].id != 'tables_all' && elements[i].id != 'tables' && elements[i].id != 'files_all' && elements[i].id != 'files' && elements[i].id != 'filemodules_all' && elements[i].id != 'modulesfiles') {
     elements[i].checked = false;
     }}
     checkboxcount1 = checkboxcount1 - 1;
     }
     );
     }
  }
  
  function checkboxroots(directoryname, checked) {  	
  	if(directoryname == 'application') {
  		if(checked == true) {
				if($('hidemodulefiles')) {
	  			$('hidemodulefiles').style.display='none';
				}		
				if($('backup_modulesfiles-wrapper')) {
					$('backup_modulesfiles-wrapper').style.display='none';
				}	
  		} else {
				if($('hidemodulefiles')) {
					if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('backup_modulesfiles', 1); ?>')
	  			$('hidemodulefiles').style.display='none';
	  			else
	  			$('hidemodulefiles').style.display='block';
				}		
				if($('backup_modulesfiles-wrapper')) {
					$('backup_modulesfiles-wrapper').style.display='block';
				}	  			
  		}
  	}
  	if($('rootfiles').type == 'checkbox') {
  		$('rootfiles').checked = false;
  	}
  }
  
  

  
 </script>