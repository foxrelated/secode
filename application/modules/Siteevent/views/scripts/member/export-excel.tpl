<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (count($this->guestDetails)): ?>

    <?php
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel;charset:UTF-8");
        header("Content-Disposition: attachment; filename=" . $this->translate('GuestList') . ".xls");
        print "\n"; // Add a line, unless excel error..
    ?>
    <?php echo $this->translate('Event Title: %s', $this->event->title); ?><br />
    <?php if($this->occurrence_id):?>
      <?php echo $this->translate('Start Date: %s', $this->eventDates['starttime']); ?><br />
      <?php echo $this->translate('End Date: %s', $this->eventDates['endtime']); ?><br /><br />
    <?php endif;?>
    <table border="0">
        <tr>
            <th><?php echo $this->translate('Display Name'); ?></th>
            <th><?php echo $this->translate("User Name") ?></th>

            <th><?php echo $this->translate("RSVP") ?></th>
        </tr>
        <?php foreach ($this->guestDetails as $guest) : ?>

            <tr>
                <td><?php echo $guest->displayname; ?></td>
                <td><?php echo $guest->username; ?></td>

                <?php
                if (!empty($guest->resource_id)) {
                    $guestInfo = $guest;
                    $guest = $this->item('user', $guestInfo->user_id);
                } else {
                    $guestInfo = $this->event->membership()->getMemberInfoCustom($guest);
                }
                ?>
                <td>    
                    <?php if ($guestInfo->rsvp == 0): ?>
                        <?php echo $this->translate('Not Attending') ?>
                    <?php elseif ($guestInfo->rsvp == 1): ?>
                        <?php echo $this->translate('Maybe Attending') ?>
                    <?php elseif ($guestInfo->rsvp == 2): ?>
                        <?php echo $this->translate('Attending') ?>
                    <?php else: ?>
                        <?php echo $this->translate('Awaiting Approval') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>