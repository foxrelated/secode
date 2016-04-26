<?php
	$item = $this -> item;
	$playlists = Engine_Api::_() -> getItemTable('ynvideochannel_playlist') -> getUserPlaylists($this -> viewer());
	$count = 0;
?>
<?php foreach($playlists as $playlist) :?>
	<?php if ($playlist->isEditable()) :?>
		<div class="checkbox-row">
			<?php
				$count ++;
				$check = false;
				if($item -> getType() == "ynvideochannel_video"){
					$tablePlaylistVideo = Engine_Api::_() -> getDbTable('playlistvideos', 'ynvideochannel');
					$row = $tablePlaylistVideo -> getMapRow($playlist -> getIdentity(), $item -> getIdentity());
					if(isset($row) && !empty($row)) {
						$check = true;
					}
				}
			?>
			<input onclick="ynvideochannelAddToPlaylist(this, '<?php echo $playlist -> getIdentity();?>', '<?php echo $item -> getGuid();?>', '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynvideochannel_general', true);?>');" <?php echo ($check)? "checked" : "" ;?> type="checkbox"><?php echo $playlist -> getTitle();?>
		</div>
	<?php endif;?>
<?php endforeach;?>
<?php if($count == 0):?>
	<div class="no-playlists">
		<?php echo $this -> translate("No playlist.");?>
	</div>
<?php endif;?>
