<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: preview.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
.mc-card{
  width:328px;
  display:block;
  float:left;
	-moz-border-radius:6px;
	border-radius:6px;
}
.mc-card-inner{
  display:block;
  float:left;
  background:url(./application/modules/Mcard/externals/images/default_inner.png) no-repeat bottom;
  padding:3px;
  width:322px;
  min-height:210px;
}
.mc-card .logo{
  float:left;
  margin-left:5px;
}
.mc-card .logo img {
  max-width:150px;
  max-height:50px;
}
.mc-card .head{
  float:right;
  color:#FFFFFF;
  font-weight:bold;
  font-size:14px;
  font-family:Arial, Helvetica, sans-serif;
  line-height:30px;
	margin-right:5px;
}
.member-details{
  float:left;
  width:100%;
  margin-top:10px;
}
.member-photo{
  float:left;
	margin:0 5px;
}
.member-photo img{
  float:left;
  width:100px;
	max-height:120px;
}
.member-details .info{
  float:left;
  color:#000000;
  font-size:12px;
  font-family:tahoma, Arial, Helvetica, sans-serif;
  line-height:14px;
	width:210px;
	overflow:hidden;
}
.member-details .info span{
  font-family:tahoma, Arial, Helvetica, sans-serif !important;
}
.member-details .info a{
  color:#000000;
}
#select_option-element label,
#select_option-element ul li{
	cursor:move;
}
</style>
	<?php if( !empty($this->userCard) ) {?>
    <div class="admin_files_wrapper" style="width:328px;margin:0 auto;">
      <div style="padding-bottom:5px;font-weight:bold;">
     		<?php echo 'Preview Membership Card'; ?>
      </div>
      <ul class="admin_files" style="max-height:800px;">
        <?php echo $this->userCard; ?>
      </ul>
    </div>	
	<?php } ?>