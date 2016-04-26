<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: statistic.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>

<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate("Videos / Channels / Playlists Statistics") ?> </h3>
      <p class="description">
        <?php echo $this->translate("Below are some valuable statistics for the Videos/Channels/Playlists created on this site:"); ?>
      </p>
      <table class='admin_table' style="width: 50%;">
        <tbody>
          <tr>
            <td><strong class="bold"><?php echo "Total Videos:" ?><strong></td>
            <td><?php echo $this->totalvideo; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Videos:" ?><strong></td>
            <td><?php echo $this->totalvideofeatured; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Videos:" ?><strong></td>
            <td><?php echo $this->totalvideosponsored; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Videos:" ?><strong></td>
            <td><?php echo $this->totalvideofavourite; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Rated Videos:" ?><strong></td>
            <td><?php echo $this->totalvideorated; ?></td>
          </tr>          
          <tr>
            <td><strong class="bold"><?php echo "Total Channels:" ?><strong></td>
            <td><?php echo $this->totalchanel; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Channels:" ?><strong></td>
            <td><?php echo $this->totalchanelfeatured; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Channels:" ?><strong></td>
            <td><?php echo $this->totalchanelsponsored; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Channels:" ?><strong></td>
            <td><?php echo $this->totalchanelfavourite; ?></td>
          </tr>
          
           <tr>
            <td><strong class="bold"><?php echo "Total Playlists:" ?><strong></td>
            <td><?php echo $this->totalplaylist; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Playlists:" ?><strong></td>
            <td><?php echo $this->totalplaylistfeatured; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Playlists:" ?><strong></td>
            <td><?php echo $this->totalplaylistsponsored; ?></td>
          </tr>
          
        </tbody>
      </table>
    </div>
  </form>
</div>