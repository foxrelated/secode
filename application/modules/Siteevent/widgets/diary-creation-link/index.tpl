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

<div class="quicklinks">
    <ul class="navigation">
        <li> 
            <?php echo $this->htmlLink(array('route' => 'siteevent_diary_general', 'action' => 'create'), $this->translate('Create New Event Diary'), array('class' => 'smoothbox buttonlink siteevent_icon_diary_add')) ?>
        </li>
        </li>
    </ul>
</div>