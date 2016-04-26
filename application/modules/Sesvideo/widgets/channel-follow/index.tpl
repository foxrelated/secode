<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php  $followbutton =  Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->checkFollow(Engine_Api::_()->user()->getViewer()->getIdentity(),$this->subject->chanel_id); ?>
<button  data-url="<?php echo $this->subject->chanel_id ; ?>" class="sesbasic_share_btn sesvideo_chanel_follow button_chanel <?php echo ($followbutton)  ? 'button_active' : '' ?>"><?php echo ($followbutton)  ? $this->translate('Un-follow') : $this->translate('Follow') ?></button>


