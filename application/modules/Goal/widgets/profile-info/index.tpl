<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
 ?>
<h3><?php echo $this->translate("Goal Info") ?></h3>
<ul>
  <li class="goal_stats_title">
   <div class="goal_profile_info">
       <div class="goal-owner">
           <span><?php echo $this->translate('Set by');?>: </span> <?php echo $this->goal->getOwner(); ?> 
       </div>
       <div class="goal-creation">
         <span>Creation Date: </span><?php echo $this->locale()->toDate($this->goal->creation_date) ?>
       </div>
       <div class="goal-cat">    
           <?php if( !empty($this->goal->category_id) && 
            ($category = $this->goal->getCategory()) instanceof Core_Model_Item_Abstract &&
                !empty($category->title)): ?>
            <span>Category: </span><?php echo $this->translate((string)$category->title) ?>
            <?php endif; ?>
    </div>
       <div class="goal-views">
           <?php echo $this->translate(array('%s total view', '%s total views', $this->goal->view_count), $this->locale()->toNumber($this->goal->view_count)) ?>
       </div>
   </div>
  </li>
</ul>

<script type="text/javascript">
  $$('.core_main_goal').getParent().addClass('active');
</script>
