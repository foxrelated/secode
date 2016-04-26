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

<ul class="seaocore_sidebar_list siteevent_edotors_statistics">
    <li>
        <?php echo $this->translate(array('<span>%s</span> <div>Editor Review</div>', '<span>%s</span> <div>Editor Reviews</div>', $this->totalEditorReviews), $this->locale()->toNumber($this->totalEditorReviews)); ?>
    </li>

    <li>
        <?php echo $this->translate(array('<span>%s</span> <div>Editor</div>', '<span>%s</span> <div>Editors</div>', $this->totalEditors), $this->locale()->toNumber($this->totalEditors)); ?>
    </li>

</ul>