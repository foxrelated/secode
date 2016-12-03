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
<div id="hidefiles" class="form-wrapper" style="border-top:none;padding:0px;margin-top:-15px;">

	<div class="form-label" >
	
		<label>
			&nbsp;
		</label>
	</div>
	<div class="form-element">
		<p class="description">
			
		</p>
		<ul class="form-options-wrapper">
			<li><input type='checkbox' value=1 id='files' name='Uncheck_filesall'  onclick="doCheckAlls();" /> <?php echo 'check all' ?></li>
			<li>
				<?php
					$session = new Zend_Session_Namespace('backup');
					$resultsfiles = $session->resultsfiles;

					foreach($resultsfiles as $values) {
						$name = explode(" ",$values);
						$name = str_replace(".", "_DBBACKUP_DOT_", $name);
						//foreach($values as $result_table) { ?>	
						<div style="clear:both;">
							<input type='checkbox' value=1 onclick='checkboxs();' id='files_all' name=<?php echo $name[0] ?>  /> <?php echo $values ?>
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
	var checkboxcount2 = 0;
	

  function doCheckAlls() {
  	
  	if(checkboxcount2 == 0) {
  		
			$$('.global_form').each(function(elements) {
    	for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox' && elements[i].id != 'backup_includedirectory' && elements[i].id != 'backup_optionsettings' && elements[i].id != 'tables_all' && elements[i].id != 'tables' && elements[i].id != 'fileroots_all' && elements[i].id != 'rootfiles' && elements[i].id != 'filemodules_all' && elements[i].id != 'modulesfiles') {
      elements[i].checked = true;
      
      }}
      checkboxcount2 = checkboxcount2 + 1;
      }
      );
    } 
    else {
     $$('.global_form').each(function(elements) {
  	 for (var i=0; i < elements.length; i++) {
     if (elements[i].type == 'checkbox' && elements[i].id != 'backup_includedirectory' && elements[i].id != 'backup_optionsettings' && elements[i].id != 'tables_all' && elements[i].id != 'tables' && elements[i].id != 'fileroots_all' && elements[i].id != 'rootfiles' && elements[i].id != 'filemodules_all' && elements[i].id != 'modulesfiles') {
     elements[i].checked = false;
     }}
     checkboxcount2 = checkboxcount2 - 1;
     }
     );
     }
  }
  
  function checkboxs() {
  	if($('files').type == 'checkbox') {
  		$('files').checked = false;
  	}
  }
 </script>