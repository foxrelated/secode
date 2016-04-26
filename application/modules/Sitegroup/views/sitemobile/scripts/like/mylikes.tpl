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
<?php
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');
	$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitegroup';
	$RESOURCE_TYPE = 'sitegroup_group';
	$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.sponsored', 1);
	$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()):?>
	<div class="sitegroup_view_select">
	<h3 class="sitegroup_mygroup_head"><?php echo $this->translate('Groups I Like'); ?></h3>
  </div>
<?php endif;?>
<?php if ($this->paginator->count() > 0): ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>	 
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
<div class="sm-content-list">  
<ul data-role="listview" data-inset="false">
			<?php foreach ($this->paginator as $sitegroup): ?>
				<li>
          <a href="<?php echo $sitegroup->getHref();?>">
          <!--ADD PARTIAL VIEWS -->
            <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/sitemobile_partial_views.tpl';?>
          
            <p><?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?></p>
		      </a>
	      </li>
			<?php endforeach; ?>
</ul>
</div>
 <?php else: ?>
<?php if(!$this->autoContentLoad) : ?>
    <ul class="p_list_grid" id="mylikegroups_ul">
<?php endif;?>
      <?php foreach ($this->paginator as $sitegroup): ?>
        <li style="height:200px;">
          <a href="<?php echo $sitegroup->getHref(); ?>" class="ui-link-inherit">
            <div class="p_list_grid_top_sec">
              <div class="p_list_grid_img">
                <?php
                $url = $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png';
                $temp_url = $sitegroup->getPhotoUrl('thumb.profile');
                if (!empty($temp_url)): $url = $sitegroup->getPhotoUrl('thumb.profile');
                endif;
                ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
              </div>
              <div class="p_list_grid_title">
                <span><?php echo $this->string()->chunk($this->string()->truncate($sitegroup->getTitle(), 45), 10); ?></span>
              </div>
            </div>
          </a>
          <div class="p_list_grid_info">	                 
            <span class="fleft">
              <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?>
            </span>
              <?php if ($postedBy): ?>
            <span class="fright">
               <?php echo $this->translate('by ') . '<b>' .$this->htmlLink($sitegroup->getOwner()->getHref(), $this->string()->truncate($sitegroup->getOwner()->getTitle(), 16)) . '</b>'; ?>
            </span> 
              <?php endif; ?>
            <span class="p_list_grid_stats">
              <?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>
            </span>                  
          </div>   
        </li>
      <?php endforeach; ?>
<?php if(!$this->autoContentLoad) : ?>
    </ul>
<?php endif; ?>
  <?php endif; ?>
<?php if( $this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
  <?php else: ?>

  <div class="tip">
  		<span>
			<?php $translategroup = "<a href=".$this->url(array('action' => 'index'), 'sitegroup_general', true).">" . $this->translate("Explore groups") . "</a>";
			echo $this->translate("You have not liked any groups yet.");?>
		</span>
	</div>
  <?php endif; ?>

 <script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>

   sm4.core.runonce.add(function() {    
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : null, 'contentUrl' : '<?php echo $this->url(array('action' => 'mylikes'));?>', 'activeRequest' : false, 'container' : 'mylikegroups_ul' };
             
          });
          
  <?php } ?>           
</script>
