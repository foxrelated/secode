<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialPhotoWidget.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js');
  
  $this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');       
  $i=0;

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php
$front = Zend_Controller_Front::getInstance(); 
$action = $front->getRequest()->getActionName();
$moduleName = $front->getRequest()->getModuleName();
?>
<script type="text/javascript">
  var submit_togroupprofile = true;
  function ShowPhotoGroup(groupurl) {
    if (submit_togroupprofile) {
      window.location = groupurl;
    }
    else {
      submit_togroupprofile = true;
    }
  }

</script>

<?php if(empty($this->showFullPhoto)):?>
<div class="sitegroupalbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="sitegroupalbum_sidebar_thumbs">

	    <?php foreach ($this->paginator as $sitegroupphoto):  ?>
	      <li class="mbot5"> 
	        <?php //if (!$this->showLightBox): ?>
<!--	          <a href="javascript:void(0)" onclick='ShowPhotoGroup("<?php //echo $sitegroupphoto->getHref() ?>")' title="<?php //echo $sitegroupphoto->title; ?>"  class="thumbs_photo">		
	            <span style="background-image: url(<?php //echo $sitegroupphoto->getPhotoUrl('thumb.normal'); ?>);"></span>
	          </a>-->
	        <?php //else: ?>           
	           <a href="<?php echo $sitegroupphoto->getHref() ?>" <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitegroupphoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitegroupphoto->title; ?>" class="thumbs_photo">          
	            <span style="background-image: url(<?php echo $sitegroupphoto->getPhotoUrl('thumb.normal'); ?>);"></span>
	          </a>
	        <?php //endif; ?>
          <?php if($this->displayGroupName):?> 
            <div class='sitegroupalbum_thumbs_details'>	
                <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $sitegroupphoto->group_id, $layout);?>
                <?php $parent = Engine_Api::_()->getItem('sitegroup_album', $sitegroupphoto->album_id);?>
								<?php echo $this->translate('in ').
										$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($sitegroupphoto->group_id)), $sitegroupphoto->group_title,array('title' => $sitegroupphoto->group_title)) ?>
            </div>         
          <?php endif;?>    
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitegroupphoto->owner_id)) :?>
              <div class='sitegroupalbum_thumbs_details'>	
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?> 
									<?php $userItem = Engine_Api::_()->getItem('user', $sitegroupphoto->owner_id);	 ?>         	
									<?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?> 
                <?php endif;?>
              </div>
            <?php endif;?>
          <?php endif;?>      
	        <?php if($this->show_detail == 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitegroupalbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitegroupphoto->comment_count), $this->locale()->toNumber($sitegroupphoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like') :?>
		          <div class='sitegroupalbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitegroupphoto->like_count), $this->locale()->toNumber($sitegroupphoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php else:?>

<div class="sitegroupalbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="generic_sitegroupalbum_photo_widget">
	    <?php foreach ($this->paginator as $sitegroupphoto):  ?>	   
	      <li class="mbot5"> 
          <div class="photo">
            <?php //if (!$this->showLightBox): ?>
<!--              <a href="javascript:void(0)" onclick='ShowPhotoGroup("<?php //echo $sitegroupphoto->getHref() ?>")' title="<?php //echo $sitegroupphoto->title; ?>"  class="thumbs_photo">		
                <img src="<?php //echo $sitegroupphoto->getPhotoUrl('thumb.normal'); ?>" class="thumb_normal" />
              </a>-->
            <?php //else: ?>
             <a href="<?php echo $sitegroupphoto->getHref() ?>" <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitegroupphoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitegroupphoto->title; ?>" class="thumbs_photo">          
	            <img src="<?php echo $sitegroupphoto->getPhotoUrl('thumb.normal'); ?>" class="thumb_normal" />
	          </a>
            <?php //endif; ?> 
          </div> 
          <?php if($this->displayGroupName):?>
            <div class='sitegroupalbum_thumbs_details'>	
               <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $sitegroupphoto->group_id, $layout);?>
               <?php $parent = Engine_Api::_()->getItem('sitegroup_album', $sitegroupphoto->album_id);?>
								<?php echo $this->translate('in ').
								$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($sitegroupphoto->group_id)), $sitegroupphoto->group_title,array('title' => $sitegroupphoto->group_title)) ?>
            </div>         
          <?php endif;?> 
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitegroupphoto->owner_id)) :?>
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?> 
                <div class='sitegroupalbum_thumbs_details'>	
                <?php $userItem = Engine_Api::_()->getItem('user', $sitegroupphoto->owner_id);	 ?>         	
                <?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?>                   </div>
              <?php endif;?>
            <?php endif;?>
          <?php endif;?>
	        <?php if($this->show_detail != 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitegroupalbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitegroupphoto->comment_count), $this->locale()->toNumber($sitegroupphoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like'):?>
		          <div class='sitegroupalbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitegroupphoto->like_count), $this->locale()->toNumber($sitegroupphoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php endif; ?> 