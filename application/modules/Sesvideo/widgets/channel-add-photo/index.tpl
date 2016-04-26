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
<button onclick="javascript:window.location.href='<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'edit', 'chanel_id' => $this->subject->chanel_id), 'sesvideo_chanel', true).'?tab=add_videos'; ?>'"  class="sesbasic_share_btn button_chanel"><?php echo $this->translate('Add Videos'); ?></button>


