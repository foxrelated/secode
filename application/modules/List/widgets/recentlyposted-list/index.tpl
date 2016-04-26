<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<ul class="seaocore_sidebar_list">
  <?php  
		$this->partialLoop()->setObjectKey('list');
		echo $this->partialLoop('application/modules/List/views/scripts/partialloop.tpl', $this->listings);
	?>
</ul>
</ul>