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

<h2><?php echo $this->translate('Backup and Restore') ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
		//->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<div class='clear' >
  <div class='settings'>

	 	<?php if(!empty( $this->success) && !Engine_Api::_()->getApi('settings', 'core')->dbbackup_backupoptions):?>
			<ul class="form-notices" >
				<li style="font-size:12px;" >
					<?php echo $this->translate($this->success); ?>
				</li>
			</ul>

		<?php elseif(!empty( $this->error)): ?>
		<ul class="form-notices" >
				<li style="font-size:12px;">
					<?php echo $this->error; ?>
				</li>
			</ul>
		<?php endif; ?>
		
   <?php echo $this->form->render($this); ?>
   
  </div>

</div>
<style type="text/css">
.settings form {float:none;}
.settings .form-description {max-width: none;}
.settings .form-element{max-width:650px;}
.settings .form-element .description {max-width:none;}
</style>
<script type="text/javascript">
	var checkboxcount = 0;
  var display_msg=0;
	//HERE WE CREATE THE FUNCTION FOR CHECK ALL BOXES FOR BACKUP.
	function doCheckAll() {
  	if(checkboxcount == 0) {
			$$('.global_form').each(function(elements) {
    	for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
      );
    } 
    else {
     $$('.global_form').each(function(elements) {
  	 for (var i=0; i < elements.length; i++) {
     if (elements[i].type == 'checkbox') {
     elements[i].checked = true;
     }}
     checkboxcount = checkboxcount - 1;
     }
     );
    }
  }
 
  //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  window.addEvent('domready', function() {
		showautomaticblock(<?php echo $this->autobackupoption; ?>);
		showdeleteblock(<?php echo $this->autodeleteoption; ?>);
		showdeletecodeblock(<?php echo $this->autodeletecodeoption; ?>);  
		display_msg=1;
	});


  //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  function showautomaticblock(option) {
		
  	if(option == 0) {
     if(display_msg==1)
     Smoothbox.open('<div style="margin:5px 10px 0 0;">Please make sure that the time interval between automatic database backups selected by you is atleast more than 5 times the duration taken for a manual backup.Please refer to the Take Backup section for a manual backup.</div><br /> <center> <button  onclick="javascript:parent.Smoothbox.close()">ok</button> </center>');
     if($('dbbackup_dropdowntime-wrapper')) {
			 $('dbbackup_dropdowntime-wrapper').style.display='block';
     }
     if($('dbbackup_mailoption-wrapper'))
     $('dbbackup_mailoption-wrapper').style.display='block';
     if($('dbbackup_lockoptions-wrapper')) {
     	$('dbbackup_lockoptions-wrapper').style.display='block';
     }
     if($('dbbackup_destinations-wrapper'))
     $('dbbackup_destinations-wrapper').style.display='block';
     if($('dbbackup_autofilename-wrapper'))
     $('dbbackup_autofilename-wrapper').style.display='block';
     
     var mail_option=0;
     if( $('dbbackup_mailoption-1').checked==true)
      mail_option=1;
     
      showmailblock(mail_option);
  	} else {
  		if($('dbbackup_autofilename-wrapper')) {
	  		$('dbbackup_autofilename-wrapper').style.display='none';
  		}
  		if($('dbbackup_dropdowntime-wrapper')) {
  		  $('dbbackup_dropdowntime-wrapper').style.display='none';
  		}

  		if($('dbbackup_lockoptions-wrapper')) {
      	$('dbbackup_lockoptions-wrapper').style.display='none';
  		}
  		if($('dbbackup_mailoption-wrapper')) {
      	$('dbbackup_mailoption-wrapper').style.display='none';
  		}
  		if($('dbbackup_destinations-wrapper')) {
	      $('dbbackup_destinations-wrapper').style.display='none';
  		}
  		showmailblock(0);
  	}
  }
  
  //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  function showdeleteblock(option) {
    if($('dbbackup_deletelimit-wrapper')) {
			if(option == 1) {
			$('dbbackup_deletelimit-wrapper').style.display='block';
			} else {
				$('dbbackup_deletelimit-wrapper').style.display='none';
			}
    }
  }

  //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  function showdeletecodeblock(option) {
    if($('dbbackup_deletecodelimit-wrapper')) {
			if(option == 1) {
			$('dbbackup_deletecodelimit-wrapper').style.display='block';
			} 
			else {
				$('dbbackup_deletecodelimit-wrapper').style.display='none';
			}
    }
  }

  //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  function showmailblock(option) {
    if($('dbbackup_mailsender-wrapper')) {
			if(option == 1) { 
				$('dbbackup_mailsender-wrapper').style.display='block';
			} else {
				$('dbbackup_mailsender-wrapper').style.display='none';
			}
    }
  }

</script>

<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>