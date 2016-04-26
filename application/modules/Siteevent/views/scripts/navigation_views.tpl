<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: navigation_views.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<?php
$siteeventUserNavigation = Zend_Registry::isRegistered('siteeventUserNavigation') ? Zend_Registry::get('siteeventUserNavigation') : null;
$this->navigation = !empty($siteeventUserNavigation) ? $this->navigation : $siteeventUserNavigation;
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {

        var moreTabSwitchNavigation = window.moreTabSwitchNavigation = function(el) {
            el.toggleClass('seaocore_tab_open active');
            el.toggleClass('tab_closed');
        }
    });
</script>

<div class="headline">
    <h2>
        <?php if (!empty($this->navigationTabTitle)) : ?>
            <?php echo $this->navigationTabTitle; ?>
        <?php else: ?>
            <?php echo $this->translate("Events"); ?>
        <?php endif; ?>
    </h2>
    <div class="tabs">

        <?php if (count($this->navigation)): ?>
            <?php //if( !empty($this->navigationTabCount) ) : ?>
            <?php //$this->max = $this->navigationTabCount; ?>
            <?php //else: ?>
            <?php $this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.navigationtabs', 6); ?>
            <?php //endif; ?>

            <?php if (count($this->navigation) == ($this->max + 1)): $this->max++;
            endif;
            ?>

            <ul class='navigation siteevent_navigation_common'>
        <?php $key = 0; ?>
                <?php foreach ($this->navigation as $item): ?>			
          <?php if ($key < $this->max): ?>
            <li <?php if ($item->isActive() || ($this->package_show == 1 && $item->action ==  'create' && $controller == 'package' && $action != 'update-package')) : ?> class="active" <?php endif; ?>>
            <?php if($item->action == 'create'): ?>
             <?php if(Engine_Api::_()->siteevent()->hasPackageEnable()){ 
             $PackageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();}
              else{
             $PackageCount = 0;}
            ?>
              <?php if($PackageCount > 0):?>
                <a class= "<?php echo $item->class ?>" href='<?php echo $this->url(array('action' => 'index'), 'siteevent_package', true)?>' <?php if ($item->target): ?> target="_blank" <?php endif; ?>><?php echo $this->translate($item->label); ?></a>
              <?php else:?>
                <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                        <?php echo $this->translate($item->getLabel()); ?>
                </a>
              <?php endif;?>
              <?php else:?>
                <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                        <?php echo $this->translate($item->getLabel()); ?>
                </a>
              <?php endif;?>
            </li>
          <?php else: ?>
            <?php break; ?>
          <?php endif; ?>
          <?php $key++ ?>
        <?php endforeach; ?>

        <?php if (count($this->navigation) > $this->max): ?>
          <li class="tab_closed more_tab" onclick="moreTabSwitchNavigation($(this));">
            <div class="tab_pulldown_contents_wrapper">
              <div class="tab_pulldown_contents">          
                <ul>
                  <?php $key = 0; ?>
                  <?php foreach ($this->navigation as $item): ?>
                    <?php if ($key >= $this->max): ?>
                        <li <?php if ($item->isActive() || ($item->action ==  'create' && $controller == 'package' && $action != 'update-package')) : ?> class="active" <?php endif; ?>>
                        <?php $name = trim(str_replace('menu_core_main ', '', $item->getClass())); ?>
												<?php if($item->action == 'create'):?>
             <?php if(Engine_Api::_()->siteevent()->hasPackageEnable())
              $PackageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();
            else
              $PackageCount = 0; ?>
             <?php if($PackageCount > 0):?>
              <a class= "<?php echo $item->class ?>" href='<?php echo $this->url(array('action' => 'index'), 'siteevent_package', true)?>' <?php if ($item->target): ?> target="_blank" <?php endif; ?>><?php echo $this->translate($item->label); ?></a>
             <?php else:?>
              <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
													<?php echo $this->translate($item->getLabel()); ?>
              </a>
             <?php endif;?>
												<?php else:?>
													<a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
													<?php echo $this->translate($item->getLabel()); ?>
													</a>
												<?php endif;?>
                      </li>
                    <?php endif; ?>
                    <?php $key++ ?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
          </li>
        <?php endif; ?>
      </ul>

            <?php endif; ?>
    </div>
</div>