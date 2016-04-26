<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
  <?php foreach ($this->sitegroups as $sitegroup): ?>
    <li>
	   <?php  $this->partial()->setObjectKey('sitegroup');
        echo $this->partial('application/modules/Sitegroup/views/scripts/partial_widget.tpl', $sitegroup);
		 ?>
	   <?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>
	      </div>
	    </div>
    </li>
  <?php endforeach; ?>
</ul>