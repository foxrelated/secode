<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<script>
var seaocore_like_url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitegroup_like', true);?>';
seaocore_content_type = 'sitegroup';
</script>

<?php 
$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
  if(!empty($viewer))
  {
    $MODULE_NAME = 'sitegroup';
    $RESOURCE_TYPE = 'sitegroup_group';
    $RESOURCE_ID = Engine_Api::_()->core()->getSubject()->getIdentity();

    // Check that for this 'resurce type' & 'resource id' user liked or not.
    $check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
    if( !empty($check_availability) )
    {
      $label = 'Unlike this';
      $unlike_show = "display:block;";
      $like_show = "display:none;";
      $like_id = $check_availability[0]['like_id'];
    }
    else
    {
      $label = 'Like this';
      $unlike_show = "display:none;";
      $like_show = "display:block;";
      $like_id = 0;
    }
  }
  ?>
	<?php if(empty($this->sitegroup_like)){ return; } ?>
  <div class="sitegroup_like_button" id="sitegroup_unlikes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $unlike_show;?>' >
    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
      <i class="sitegroup_like_thumbdown_icon"></i>
      <span><?php echo $this->translate('Unlike') ?></span>
    </a>
  </div>
  <div class="sitegroup_like_button" id="sitegroup_most_likes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $like_show;?>'>
    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
      <i class="sitegroup_like_thumbup_icon"></i>
      <span><?php echo $this->translate('Like') ?></span>
    </a>
  </div>
  <input type ="hidden" id = "sitegroup_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />