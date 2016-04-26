<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  <?php
  $params = array('action' => 'create');
  if ($this->parent_type && $this->parent_id) {
    $params['parent_type'] = $this->parent_type;
    $params['parent_id'] = $this->parent_id;
  }
  ?>
<?php if ($this->quick): ?>
  <div class="quicklinks">
    <ul class="navigation">
      <li> 
        <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
            <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="buttonlink seaocore_icon_add" ><?php echo $this->translate("Create New Event"); ?></a>
        <?php else:?>  
        <?php if(empty($this->viewer_id) && $this->creation_link): ?>  
            <a href='<?php echo $this->url($params, "siteevent_general", true) ?>' class="buttonlink seaocore_icon_add" ><?php echo $this->translate("Create New Event"); ?></a>
        <?php else: ?>  
            <a href='<?php echo $this->url($params, "siteevent_general", true) ?>' class="buttonlink seaocore_icon_add seao_smoothbox" ><?php echo $this->translate("Create New Event"); ?></a> 
        <?php endif; ?>
       <?php endif;?>
      </li>
    </ul>
  </div>
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>
  <?php endif; ?>
  <script type="text/javascript">
    Asset.javascript('<?php echo $this->tinyMCESEAO()->addJS(true) ?>');
  </script>
<?php else: ?>
  <div class="quicklinks">
    <ul class="navigation">
      <li>
       <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="buttonlink seaocore_icon_add" ><?php echo $this->translate("Create New Event"); ?></a>
     <?php else:?>
			<a href='<?php echo $this->url($params, "siteevent_general", true) ?>' class="buttonlink seaocore_icon_add" ><?php echo $this->translate("Create New Event"); ?></a>
   <?php endif;?>
      </li>
    </ul>
  </div>
<?php endif; ?>