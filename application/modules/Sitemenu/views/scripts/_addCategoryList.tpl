<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addCategoryList.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div id="category_id_backgroundimage" style="display:none;">
    <div class="form-wrapper">
        <div class="form-label">
            &nbsp;
        </div>
        <div class="form-element">
            <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/loading.gif'?>" />
        </div>
    </div>
</div>
<div id="category_id-wrapper" class="form-wrapper">
	<div id="category_id-label" class="form-label">
	 <label for="category_id" class="optional"><?php echo "Category";?></label>
	</div>
    <div id="category_id-element" class="form-element">
		<select name="category_id" id="category_id" >
			
		</select>
	</div>
</div>