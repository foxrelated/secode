<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: comment.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<p><?php echo $this->message ?></p>
<script type="text/javascript">
  //<![CDATA[
  parent.en4.sitetagcheckin.viewComments(<?php echo $this->action_id ?>);
  parent.Smoothbox.close();
  //]]>
</script>