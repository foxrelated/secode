<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<div id="profile_options">
  <ul>
  	<?php if($this->can_edit){ ?>
    <li> 
    	<a class="buttonlink sesbasic_icon_edit" href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'edit', 'chanel_id' => $this->subject->chanel_id), 'sesvideo_chanel', true); ?>"><?php echo $this->translate('Edit Channel Details'); ?></a> 
    </li>
    <?php } ?>
    <li> 
    	<a class="buttonlink smoothbox sesbasic_icon_share" href="<?php echo $this->url(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'sesvideo_chanel', 'id' => $this->subject->chanel_id, 'format' => 'smoothbox'),'default',true); ?>"><?php echo $this->translate("Share This Chanel"); ?></a> 
    </li>
    <?php if ($this->can_delete){ ?>
    <li> 
    	<a onclick="opensmoothboxurl(this.href);return false;" class="buttonlink smoothbox sesbasic_icon_delete" href="<?php  echo $this->url(array( 'module' => 'sesvideo', 'action' => 'delete', 'chanel_id' => $this->subject->chanel_id),'sesvideo_chanel',true); ?>"><?php echo $this->translate('Delete Channel'); ?></a> 
   </li>
   <?php  } ?>
  </ul>
</div>