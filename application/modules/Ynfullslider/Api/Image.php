<?php

class Ynfullslider_Api_Image
{
	public function _fetchImage($photo_url)
	{
		$photo_url = str_replace(' ', '%20', $photo_url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $photo_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		$data = curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($retcode != 200) {
			return 0;
		}

		$tmpfile = APPLICATION_PATH_TMP . DS . md5($photo_url) . '.jpg';
		@file_put_contents($tmpfile, $data);
		return $this -> _resizeImages($tmpfile);
	}

	public function _getVideoImage($video_file_id) {
		$storageObject = Engine_Api::_() -> getItem('storage_file', $video_file_id);
		if (!$storageObject)
		{
			throw new Ynfullslider_Model_Exception('Video storage file was missing');
		}

		$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynfullslider_ffmpeg_path;

		if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
		{
			$output = null;
			$return = null;
			exec($ffmpeg_path . ' -version', $output, $return);
			if ($return > 0)
			{
				return 0;
			}
		}
		// CALCULATE VIDEO DURATION
		$fileCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($storageObject -> storage_path) . ' ' . '2>&1';
		$fileOutput = shell_exec($fileCommand);
		$infoSuccess = true;
		$duration = 0;
		if (preg_match('/video:0kB/i', $fileOutput))
		{
			$infoSuccess = false;
		}

		if ($infoSuccess)
		{
			// REGEX FROM FFMPEG INFO TO GET VIDEO DURATION
			if (preg_match('/Duration:\s+(.*?)[.]/i', $fileOutput, $matches))
			{
				list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
				// CALCULATE FROM H:M:S TO SECONDS
				$duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
			}
		}

		// START GETTING THUMBNAIL, GET THE CAPTURE AT VIDEO MIDDLE POINT FOR NOW
		$thumb_splice = $duration / 2;
		$tmpDir = APPLICATION_PATH_TMP;
		$thumbPathLarge = $tmpDir . DIRECTORY_SEPARATOR . $video_file_id . '_vthumb_large.jpg';

		// Prepare output header
		$output = PHP_EOL;
		$output .= $storageObject -> temporary() . PHP_EOL;
		$output .= $thumbPathLarge . PHP_EOL;

		$thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($storageObject -> temporary()) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbPathLarge) . ' ' . '2>&1';
		// Process thumbnail
		$thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);
		// Check output message for success

		// Resize thumbnail
		if ($infoSuccess && is_file($thumbPathLarge))
		{
			return $this->_resizeImages($thumbPathLarge);
		}
	}

	protected function _resizeImages($file)
	{
		$name = basename($file);
		$path = dirname($file);

		try {
			// MAIN SIZE, THIS WILL BE USED FOR SLIDE EDITING, THEREFORE WE CHOOSE 950px AS THE ADMIN LAYOUT WIDTH
			$iMainPath = $path . '/m_' . $name;
			$image = Engine_Image::factory();
			$image -> open($file) -> write($iMainPath) -> destroy();

			// PROFILE SIZE
			$iProfilePath = $path . '/p_' . $name;
			$image = Engine_Image::factory();
			$image -> open($file) -> resize(300, 300) -> write($iProfilePath) -> destroy();

			// CLOUD COMPATIBILITY, PUT INTO STORAGE SYSTEM AS TEMPORARY FILES
			$storage = Engine_Api::_() -> getItemTable('storage_file');

			// SAVE
			$iMain = $storage -> createTemporaryFile($iMainPath);
			$iProfile = $storage -> createTemporaryFile($iProfilePath);

			$iMain -> bridge($iProfile, 'thumb.profile');

			// REMOVE TEMP FILES
			@unlink($path . '/m_' . $name);
			@unlink($path . '/p_' . $name);
			@unlink($file);

			return $iMain->file_id;
		}
		catch( Engine_Image_Adapter_Exception $e ) {
			return 0;
		}
	}
}
