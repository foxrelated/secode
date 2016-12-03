<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="print-popup">
  <div id="printdiv_top" class="top">
  <?php echo $this->translate("Here is your Membership card");?>
  </div>
  <div style="margin:0 auto;width:326px;">
    <?php echo $this->userCard; ?>
  </div>
  <script type="text/javascript">
	function printData() {
	  document.getElementById('printdiv').style.display = "none";
	  document.getElementById('printdiv_top').style.display = "none";
	  window.print();
	  setTimeout(function() {
		parent.Smoothbox.close();
	  }, 500);
	}
  this.onload = function() 
  {
  $('global_page_mcard-index-print').set('style', 'cackfround-color:red');
  }
  </script>
  <div id="printdiv">
    <div class="print-txt">
      <p><?php echo $this->translate("Print this membership card to carry it with you."); ?></p>
      <p class="mcard_light" ><?php echo $this->translate("If you find that the background doesn't print and your browser is Microsoft Internet Explorer, go to the 'Tools' drop-down menu and select 'Internet Options'. Then click on 'Advanced' and scroll down to 'Print Background Colors' and check the box. That should do the trick. Other browsers should have a similar option.");?></p>
      <p class="mcard_light" ><?php echo $this->translate("If you find that the background doesn't print and your browser is 'Firefox', then click on 'Print Now' and then in 'Options', check the options for 'Print Background Images' and 'Print Background Color'."); ?></P>

      <p><?php echo $this->translate("The information you see on your card is taken from your Member Profile. To change it just go to your profile page by clicking on Profile in the main menu at the top of page.");?></p>

      <p><?php echo $this->translate("You can print out your membership card and keep it in your handbag or wallet, although there's no need to take it with you at all times. It's just a bit of fun!");?></p>      
    </div>
    <div class="print-card">
      <input type="button" value="Print Now" onClick="printData();">
    </div>
  </div>
</div>

