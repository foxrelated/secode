<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: header.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_dashboard_header">
    <span class="fright">
        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->translate('View Event'), array("class" => 'siteevent_buttonlink')) ?>
    </span>
    <span class="siteevent_dashboard_header_title o_hidden">
        <?php echo $this->translate('Dashboard'); ?>: 
        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
    </span>
    <!--//IF EVENT REPEAT MODULE EXIST THEN SHOW EVENT REPEAT INFO WIDGET-->
    <?php
    $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
    if ($siteeventrepeat) {
        echo $this->content()->renderWidget("siteeventrepeat.event-profile-repeateventdate", array("showeventtype" => 1, "showeventtime" => 1));
    }
    ?>
</div>