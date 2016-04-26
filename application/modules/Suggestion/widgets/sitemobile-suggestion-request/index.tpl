<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */ 
?>
<div class="sm-content-list">
    <ul class='sm-ui-lists'  data-role="listview" data-icon="false">    
      <?php if ($this->requests->getTotalItemCount() > 0): ?>
        <?php foreach ($this->requests as $notification): ?>
          <?php
          try {
            $parts = explode('.', $notification->getTypeInfo()->handler);
            echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
          } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
              echo $e->__toString();
            }
            continue;
          }
          ?>
        <?php endforeach; ?>
      <?php else: ?>
        <li>
          <div class="tip">
            <span><?php echo $this->translate("You have no requests.") ?></span>
          </div>	
        </li>
  <?php endif; ?>
    </ul>  
</div>



