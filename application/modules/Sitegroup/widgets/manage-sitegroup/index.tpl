<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='layout_middle'>
  <?php  
    $item_approved = Zend_Registry::isRegistered('sitegroup_approved') ? Zend_Registry::get('sitegroup_approved') : null;
    $renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.renew.email', 2))));?>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="seaocore_browse_list">
      <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
      
      <?php foreach ($this->paginator as $item): ?>
        <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?><?php if($item->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?>
           <?php if($item->featured):?>
             <i title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"></i>
          <?php endif;?>
        <?php endif;?>
          <div class='seaocore_browse_list_photo'>
            <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($item->group_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align'=>'left'))) ?> 
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?>
          <?php if (!empty($item->sponsored)): ?>
            <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
            if (!empty($sponsored)) { ?>
              <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>                 
              </div>
            <?php } ?>
          <?php endif; ?>
        <?php endif; ?>  
          </div>
        
          <div class='seaocore_browse_list_options'>
            <?php $this->can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($item, 'edit');  ?>
            <?php $this->can_delete = Engine_Api::_()->sitegroup()->isManageAdmin($item, 'delete');  ?>
            <?php if ($this->can_edit): ?>
                <?php if(empty ($item->declined)): ?>
                <a href='<?php echo $this->url(array('group_id' => $item->group_id), 'sitegroup_edit', true) ?>' class='buttonlink icon_sitegroups_dashboard'><?php if(!empty($item_approved)){ echo $this->translate('Dashboard'); }else { echo $this->translate($this->group_manage); } ?></a>

                <?php if($item->draft == 0) echo $this->htmlLink(array('route' => 'sitegroup_publish', 'group_id' => $item->group_id), $this->translate('Publish Group'), array('class'=>'buttonlink smoothbox icon_sitegroup_publish')) ?>

                  <?php endif; ?>

              <?php endif; ?>
              <?php if ($this->can_delete): ?>
                <a href='<?php echo $this->url(array('group_id' => $item->group_id), 'sitegroup_delete', true) ?>' class='buttonlink icon_sitegroups_delete'><?php echo $this->translate('Delete Group'); ?></a>
              <?php endif; ?>
              <?php if (Engine_Api::_()->sitegroup()->canShowPaymentLink($item->group_id)): ?>
              <div class="tip">
                <span>
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->group_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                </span>
              </div>
            <?php endif; ?>

            <?php if (Engine_Api::_()->sitegroup()->canShowRenewLink($item->group_id)): ?>
              <div class="tip">
                <span>
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->group_id ?>)"><?php echo $this->translate('Renew Group'); ?></a>
                </span>
              </div>
            <?php endif; ?>
          </div>

            <?php  
               echo $this->partial('partial_views.tpl', 'sitegroup', array('sitegroup' => $item, 'showOwnerInfo' => $this->showOwnerInfo)); ?>
            <?php echo $this->viewMore($item->body,200,5000) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php elseif ($this->search): ?>
    <div class="tip"> <span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any group which matches your search criteria.'); }else { echo $this->translate($this->group_manage_msg); } ?> </span> </div>
  <?php else: ?>
    <div class="tip">
      <span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any groups yet.'); }else { echo $this->translate($this->group_manage_msg); } ?>
        <?php if ($this->can_create): ?>
          <?php  if (Engine_Api::_()->sitegroup()->hasPackageEnable()):
          $createUrl=$this->url(array('action'=>'index'), 'sitegroup_packages');
          else:
          $createUrl=$this->url(array('action'=>'create'), 'sitegroup_general');
          endif; ?>
          <?php echo $this->translate('Get started by %1$screating%2$s a new group.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitegroup")); ?>