<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addViewByList.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div id="viewby_backgroundimage" style="display:none;">
    <div class="form-wrapper">
        <div class="form-label">
            &nbsp;
        </div>
        <div class="form-element">
            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" />
        </div>
    </div>
</div>
<div id="viewby-wrapper" class="form-wrapper">
	<div id="viewby-label" class="form-label">
	 <label for="viewby" class="optional"><?php echo "Popularity Criteria";?></label>
	</div>
    <div id="viewby-element" class="form-element">
		<select name="viewby" id="viewby" >
			
		</select>
	</div>
</div>