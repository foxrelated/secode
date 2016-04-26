<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pollWidgets.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
		// THERE SOME SIMILAR CODE IN WIDGETS LIKE COMMENTS AND VIEWS AND PHOTO ITEM.
		include APPLICATION_PATH . '/application/modules/Grouppoll/views/scripts/pollWidgetsCode.tpl';
	?> 
		<?php echo $this->translate(array('%s comment', '%s comments', $grouppoll->comment_count), $this->locale()->toNumber($grouppoll->comment_count)) ?> |
		<?php echo $this->translate(array('%s vote', '%s votes', $grouppoll->vote_count), $this->locale()->toNumber($grouppoll->vote_count)) ?>
	</div>
</div>