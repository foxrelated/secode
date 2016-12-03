<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="quicklinks">
	<ul class="navigation">
		<li> 
      <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_wishlist_general', 'action' => 'create'), $this->translate('Create New Wishlist'), array('class' => 'smoothbox buttonlink sitestore_icon_wishlist_create')) ?>
    </li>
		</li>
	</ul>
</div>