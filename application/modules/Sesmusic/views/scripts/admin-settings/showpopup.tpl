<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: statistic.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("How to get SoundCloud Client ID and Client Secret?") ?></h3>
    <p><?php echo $this->translate("        
        1. Login to your SoundCloud Account. Register a new account, if you do not have one.<br />
        2. Now, go to the URL: https://soundcloud.com/you/apps<br />
        3. Click on Register a new application button.<br />
        4. Follow the easy steps by entering the name of your app and your website URL.<br />
        5. Now, copy the Client ID and Client Secret and paste here.") ?></p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->artist_id ?>"/>
      <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Ok") ?></button>
      </a>
    </p>
  </div>	
</form>