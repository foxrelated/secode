<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: userfbinvite.tpl 6590 2010-11-25 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="headline">
   <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
<?php echo $this->friends_invite;?>
