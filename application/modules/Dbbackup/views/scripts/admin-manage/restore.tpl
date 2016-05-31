<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: restore.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<style type="text/css">
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
  margin:10px 0;
  float:left;
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
table.dbbackup_table{
	margin-top:5px;
	border:1px solid #EEEEEE;
	border-bottom:none;
	width:400px;
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
</style>
<h2><?php echo $this->translate('Backup and Restore') ?></h2>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  if ($this->flage != 0)
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
<?php //endif;  ?>

  <div class='clear'>
    <div class='settings'>
			<?php if (!empty($this->success)): ?>
				<div class="dbbackup_form">
					<div class="dbbackup_form_inner">
						<h3><?php echo $this->translate("Restore Completed Successfully"); ?></h3>
						<div class="dbbackup_success">
							<?php echo $this->translate("Congratulations! Your database has been successfully restored."); echo "<br />" ?>
			      </div>

			      <div style="clear:both;font-weight:bold;"><?php echo $this->translate("Database restore information"); ?></div> 
						<table class="dbbackup_table">
						  <tr>
						    <td>
						    	<u>Time taken</u>: <?php echo $this->translate("$this->duration"); ?>
						  	</td>	
						  </tr>
						</table>  
			      
		      </div>
	      </div>
			<?php endif; ?>

        <?php if ($this->flage == 1): ?>
		    <?php echo $this->form->render($this); ?>
        <?php endif; ?>
  	</div>
	</div>
<script language="javascript" type="text/javascript">
function hidebutton() {
	if(document.getElementById('submit_button'))
		document.getElementById('submit_button').style.display='none';
	if(document.getElementById('loading_img'))
		document.getElementById('loading_img').style.display='block';
}

function showlightbox() {
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}
</script>





<div id="light" class="dbbackup_white_content">
	<?php echo $this->translate('Restoring'); ?>
	<img src="application/modules/Dbbackup/externals/images/backup-restore.gif" alt="" style="vertical-align:middle;margin-left:10px;" />
</div>
<div id="fade" class="dbbackup_black_overlay"></div>