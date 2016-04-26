<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div class="global_form_popup">
  
  <h2><?php echo $this->translate("Icon for 3rd Party integrated module"); ?></h2>

  <?php echo $this->translate("To display icon for 3rd party integrated module on “Updates” Notifications link and “Request” widget follow the steps mentioned below:"); ?><br /><br />

  <?php echo $this->translate("Step 1: Open file at the path below:"); ?><br />
  <?php echo $this->translate("'/application/modules/Suggestion/externals/styles/main.css'"); ?><br /><br />

  <?php echo $this->translate("Step 2: Copy below mentioned code:"); ?>
  <div class="sugg_codemain">
    <?php echo '
    .' . $this->className . '{
    background-image: url(~/application/modules/Suggestion/externals/images/sugg_intr.png);
    }
    '; ?>
  </div><br />
  <?php echo $this->translate("Step 3: Paste the code mentioned in step 2 at the end of the file."); ?><br /><br />
  <button  onclick='javascript:parent.Smoothbox.close()'> <?php echo $this->translate("Close") ?> </button>
</div>

<style type="text/css">
  .sugg_codemain {
  background-color: #FAFCFE;
  border: 1px solid #8EB7CD;
  color: #000;
  font-family: Courier,Courier New,Verdana,Arial;
  margin: 3px auto 10px;
  padding: 10px;
  width: 90%;
  float: left;
  clear: both;
  }
</style>