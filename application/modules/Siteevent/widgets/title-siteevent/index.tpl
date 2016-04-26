<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id='profile_status'>
    <h2>
        <?php echo $this->title; ?>
    </h2>
    <?php
    $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
    if ($siteeventrepeat) {
        $showrepeatinfo = is_array($this->eventInfo) && in_array('showrepeatinfo', $this->eventInfo) ? true : false;
        echo $this->content()->renderWidget("siteeventrepeat.event-profile-repeateventdate", array("showrepeatinfo" => $showrepeatinfo));
    }
    ?>
</div>