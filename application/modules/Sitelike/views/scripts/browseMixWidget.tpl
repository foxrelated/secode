<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browseMixWidget.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $item =  $row_mix_fetch['object'][0];
if (!empty($item)):
  $show_like = 1;
  $module_type = $row_mix_fetch['type'];
  $module_type_id = $item->$module_id;
  ?>
  <li>
    <div class='sitelike_thumb'>
       <?php $itemTitle=$item->getTitle();

       if (empty ($itemTitle) && substr($row_mix_fetch['type'], -6) == "_photo"):  ?>
          <?php $parent=$item->getParent();
              $itemTitle= $parent->getTitle();
              if(empty ($itemTitle)):
               $parent=$parent->getParent();
              $itemTitle= $parent->getTitle();
              endif;
              $itemTitle=$this->translate("%s's photo", $itemTitle);
            ?>
        <?php endif;?>
      <?php if ($item->getPhotoUrl()): ?>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$module_type_id)); ?>
        <?php elseif (strstr($row_mix_fetch['type'], 'siteestore')) : ?>
        <?php
        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$module_type_id));
        ?>
      <?php else: ?>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$module_type_id)); ?>
      <?php endif; ?>
    </div>
    <div class='sitelike_info'>
      <div class='sitelike_title'>
       <?php $title1 = $itemTitle; ?>
        <?php $truncatetitle = Engine_String::strlen($title1) > 17 ? Engine_String::substr($title1, 0, 17) . '..' : $title1 ?>
        <?php echo $this->htmlLink($item, $itemTitle, array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$module_type_id)); ?>

      </div>
      <div class='sitelike_stats' style="clear:both;" id = "<?php echo $module_type; ?>_browsenum_of_like_<?php echo $module_type_id; ?>">
        <?php echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit'])); ?>
        <br>
        <?php
        if (!empty($row_mix_fetch['poster_id'])) {
          $show_label = Zend_Registry::get('Zend_Translate')->_('Last liked by %s');
          $getTitle = Engine_Api::_()->getItem('user', $row_mix_fetch['poster_id'])->getTitle();
          $getLink = Engine_Api::_()->getItem('user', $row_mix_fetch['poster_id'])->getHref();
          $show_label = sprintf($show_label, $this->htmlLink($getLink, $getTitle));
          echo $show_label;
        }
        ?>
      </div>
    </div>
    <div class="sitelikes_right_links">
      <div class="likes_link">
        	<?php
          if (($row_mix_fetch['type'] == 'event' || $row_mix_fetch['type'] == 'group') && ($this->filter != "past") && $this->viewer()->getIdentity() && !$item->membership()->isMember($this->viewer(), null)) :
            echo $this->htmlLink(array('route' => $module_name . '_extended', 'controller' => 'member', 'action' => 'join', "$module_id" => $item->getIdentity()), $this->translate("$module_title"), array('class' => 'buttonlink smoothbox icon_group_join', 'title' => $item->getTitle()));

          else:
            echo $this->htmlLink($item->getHref(), $this->translate("$view_title"), array('class' => "buttonlink $module_class", 'title' => $item->getTitle()));
          endif;
          ?>
      </div>

    <?php endif; ?>
