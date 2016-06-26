<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php if ($this->canDeleteMenu): ?>

    <form method="post" class="global_form_popup">
  <div>
    <h3><?php echo "Delete Menu Item?" ?></h3>
    <p>
      <?php echo "Are you sure you want to remove this menu item?" ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo TRUE ;?>"/>
      <button type='submit'><?php echo "Delete" ?></button>
      <?php echo "or" ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo "cancel" ?></a>
    </p>
  </div>
</form>

<?php else: ?>

    <div><b><?php echo "This menu item contains sub menus items therefore it cannot be deleted. To delete this menu item, first remove all of its sub menus." ?></b></div>

<?php endif; ?>