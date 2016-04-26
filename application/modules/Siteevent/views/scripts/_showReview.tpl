<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _showReview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if (isset($this->siteevent->rating_editor) && $this->siteevent->rating_editor && $this->siteevent->review_count == 1):
    $html_title = $this->translate('1 Editor Review');
elseif (isset($this->siteevent->rating_editor) && $this->siteevent->rating_editor && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)):
    $html_title = $this->translate(array('1 Editor Review and %s User Review', '1 Editor Review and %s User Reviews', ($this->siteevent->review_count - 1)), $this->locale()->toNumber(($this->siteevent->review_count - 1)));
elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)):
    $html_title = $this->translate(array('%s User Review', '%s User Reviews', $this->siteevent->review_count), $this->locale()->toNumber($this->siteevent->review_count));
endif;
?>

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
    <span title="<?php echo $html_title ?>">
        <?php echo $this->translate(array('%s review', '%s reviews', $this->siteevent->review_count), $this->locale()->toNumber($this->siteevent->review_count)) ?>
    </span>
<?php endif; ?>