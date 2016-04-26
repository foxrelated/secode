<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<div class="sesbasic_alphabetic_search">
  <?php if($this->contentType == 'videos'): ?>
    <?php $URL =  $this->url(array('action' => 'browse'), 'sesvideo_general', true); ?>
  <?php elseif($this->contentType == 'chanels'): ?>
    <?php $URL =  $this->url(array('action' => 'browse'), 'sesvideo_chanel', true); ?>
  <?php elseif($this->contentType == 'playlists'): ?>
    <?php $URL =  $this->url(array('action' => 'browse'), 'sesvideo_playlist', true); ?>
  <?php elseif($this->contentType == 'artists'): ?>
    <?php $URL =  $this->url(array('action' => 'browse'), 'sesvideo_artists', true); ?>
  <?php endif; ?>

  <?php $alphbet_array = array('all' => "#", "a" => "A", "b" => "B", "c" => "C", "d" => "D", "e" => "E", "f" => "F", "g" => "G", "h" => "H", "i" => "I", "j" => "J", "k" => "K", "l" => "L", "m" => "M", "n" => "N", "o" => "O", "p" => "P", "q" => "Q", "r" => "R", "s" => "S", "t" => "T", "u" => "U", "v" => "V", "w" => "W", "x" => "X", "y" => "Y", "z" => "Z"); ?>

  <?php foreach($alphbet_array as $key => $alphbet): ?>
    <a href="<?php echo $URL . '?alphabet=' . urlencode($key)?>" <?php if(isset($_GET['alphabet']) && $_GET['alphabet'] == $key):?> class="sesbasic_alphabetic_search_current"<?php endif;?>><?php echo $this->translate($alphbet);?></a>  <?php endforeach; ?>
</div>