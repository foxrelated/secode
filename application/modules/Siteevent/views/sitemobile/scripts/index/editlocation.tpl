<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editlocation.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php echo $this->partial('application/modules/Siteevent/views/sitemobile/scripts/dashboard/header.tpl', array('siteevent'=> $this->siteevent));?>
<div class="dashboard-content">
  <div class="global_form">
    <h3><?php echo $this->translate("Edit Location") ?></h3>
    <p><?php echo $this->translate("Edit the location of your event by clicking on 'Edit Location' below.") ?>    </p>
    <?php if ($this->location): ?>
      <div class="t_l">
        <?php
          echo $this->htmlLink(array(
          'route' => "siteevent_specific",
                        'action' => 'editaddress',
                        'event_id' => $this->siteevent->event_id
                            ), $this->translate("Edit Event Location"), array(
          'class' => 'fright'
          ));
        ?>
        <span class="o_hidden">
          <?php echo $this->translate('Location: ');?>
          <strong><?php echo $this->location->location ?></strong>
        </span>
      </div>
      <div class="sm-ui-map-wrapper sm-ui-edit-location-map clr">
        <div class="sm-ui-map" id="mapCanvas"></div>
        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
        <?php if (!empty($siteTitle)) : ?>
          <div class="sm-ui-map-info">
          <?php echo $this->translate("Locations on %s","<a href='' target='_blank'>$siteTitle</a>");?>
          </div>
        <?php endif; ?>
      </div>	
    <?php else: ?>
      <div class="tip">
        <span>
          <?php $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'editaddress', 'event_id' => $this->siteevent->event_id), "siteevent_specific", true); ?>
          <?php echo $this->translate('You have not added a location for your event %1$sClick here%2$s to add a location for your event', "<a href='$url'>", "</a>"); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php if (!empty($this->location)): ?>
    <script type="text/javascript">
        sm4.core.runonce.add(function() {
          sm4.core.map.initialize('mapCanvas',{latitude:'<?php echo $this->location->latitude ?>',longitude:'<?php echo $this->location->longitude ?>',location_id:'<?php echo $this->location->location_id ?>',marker:true,title:'<?php echo $this->location->location; ?>',zoom:<?php echo $this->location->zoom; ?>});
        });
    </script>
<?php endif; ?>