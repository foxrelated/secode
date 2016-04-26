<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: slidesStringFloom.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$floom_var = ''; 
	$floom_image_count = 0;
	$floom_path = '';//'public/advancedslideshow/1000000/1000/5/';
	foreach( $this->paginator as $item ):
    $photoUrl = $item->getPhotoUrl();      
		$ret_str = '';
		$str = trim($item->caption); 
		for($i=0;$i < strlen($str);$i++) 
		{ 
			if(substr($str, $i, 1) != " ") 
			{ 
				$string_world = trim(substr($str, $i, 1));
				if($string_world == "'")
				{
				$string_world = "\'";
				}
				$ret_str .= $string_world; 
			} 
			else 
			{ 
				while(substr($str,$i,1) == " ")            
				{ 
					$i++; 
				} 
				$ret_str.= " "; 
				$i--; // *** 
			}
		}
		if($this->total_images == $floom_image_count + 1) {
			$floom_var .= "{ image:"."'$photoUrl'";
			if(!empty($item->url)) {
				$url = 'http://'.$item->url;
				$floom_var .= ", url :"."'$url'";
			}
      elseif(!empty($item->params) && ($url = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params)) != '') {
        $floom_var .= ", url :"."'$url'";
      }
			if(!empty($item->caption) && $this->caption == 'true')
				$floom_var .= ", caption:"."'$ret_str'";

			if(!empty($item->url)) {
				if($this->target == 1)
					$floom_var .= ",target:'_blank'},";
				else 
					$floom_var .= ",target:'_self'},";
			}
			else {
				$floom_var .= "}";
			}
		}
		else	{
			$floom_var .= "{ image:"."'$photoUrl'";
			if(!empty($item->url)) {
				$url = 'http://'.$item->url;
				$floom_var .= ", url :"."'$url'";
			}
      elseif(!empty($item->params) && ($url = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params)) != '') {
        $floom_var .= ", url :"."'$url'";
      }      
			if(!empty($item->caption) && $this->caption == 'true')
				$floom_var .= ", caption:"."'$ret_str'";

		$getParamsCheck = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params);
    if(!empty($item->url) || (!empty($item->params) && !empty($getParamsCheck))) {
				if($this->target == 1)
					$floom_var .= ",target:'_blank'},";
				else 
					$floom_var .= ",target:'_self'},";
			}
			else {
				$floom_var .= "},";
			};
		}
		$floom_image_count++;	
	endforeach;
	$floom_var = trim($floom_var, ',');
?>
