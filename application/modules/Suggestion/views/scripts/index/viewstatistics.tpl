<?php
	  
		  include_once(APPLICATION_PATH ."/application/modules/Seaocore/views/scripts/_invite.tpl");
	?>

<?php 
   //Render invite statistic widget:

  echo $this->content()->renderWidget("Seaocore.seaocores-invitestatistics", array("task" => 'refferal', "invite_type" => 'user_invite'));?>


<script type="text/javascript">

  
window.addEvent('domready', function () { 
  if($('suggestion_invite_statistics'))
    $('suggestion_invite_statistics').addClass('active');

}); 
</script>
     