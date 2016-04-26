<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1); ?>	

<?php
$sitegroup_approved = Zend_Registry::isRegistered('sitegroup_approved') ? Zend_Registry::get('sitegroup_approved') : null;
$renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.renew.email', 2))));
?>
<?php if(!$this->autoContentLoad) : ?>  
<?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
  <div class="tip"> 
    <span><?php echo $this->translate('You have already created the maximum number of groups allowed. If you would like to create a new group, please delete an old one first.'); ?> </span> 
  </div>
  <br/>
<?php endif; ?>
 <?php endif;?> 

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
    <div class="sm-content-list">
      <ul class="seaocore_browse_list" data-role="listview" data-inset="false">
        <?php foreach ($this->paginator as $sitegroup): ?>
          <li  data-icon="arrow-r">
            <a href="<?php echo $sitegroup->getHref(); ?>">			           
              <!-- ADD PARTIAL VIEWS -->
              <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/sitemobile_partial_views.tpl'; ?>
            </a> 
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else: ?>
    <?php if(!$this->autoContentLoad) : ?>  
    <ul class="p_list_grid" id="managegroups_ul">
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
              <?php
              $expiry = Engine_Api::_()->sitegroup()->getExpiryDate($sitegroup);
              if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires'))
                //echo $this->translate("Expiration Date: ");
              ?>
             <?php if ($expiry == "Expired" || $expiry == $this->translate('Never Expires')):?>
                <span style="color: green;">
                  <?php echo $expiry; ?>
                </span>
                <?php endif;?>
            </span>                  
          </div>   
        </li>
      <?php endforeach; ?>
    <?php if(!$this->autoContentLoad) : ?>      
    </ul>
  <?php endif;?>
  <?php endif; ?>
<?php elseif ($this->search): ?>
  <div class="tip"> <span> <?php
      if (!empty($sitegroup_approved)) {
        echo $this->translate('You do not have any group which matches your search criteria.');
      } else {
        echo $this->translate($this->group_manage_msg);
      }
      ?> </span> </div>
    <?php else: ?>
  <div class="tip">
    <span> <?php
      if (!empty($sitegroup_approved)) {
        echo $this->translate('You do not have any groups yet.');
      } else {
        echo $this->translate($this->group_manage_msg);
      }
      ?>
    </span>
  </div>
<?php endif; ?>

<?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
   'query' => $this->formValues,
  ));
  ?>
<?php endif; ?>

  <script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>

   sm4.core.runonce.add(function() {    
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : '<?php echo $this->url(array('action' => 'manage'));?>', 'activeRequest' : false, 'container' : 'managegroups_ul' };
             
          });
          
  <?php } ?>           
</script>