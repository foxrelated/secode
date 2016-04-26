<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getinsightinfo.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


	if (!empty($this->info_insights)) { 
 		echo 	'<table class="facebookse_show_insightsfb_table">';
 			foreach ($this->info_insights as $key => $value) {
 				echo '<tr><td class="form1">';
					echo $this->translate($key) . ':<br />' .  '<span style="font-size:11px;">' .$this->translate($value[0]) . '</span>' . 
				'</td><td class="form2">' . $value[1] . '</td></tr>';
   		}
   	echo '</table>';
 	}

?>