<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: slidesStringOther.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
//print_r($this->paginator->toArray());die;
	$var = ''; 
	$image_count = 0;
	$path = '';//'public/advancedslideshow/1000000/1000/5/';
	foreach( $this->paginator as $item ):
    $photoUrl = $item->getPhotoUrl();
    $thumbPhotoUrl = $item->getPhotoUrl('thumb.normal');
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

		if(empty($this->oldversion)) {
			if(empty($ret_str)) {$ret_str = '<span></span>';}
		}

		if($this->total_images == $image_count + 1) {
			if(!empty($item->url)) {
				$url = 'http://'.$item->url;
        
        if($this->target == 1) {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". " href:" . "'$url'" . " , ". " target:" . "'_blank'" . "}";
        }
        else {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". " href:" . "'$url'" . "}";
        }        
        
			}
      elseif(!empty($item->params) && ($url = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params)) != '') {
        
        if($this->target == 1) {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". " href:" . "'$url'" . " , ". " target:" . "'_blank'" . "}";
        }
        else {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". " href:" . "'$url'" . "}";
        }               
        
      }      
			else
				$var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" ."}";
		}
		else	{
			if(!empty($item->url)) {
				$url = 'http://'.$item->url;
        if($this->target == 1) {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". "href:" . "'$url'" . ", ". " target:" . "'_blank'" . "  },";
        }
        else {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". "href:" . "'$url'" . "  },";
        }
			}
      elseif(!empty($item->params) && ($url = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params)) != '') {
        if($this->target == 1) {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". "href:" . "'$url'" . ", ". " target:" . "'_blank'" . "  },";
        }
        else {
          $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" .", ". "href:" . "'$url'" . "  },";
        }        
      }      
			else
				$var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" .", ". " caption:" . "'$ret_str'" ."},";
		}
		$image_count++;	
	endforeach;

	$var = trim($var, ',');
?>