<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addMenuList.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div id="parent_id_backgroundimage" style="display:none;">
    <div class="form-wrapper">
        <div class="form-label">
            &nbsp;
        </div>
        <div class="form-element">
            <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/loading.gif'?>" />
        </div>
    </div>
</div>
<div id="parent_id-wrapper" class="form-wrapper">
	<div id="parent_id-label" class="form-label">
	 <label for="parent_id" class="optional"><?php echo "Choose sub menu";?></label>
	</div>
    <div id="parent_id-element" class="form-element"<?php if(!empty ($this->flag)):?> onchange = "isSubMenuItem(<?php echo $this->depth ?>)"<?php endif;?>>
		<select name="parent_id" id="parent_id" >
			
		</select>
	</div>
</div>