<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: print.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<link href="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesmusic/externals/styles/styles.css" rel="stylesheet" />
<link href="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesmusic/externals/styles/styles.css" rel="stylesheet" media="print" />
<script type="text/javascript">
  function printData() {
    $('lyricsprint').style.display = "none";
    window.print();
    setTimeout(function() {
      $('lyricsprint').style.display = "block";
    }, 400);
  }
</script>
<div class="sesmusic_print_wrapper">
  <div id="lyricsprint" class="sesmusic_options_buttons sesmusic_print_button">
    <a href="javascript:void(0);" class="fa fa-print" onclick="printData()"><?php echo $this->translate('Take Print') ?></a>
  </div>
  <div class="sesmusic_print_lyrics_content">	      
    <span><strong><?php echo $this->translate('Lyrics:'); ?></strong></span>
    <span><?php echo nl2br($this->albumsong->lyrics); ?></span>
  </div>	
</div>