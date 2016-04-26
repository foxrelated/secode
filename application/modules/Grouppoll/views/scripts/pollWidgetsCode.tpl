<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pollWidgetsCode.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
      echo $this->htmlLink(
              $grouppoll->getHref(), $this->itemPhoto($grouppoll->getOwner(), 'thumb.icon', $grouppoll->getOwner()->getTitle()), array('class' => 'grouppolls_profile_photo', 'title' => $grouppoll->title)
      )
      ?>
      <div class='seaocore_sidebar_list_info'>
        <div class='seaocore_sidebar_list_title'> 
          <?php echo $this->htmlLink($grouppoll->getHref(), Engine_Api::_()->grouppoll()->turncation($grouppoll->getTitle()), array('title' => $grouppoll->getTitle())); ?> 
        </div>
        <div class='seaocore_sidebar_list_details'>