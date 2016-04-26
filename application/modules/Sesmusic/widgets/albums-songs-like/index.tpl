<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php //List View Code ?>
<?php if($this->showViewType): ?>
  <ul class="sesmusic_side_block">
    <?php foreach ($this->results as $userObject): ?>
    <?php $resource = Engine_Api::_()->getItem('user', $userObject['poster_id']); ?>
      <li class="sesmusic_sidebar_list">
        <?php echo $this->htmlLink($resource->getHref(), $this->itemPhoto($resource, 'thumb.icon'), array('class' => 'sesmusic_sidebar_list_thumb', 'title' => $resource->getTitle(), 'target' => '_parent')); ?>
        <div class="sesmusic_sidebar_list_info">
          <div class="sesmusic_sidebar_list_title">
            <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle(), array('title' => $resource->getTitle(), 'target' => '_parent')); ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    <?php if (!empty($this->viewAllLink)) { ?>
      <li class="sesmusic_side_block_more">
        <?php
          echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'sesmusic', 'controller' => 'index', 'action' => 'all-likes', 'type' => $this->type, 'id' => $this->id, 'showUsers' => $this->showUsers), 'default', true) . '">' . $this->translate('View All') . ' &raquo;</a>';
        ?>
      </li>
    <?php } ?>
  </ul>
<?php else: ?>
<?php //Grid View Code ?>
  <ul class="sesmusic_side_block">
    <?php foreach ($this->results as $userObject): ?>
    <?php $resource = Engine_Api::_()->getItem('user', $userObject['poster_id']); ?>
    <li class="sesmusic_sidebar_list_grid">
      <?php echo $this->htmlLink($resource->getHref(), $this->itemPhoto($resource, 'thumb.icon'), array('title' => $resource->getTitle(), 'target' => '_parent')); ?>
    </li>
    <?php endforeach; ?>
    <li class="sesmusic_side_block_more">
      <?php
        if (!empty($this->viewAllLink)) {
        echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'sesmusic', 'controller' => 'index', 'action' => 'all-likes', 'type' => $this->type, 'id' => $this->id, 'showUsers' => $this->showUsers), 'default', true) . '">' . $this->translate('View All') . '</a>';
        }
      ?>
    </li>
  </ul>
<?php endif; ?>