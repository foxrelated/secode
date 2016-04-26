<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: myadmingroups.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  function smoothboxgroup(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
<script type="text/javascript">

  var pageAction =function(group){
    $('group').value = group;
    $('filter_form').submit();
  }
</script>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
  <?php //echo $this->form->render($this) ?>

<div class='layout_middle'>

    <h3 class="sitegroup_mygroup_head"><?php echo $this->translate('Groups I Admin'); ?></h3>
  
  <?php
  $sitegroup_approved = Zend_Registry::isRegistered('sitegroup_approved') ? Zend_Registry::get('sitegroup_approved') : null;
  $renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.renew.email', 2))));
  ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="seaocore_browse_list">
      <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <div class='seaocore_browse_list_photo'>
						<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($item->group_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal')) ?> 
          </div>

          <div class='seaocore_browse_list_options'>
            <?php if ($this->can_edit): ?>
              <?php if (empty($item->declined)): ?>
                <a href='<?php echo $this->url(array('group_id' => $item->group_id), 'sitegroup_edit', true) ?>' class='buttonlink icon_sitegroups_dashboard'><?php if (!empty($sitegroup_approved)) {
                 echo $this->translate('Dashboard');
              } else {
                  echo $this->translate($this->group_manage);
              } ?></a>

							<?php if ($item->draft == 0)
								echo $this->htmlLink(array('route' => 'sitegroup_publish', 'group_id' => $item->group_id), $this->translate('Publish Group'), array('class' => 'buttonlink icon_sitegroup_publish', 'onclick' => 'smoothboxgroup(this);return false')) ?>
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1)):?>  
                <?php if (!$item->closed): ?>
                  <a href='<?php echo $this->url(array('group_id' => $item->group_id, 'closed' => 1, 'check' => 1), 'sitegroup_close', true) ?>' class='buttonlink icon_sitegroups_close'><?php echo $this->translate('Close Group'); ?></a>
                <?php else: ?>
                  <a href='<?php echo $this->url(array('group_id' => $item->group_id, 'closed' => 0, 'check' => 1), 'sitegroup_close', true) ?>' class='buttonlink icon_sitegroups_open'><?php echo $this->translate('Open Group'); ?></a>
                <?php endif; ?>
              <?php endif; ?>
              
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

					<?php  $this->partial()->setObjectKey('sitegroup');
					echo $this->partial('partial_views.tpl', $item); ?>

							<?php
							// Not mbstring compat
							echo substr(strip_tags($item->body), 0, 350);
							if (strlen($item->body) > 349)
								echo "...";
							?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="tip">
      <span> <?php if (!empty($sitegroup_approved)) {
      echo $this->translate('You do not have any groups yet.');
    } else {
      echo $this->translate($this->group_manage_msg);
    } ?>
  <?php if ($this->can_create): ?>
    <?php
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()):
      $createUrl = $this->url(array('action' => 'index'), 'sitegroup_packages');
    else:
      $createUrl = $this->url(array('action' => 'create'), 'sitegroup_general');
    endif;
    ?>
    <?php echo $this->translate('Get started by %1$screating%2$s a new group.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
  <?php endif; ?>
      </span>
    </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitegroup")); ?>
</div>

<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitegroup_session_payment', true) ?>">
  <input type="hidden" name="group_id_session" id="group_id_session" />
</form>

<script type="text/javascript">
  function submitSession(id){
    
    document.getElementById("group_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>