<?php ?>
<div style="display:none" id="myid">
	<div class="suggestion-help-popup">
		<ul>
			<li>
				<a href='javascript: toggle("id_outlook")'><?php echo $this->translate('Microsoft Outlook'); ?></a>
				<ul style="display:none;" id="id_outlook">
					<li>	
						<?php echo $this->translate('To export a CSV file from Microsoft Outlook:'); ?>						
						<ol>
							<li><?php echo $this->translate('1. Open Outlook'); ?></li>
							<li><?php echo $this->translate("2. Go to File menu and select 'Import and Export'"); ?></li>
							<li><?php echo $this->translate("3. In the wizard window that appears, select 'Export to a file' and click 'Next'"); ?></li>
							<li><?php echo $this->translate("4. Select 'Comma separated values (Windows)' and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("6. Ensure that the checkbox next to 'Export..' is checked and click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_microsoftoutlook")'><?php echo $this->translate('Microsoft Outlook Express'); ?></a>
				<ul style="display:none" id="id_microsoftoutlook">
					<li>
						<?php echo $this->translate('To export a CSV file from Microsoft Outlook Express:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Outlook Express'); ?></li>
							<li><?php echo $this->translate("2. Go to File menu and select 'Export', and then click 'Address Book'"); ?></li>
							<li><?php echo $this->translate("3. Select 'Text File (Comma Separated Values)', and then click 'Export'"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
		    <a href='javascript: toggle("id_mozila_thunder")'><?php echo $this->translate('Mozilla Thunderbird'); ?></a>
				<ul style="display:none" id="id_mozila_thunder">
					<li>
						<?php echo $this->translate('To export a CSV file from Mozilla Thunderbird:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Mozilla Thunderbird'); ?></li>
							<li><?php echo $this->translate("2. Go to Tools menu and select 'Address Book'"); ?></li>
							<li><?php echo $this->translate("3. In the 'Address Book' window that opens, select 'Export...' from the Tools menu"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported file, choose 'Comma Separated (*.CSV)' under the 'Save as type' dropdown list, choose a name for your file (example : mycontacts.csv) and click 'Save'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_linkedin")'><?php echo $this->translate('LinkedIn'); ?></a>
				<ul style="display:none" id="id_linkedin">
					<li>
						<?php echo $this->translate('To export a CSV file from LinkedIn:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Sign into your LinkedIn account'); ?></li>
							<li><?php echo $this->translate('2. Visit the'); ?> <a href='http://www.linkedin.com/addressBookExport' target="_blank"><?php echo $this->translate('Address Book Export'); ?></a><?php echo $this->translate(' page'); ?></li>
							<li><?php echo $this->translate("3. Select 'Microsoft Outlook (.CSV file)' under the 'Export to' dropdown list and click 'Export'"); ?></li>
							<li><?php echo $this->translate('4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv).'); ?></li>
						</ol>
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_windowabook")'><?php echo $this->translate('Windows Address Book'); ?></a>
				<ul style="display:none" id="id_windowabook">
					<li>
						<?php echo $this->translate('To export a CSV file from Windows Address Book:'); ?>
					<ol>
							<li><?php echo $this->translate('1. Open Windows Address Book'); ?></li>
							<li><?php echo $this->translate("2. Go to the File menu, select 'Export', and then select 'Other Address Book...'"); ?></li>
							<li><?php echo $this->translate("3. In the 'Address Book Export Tool' dialog that opens, select 'Text File (Comma Separated Values)' and click 'Export'"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
							<li><?php echo $this->translate("6. Click 'OK' and then click 'Close'"); ?></li>
						</ol>
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_macos")'><?php echo $this->translate('Mac OS X Address Book'); ?></a>
				<ul style="display:none" id="id_macos">
					<li>
					<?php echo $this->translate('To export a CSV file from Mac OS X Address Book:'); ?>
					
						<ol>
							<li><?php echo $this->translate('1. Download the free Mac Address Book exporter from'); ?> <a href='http://www.apple.com/downloads/macosx/productivity_tools/exportaddressbook.html' target="_blank">here</a>.</li>
							<li><?php echo $this->translate('2. Choose to export your Address Book in CSV format.'); ?></li>
							<li><?php echo $this->translate('3. Save your exported address book in CSV format.'); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_palmdesktop")'><?php echo $this->translate('Palm Desktop'); ?></a>
				<ul style="display:none" id="id_palmdesktop">
					<li>
						<?php echo $this->translate('To export a CSV file from Palm Desktop:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Palm Desktop'); ?></li>
							<li><?php echo $this->translate("2. Click on the 'Addresses' icon on the lefthand side of the screen to display your contact list"); ?></li>
							<li><?php echo $this->translate("3. Go to the File menu, select 'Export'"); ?></li>
							<li><?php echo $this->translate('4. In the dialog box that opens, do the following:'); ?></li>
							<li><?php echo $this->translate("5. Enter a name for the file you are creating in the 'File name:' field"); ?></li>
							<li><?php echo $this->translate("6. Select 'Comma Separated' in the 'Export Type' pulldown menu"); ?></li>
							<li><?php echo $this->translate("7. Be sure to select the 'All' radio button from the two 'Range:' radio buttons"); ?></li>
							<li><?php echo $this->translate("8. In the second dialog box: 'Specify Export Fields' that opens, leave all of the checkboxes checked, and click 'OK'."); ?></li>
						</ol>
					</li>
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_windowmail")'><?php echo $this->translate('Windows Mail'); ?></a>
				<ul style="display:none" id="id_windowmail">
					<li>
						<?php echo $this->translate('To export a CSV file from Windows Mail:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Windows Mail'); ?></li>
							<li><?php echo $this->translate('2. Select: Tools | Windows Contacts... from the menu in Windows Mail'); ?></li>
							<li><?php echo $this->translate("3. Click 'Export' in the toolbar"); ?></li>
							<li><?php echo $this->translate("4. Make sure CSV (Comma Separated Values) is highlighted, then click 'Export'"); ?></li>
							<li><?php echo $this->translate("5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("6. Click 'Save' then click 'Next'"); ?></li>
							<li><?php echo $this->translate('7. Make sure all address book fields you want included are checked'); ?></li>
							<li><?php echo $this->translate("8. Click 'Finish'"); ?></li>
							<li><?php echo $this->translate("9. Click 'OK' then click 'Close'"); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_othermail")'><?php echo $this->translate('For Other'); ?></a>
				<ul style="display:none" id="id_othermail">
					<li>
						<?php echo $this->translate('Many email services, email applications, address book management applications allow contacts to be imported to a file. We support .CSV and .TXT types of contact files'); ?>
					</li>	
				</ul>
			</li>	
			<script type="text/javascript">
			function toggle(divid){
  			  var MyidHTML =  $('myid').innerHTML;
  			  $('myid').innerHTML = '';
  				var div1 = $(divid);
  				if (div1.style.display == 'none') {
  					div1.style.display = 'block'
  				} else {
  					div1.style.display = 'none'
  				}
  				 $('myid').innerHTML = MyidHTML;
  			}
			</script>
		</ul>
		
	</div>	
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close'); ?></button>
</div>
    <?php
    if(!empty($this->paginator)){
    foreach( $this->paginator as $search_result ): 
	    echo $this->htmlLink($search_result->getHref(), $this->itemPhoto($search_result, 'thumb.icon'), array('class' => 'popularmembers_thumb'));
			echo $this->htmlLink($search_result->getHref(), $search_result->getTitle());
    endforeach; 
    echo $this->paginationControl($this->paginator);
    }
   

if ($this->user_id) { 
  ?>
<form action="" id="id_myform_temp" name="id_myform_temp">

</form>
<?php } ?>
<?php
$session = new Zend_Session_Namespace();
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/usercontacts.js');
$this->headTranslate(array('Importing Contacts', 'Sending Request', 'Uploading file'));
?>

<?php 
$googleredirect = $session->googleredirect;
$yahooredirect = $session->yahooredirect;
$aolredirect = $session->aolredirect;
$windowlivemsnredirect = $session->windowlivemsnredirect;
$facebookredirect =$session->facebookredirect;
$linkedinredirect =$session->linkedinredirect;
$twitterredirect = $session->twitterredirect;


if (!empty($session->filename)) {
  $csv_filename = $session->filename;
}
else {
  $csv_filename = '';
}

?>

<script type="text/javascript">
var invite_callbackURl = '<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), 'friends_suggestions_viewall', true);?>';
//RETRIVING THE VALUE FROM SESSION AND CALL THE CORROSPONDING ACTION FOR WHICH SERVICE IS BEING CURRENTLY EXECUTING.
var googleredirect = '<?php echo $googleredirect;?>';
var yahooredirect = '<?php echo $yahooredirect;?>';
var aolredirect = '<?php echo $aolredirect;?>';
var windowliveredirect = '<?php echo $windowlivemsnredirect;?>';
var facebookredirect = '<?php echo $facebookredirect;?>';
var linkedinredirect = '<?php echo $linkedinredirect;?>';
var twitterredirect = '<?php echo $twitterredirect;?>';
window.addEvent('load', function () {
	
	if (facebookredirect == 1 && window.opener!= null) {
  show_contacts_Facebook (0);
}
	
	});
if (googleredirect == 1 && window.opener!= null) { 
  show_contacts_google (0);
}
else if (yahooredirect == 1 && window.opener!= null) {
	show_contacts_yahoo (0);
}
else if (aolredirect == 1 && window.opener!= null) {
	show_contacts_aol (0);
}
else if (windowliveredirect == 1 && window.opener!= null) {
  show_contacts_windowlive (0);
}

else if (linkedinredirect == 1 && window.opener!= null) { 
  show_contacts_linkedin (0);
}
else if (twitterredirect == 1 && window.opener!= null) { 
  show_contacts_Twitter (0);
}
if (window.opener == null && googleredirect == 1) {
<?php if (isset($_GET['token'])) : ?>
      googleredirect = 0;
      get_contacts_google('<?php echo $_GET['token'];?>');
      
  <?php endif;?>  
}
else if (window.opener == null && yahooredirect == 1) { 
        yahooredirect = 0;
  <?php if (isset($_GET['oauth_verifier'])) : ?>
      get_contacts_yahoo('<?php echo $_GET['oauth_verifier'];?>');
      
  <?php endif;?> 
}
else if  (window.opener == null && windowliveredirect == 1) { 
  windowliveredirect = 0; 
  get_contacts_windowlive();
   
  
}

else if (aolredirect == 1 && window.opener == null) {
<?php if (isset($_GET['redirect_aol'])) : ?>
  aolredirect = 0;
	get_contacts_aol (0);
	 <?php endif;?> 
}
else if  (window.opener == null && facebookredirect == 1) {  
  <?php if (isset($_GET['redirect_fbinvite'])) : ?>
  facebookredirect = 0;
  window.addEvent('load', function() { 
	  
	     setTimeout('get_contacts_Facebook();', '1000');
	  
	  });
   <?php endif;?>  
  
}

else if (window.opener == null && linkedinredirect == 1) { 
        linkedinredirect = 0;
  <?php if (isset($_GET['oauth_verifier'])) : ?>
      get_contacts_linkedin('<?php echo $_GET['oauth_verifier'];?>');
      
  <?php endif;?>
  
}

else if (window.opener == null && twitterredirect == 1) { 
        twitterredirect = 0;
  <?php if (isset($_GET['redirect_tweet'])) : ?>
      get_contacts_twitter('<?php echo $_GET['redirect_tweet'];?>');
      
  <?php endif;?>
  
}


<?php if (!empty($_GET['csv_filename']) && !empty($session->filename)) :?> 
  fileext = true;
  showhide('id_show_networkcontacts', 'id_csvcontacts')
  getcsvcontacts('<?php echo $_GET['csv_filename'];?>');

<?php endif;?>
function show_services() {
	 var supported_services = '<div class="suggestion-mail-supported"> <h2><?php echo $this->string()->escapeJavascript($this->translate("Supported Services")) ?></h2><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/outlook.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" Microsoft Outlook ")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/outlookexpress.gif" alt="" /><?php echo $this->string()->escapeJavascript($this->translate(" Microsoft Outlook Express ")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/thunderbird.png" alt="" /><?php echo $this->string()->escapeJavascript($this->translate(" Mozilla Thunderbird ")) ?> <br / ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/linkedin16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" LinkedIn ")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/windowslive16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" Windows Address Book ")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/addressbook.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" Mac OS X Address Book ")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/palm.gif" width="16" alt="" /><?php echo $this->string()->escapeJavascript($this->translate(" Palm Desktop ")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/windowslive16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" Windows Mail ")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/plus.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate(" Other ")) ?><br /><br /><button onclick="javascript:close_popup();"><?php echo $this->string()->escapeJavascript($this->translate("Close")) ?></button></div>';
	Smoothbox.open( supported_services);
 }

 function show_createfile() {
  if ($('myid').innerHTML != '') {
		var howToCreateFile = $('myid').innerHTML;
	}
	Smoothbox.open(howToCreateFile);
	
 }

  <?php if (empty ($this->user_id)) { ?> 
  	window.addEvent('domready', function() {
  		
  		$('sub-title').style.display = 'block';
  		$('sub-txt').style.display = 'block';
  		$('webacc-logos').style.display = 'block';
  		$('skipinviterlink').style.display = 'block';
  		$('invite_info').style.display = 'block';
  		$('header_title').style.display = 'block';
   
   		$('inviter_form1').style.display = 'none';
   		$('inviter_form2').style.display = 'none';
   
   		$('help_link2').style.display = 'block';
   		$('help_link1').style.display = 'none';
   	});

 	<?php } ?>
	function close_popup () {
	Smoothbox.close();
	}
</script>