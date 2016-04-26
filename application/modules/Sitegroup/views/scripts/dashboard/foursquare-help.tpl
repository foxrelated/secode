<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquarehelp.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitegroup_claim_turms">
  <b><?php echo $this->translate('Obtaining code for your Save to foursquare Button.') ?></b>
  <ol>
    <li><?php echo $this->translate("Next to your foursquare venue or foursquare tip, you’ll notice a button: &lt;/&gt; , like in the 2 pictures below.") ?></li>
    <li><?php echo $this->translate("Click on &lt;/&gt;.") ?></li>
    <li><?php echo $this->translate("Choose your button color.") ?></li>
    <li><?php echo $this->translate("Copy the embed code for your Save to foursquare Button.") ?></li>
  </ol>
  <br /><br /><img src="./application/modules/Sitegroup/externals/images/foursquarehelp1.jpg" alt="" />
  <br /><br /><img src="./application/modules/Sitegroup/externals/images/foursquarehelp2.jpg" alt="" />
</div>

<style type="text/css">
  *{
    font-size:12px;
    font-family:Arial, Helvetica, sans-serif;
  }
  .sitegroup_claim_turms
  {
    margin:10px;
  }
  ol{
    float:left;
    width:100%;
    clear:both;
    margin-bottom:10px;
  }
  ol li
  {
    margin-left:30px;
    clear:both;
    margin-top: 5px;
  }
  p{
    margin-left:30px;
    float:left;
  }
</style>