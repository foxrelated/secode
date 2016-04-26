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
<?php if(count($this->paginator) <= 0): ?>
<div class="tip">
  <span>
    <?php echo $this->translate('Nobody has created a music album with that criteria.') ?>
    <?php if($this->canCreate): ?>
    <?php echo $this->htmlLink(array('route' => 'sesmusic_general', 'action' => 'create'), $this->translate('Why don\'t you add some?')) ?>
    <?php endif; ?>
  </span>
</div>
<?php endif; ?>