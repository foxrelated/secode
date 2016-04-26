<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Likes Plugin & Widgets') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
      //RENDEDR THE FORM		
      echo $this->form->render($this);
    ?>
  </div>
</div>
<script type="text/javascript">
  var content_type = '<?php echo $this->content_type;?>';
  //CHECK THA VALIDATION FOR THE BCONTENT TYPE  
  if (content_type != '') 
  {
    //HERE WE CREATE A FUNCTION FOR SHOWING THE DROPDOWN BLOCK OF AUTOMATIC TAB SHOW OR SIMPLE TAB SHOW OPTIONS.
    window.addEvent('domready', function() {
      //FUNCTION CALLING HERE
      showblock1(<?php echo $this->tab1_show ?>);
      showblock2(<?php echo $this->tab2_show ?>);
      showblock3(<?php echo $this->tab3_show ?>);
    });
  }
  var fetchLikeSettings =function(pagelevel_id) {
    //CONDITION IS CHECK
    if (pagelevel_id != 0) 
    {
      window.location.href= en4.core.baseUrl+'admin/sitelike/settings/index/id/'+pagelevel_id;
    }
    else 
    {
      window.location.href= en4.core.baseUrl+'admin/sitelike/settings/';
    }   
  }


  //WE CAN MAKE THE FUNCTION FOR SHOWLOCK 1
  function showblock1(option) {
    //CONDITION IS CHECK  FOR SHOW OR HIDE
    if(option == 1) 
    {
      $('tab1_duration-wrapper').style.display = 'block';
      $('tab1_name-wrapper').style.display = 'block';
      $('tab1_entries-wrapper').style.display = 'block';
    }
    else 
    {
      $('tab1_duration-wrapper').style.display = 'none';
      $('tab1_name-wrapper').style.display = 'none';
      $('tab1_entries-wrapper').style.display = 'none';
    }
  }


  //WE CAN MAKE THE FUNCTION FOR SHOWLOCK 2
  function showblock2(option) {
    //CONDITION IS CHECK  FOR SHOE OR HIDE 
    if(option == 1) 
    {
      $('tab2_duration-wrapper').style.display = 'block';
      $('tab2_name-wrapper').style.display = 'block';
      $('tab2_entries-wrapper').style.display = 'block';
    } 
    else 
    {
      $('tab2_duration-wrapper').style.display = 'none';
      $('tab2_name-wrapper').style.display = 'none';
      $('tab2_entries-wrapper').style.display = 'none';
    }
  }


  //WE CAN MAKE THE FUNCTION FOR SHOWLOCK 3
  function showblock3(option) {
    //CONDITION IS CHECK 
    if(option == 1) 
    {
      $('tab3_duration-wrapper').style.display = 'block';
      $('tab3_name-wrapper').style.display = 'block';
      $('tab3_entries-wrapper').style.display = 'block';
    } 
    else 
    {
      $('tab3_duration-wrapper').style.display = 'none';
      $('tab3_name-wrapper').style.display = 'none';
     $('tab3_entries-wrapper').style.display = 'none';

    }
  }
</script>