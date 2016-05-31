<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-icon.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo "Advanced Search Plugin" ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render();?>
  </div>
<?php endif; ?>

<h3><?php echo "Manage Content Icons"; ?></h3>
<p><?php echo "Below, you can change the icon for content types you have selected to be shown in auto suggest. These icons will displayed in auto suggest of global search available in the mini-menu of the website."; ?> </p>
<br />

<div class="seaocore_admin_order_list">
	 <div class="list_head">
    <div style="width:20%">
	     <?php echo "Content Module";?>
	   </div>
	   <div style="width:20%">
	     <?php echo "Title";?>
	   </div>  
	   <div style="width:20%">
	     <?php echo "Icon";?>
	   </div>
	   <div style="width:16%">
	     <?php echo "Option";?>
	   </div>
	 </div>
  <form>
    <div>      
    	 <ul>
      	 <?php foreach ($this->paginator as $item) : ?>
      	   <?php if($item->resource_type == 'feedback'):?>
      	     <?php $version = Engine_Api::_()->getApi('core','siteadvsearch')->getModuleVersion($item->resource_type);?>
      	     <?php if($version < '4.8.0'):?>
      	       <?php continue;?>
      	     <?php endif;?>
      	   <?php endif;?>
      	   <?php if($item->resource_type == 'document'):?>
      	     <?php $version = Engine_Api::_()->getApi('core','siteadvsearch')->getModuleVersion($item->resource_type);?>
      	     <?php if($version < '4.8.0'):?>
      	       <?php continue;?>
      	     <?php endif;?>
      	   <?php endif;?>
        	 <li>
	          <div style="width:20%;" class='admin_table_bold'>
              <?php if(!empty($item->listingtype_id)):?>
                <?php echo 'Multiple Listing Types'; ?>
              <?php else:?>
                <?php echo $item->module_title; ?>
              <?php endif;?>
	           </div>
            <div style="width:20%;" class='admin_table_bold'>
	             <?php echo $item->resource_title; ?>
	           </div>	  
	           <div style="width:20%;" class='admin_table_bold'>
              <span class="categories-image-preview seaocore_file_preview_wrapper fleft">
                <span class="">
                  <?php if(!empty($item->file_id)):?>
                    <img alt="" src="<?php echo $this->storage->get($item->file_id, '')->getPhotoUrl(); ?>" />
                  <?php elseif( Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $item->resource_type == 'sitehashtag_hashtag'):?>
                        <img src=" <?php  $this->layout()->staticBaseUrl?> application/modules/Sitehashtag/externals/images/Hashtag.png" alt='' >
                    <?php else:?>
                    <img alt="" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Siteadvsearch/externals/images/search.png">
                  <?php endif;?>
                </span>
              </span>
	           </div>	
	           <div style="width:16%;">     
							       <a href='<?php echo $this->url(array('action' => 'edit-icon','content_id' => $item->content_id), 'siteadvsearch_admin_general', true) ?>' class="smoothbox fleft"><?php echo "Change Icon"; ?>
	            </a>
	      		   </div>
	      	  </li>
	    	  <?php endforeach; ?>
   		 </ul>
  	 </div>
	 </form>
</div>