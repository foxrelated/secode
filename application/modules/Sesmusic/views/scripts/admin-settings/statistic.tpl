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
<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate("Music Statistics") ?> </h3>
      <p class="description">
        <?php echo $this->translate("Below are some valuable statistics for the Music created on this site:"); ?>
      </p>
      <table class='admin_table' style="width: 50%;">
        <tbody>
          <tr>
            <td><strong class="bold"><?php echo "Total Albums:" ?><strong></td>
            <td><?php echo $this->totalalbum; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Albums:" ?><strong></td>
            <td><?php echo $this->totalfeatured; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Albums:" ?><strong></td>
            <td><?php echo $this->totalsponsored; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Albums:" ?><strong></td>
            <td><?php echo $this->totalfavourite; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Hot Albums:" ?><strong></td>
            <td><?php echo $this->totalhot; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Rated Albums:" ?><strong></td>
            <td><?php echo $this->totalrated; ?></td>
          </tr>          
          <tr>
            <td><strong class="bold"><?php echo "Total Songs:" ?><strong></td>
            <td><?php echo $this->totalalbumsongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Songs:" ?><strong></td>
            <td><?php echo $this->totalfeaturedsongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Songs:" ?><strong></td>
            <td><?php echo $this->totalsponsoredsongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Songs:" ?><strong></td>
            <td><?php echo $this->totalfavouritesongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Hot Songs:" ?><strong></td>
            <td><?php echo $this->totalhotsongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Rated Songs:" ?><strong></td>
            <td><?php echo $this->totalratedsongs; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Playlists:" ?><strong></td>
            <td><?php echo $this->totalplaylists; ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </form>
</div>