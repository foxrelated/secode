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

<h3 class="goal_categories_title">
    <?php echo $this->translate("Categories") ?>
</h3>
<?php if(count($this->categories) > 0): ?>
<ul id="category_list">
    <?php foreach($this->categories as $cat): ?>
    <li>
        <a href="<?php echo $this->base_url ?>/goals?category_id=<?php echo $cat->getIdentity()?>" ><?php echo $cat->getTitle() ?></a>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
  <div class="tip">
    <span>
        <?php echo $this->translate('There are no categories yet.') ?>
    </span>
  </div>
<?php endif; ?>

