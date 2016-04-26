<?php 
   
		include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviterwidget.tpl");
	?>
	
	<?php
		include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviteContent.tpl");
		
	
	?>

<script type="text/javascript">
	window.addEvent('domready', function () { 
	  aaf_main_page_invite = true;
	  invite_mainpage_url = '<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), 'friends_suggestions_viewall', true); ?>'; 
	});

</script>	
	
	


