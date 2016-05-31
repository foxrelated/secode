<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: edit.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */

?>

<script type="text/javascript">
  var fetchDestinationSettings =function(mode){
    window.location.href= en4.core.baseUrl+'admin/dbbackup/destinationsettings/destination/mode/'+mode;
    //alert(level_id);
  }
</script>

<h2><?php echo $this->translate('Backup and Restore') ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='tabs'>
	  <?php
	  //->setUlClass()

	  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	  ?>
	</div>
<?php endif; ?>
<div>


<div class="dbbackup_destination_list">
<?php   echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'dbbackup', 'controller' => 'destinationsettings', 'action'=>'index','show'=>1), $this->translate('Back to Destinations Listing') )
?>
</div>
<br /><br />

<div class='clear'>
  <div class='settings'>

   <?php echo $this->form->render($this); ?>

  </div>

</div>

<script type="text/javascript" >
//HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC BACKUP OR SIMPLE BACKUP OPTIONS.
  window.addEvent('domready', function() {
	showfields(<?php echo  $this->destination_mode; ?>);

	

});

function 	showfields(destination_mode){
	 $('email-wrapper').style.display='none';
   $('ftphost-wrapper').style.display='none';
   $('ftpuser-wrapper').style.display='none';
   $('ftppassword-wrapper').style.display='none';
   $('ftpportno-wrapper').style.display='none';
   $('ftppath-wrapper').style.display='none';
   $('ftpmdb-wrapper').style.display='none';
   $('ftpmfile-wrapper').style.display='none';
   $('ftpadb-wrapper').style.display='none';
 //  $('ftpafile-wrapper').style.display='none';
   $('ftpmsg-wrapper').style.display='none';

   $('dbhost-wrapper').style.display='none';
   $('dbuser-wrapper').style.display='none';
   $('dbpassword-wrapper').style.display='none';
   $('dbname-wrapper').style.display='none';
	 $('ftpdirectoryname-wrapper').style.display='none';
    switch(destination_mode)
    {
    
      case 1:
         $('email-wrapper').style.display='block';
        break;
        case 2:
         $('ftphost-wrapper').style.display='block';
         $('ftpuser-wrapper').style.display='block';
         $('ftppassword-wrapper').style.display='block';
         $('ftpportno-wrapper').style.display='block';
         $('ftppath-wrapper').style.display='block';
         $('ftpdirectoryname-wrapper').style.display='block';
         $('ftpmsg-wrapper').style.display='block';
         $('ftpmdb-wrapper').style.display='block';
         $('ftpmfile-wrapper').style.display='block';
         $('ftpadb-wrapper').style.display='block';
   //      $('ftpafile-wrapper').style.display='block';
        break;
      case 3:
        $('dbhost-wrapper').style.display='block';
        $('dbname-wrapper').style.display='block';
        $('dbuser-wrapper').style.display='block';
        $('dbpassword-wrapper').style.display='block';
      
        break;
    }
}






</script>