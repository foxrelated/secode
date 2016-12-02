<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 

?>
<?php if (count($this->paginator) > 0): ?>
<?php if($this->autoContentLoad == 0) : ?>
  <div id="grid_view">
   <ul class="p_list_grid" id='browsediaries_ul'>
    <?php endif; ?>
      <?php foreach ($this->paginator as $diary):?>
        
       <li>
         <a href="<?php echo $diary->getHref() ?>" class="ui-link-inherit"> 
            <div class="p_list_grid_top_sec">
              <div class="p_list_grid_img">
            <?php 
            $url = $diary->getPhotoUrl('thumb.normal');
            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
              endif;
              ?>
                <span style="background-image: url(<?php echo $url; ?>);"></span>
              </div>
              <div class="p_list_grid_title">
                <span><?php echo $diary->title ?></span>
              </div>
            </div></a>
         
            <div class="p_list_grid_info">	
            <?php if (!empty($this->statisticsDiary)): ?>
              <span class="p_list_grid_stats">
                <?php
                $statistics = '';
                if (in_array('entryCount', $this->statisticsDiary)) {
                                    $statistics .= $this->translate(array('%s event', '%s events', $diary->total_item), $this->locale()->toNumber($diary->total_item)) . ' - ';
                                }

                if (in_array('viewCount', $this->statisticsDiary)) {
                    $statistics .= $this->translate(array('%s view', '%s views', $diary->view_count), $this->locale()->toNumber($diary->view_count)) . ' - ';
                }
                  
                  $statistics = trim($statistics);
                  $statistics = rtrim($statistics, ' - ');

                ?>
                <?php echo $statistics; ?>
              </span>
            <?php endif; ?>
              <span class="p_list_grid_stats">
                <?php echo $this->translate('%s - created by %s', $this->timestamp($diary->creation_date), "<b>".$diary->getOwner()->getTitle()."</b>") ?>
              </span>
            </div>
        </li></a>
      <?php endforeach; ?>
         <?php if($this->autoContentLoad == 0) : ?>
   </ul>
  </div>
<?php endif; ?>
<?php elseif ($this->isSearched > 2): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a diary with that criteria.'); ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a diary yet.'); ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">

 var browseEventPageWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/siteevent/name/diary-browse';   
 <?php $current_url = $this->url(array('controller' => 'diary','action' => 'browse')); ?>  
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : browseEventPageWidgetUrl, 'activeRequest' : false, 'container' : 'browsediaries_ul' };  
          });
</script>          