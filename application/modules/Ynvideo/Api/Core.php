<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Api_Core extends Core_Api_Abstract {

    public function getItemTable($type) {
        if ($type == 'video') {
            return Engine_Loader::getInstance()->load('Ynvideo_Model_DbTable_Videos');
        } else if ($type == 'video_category') {
            return Engine_Loader::getInstance()->load('Ynvideo_Model_DbTable_Categories');
        } else {
            $class = Engine_Api::_()->getItemTableClass($type);
            return Engine_Api::_()->loadClass($class);
        }
    }

    public function getVideosPaginator($params = array(), $order_by = true) {
        $paginator = Zend_Paginator::factory($this->getVideosSelect($params, $order_by));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getVideosSelect($params = array(), $order_by = true) {
        $table = Engine_Api::_()->getDbtable('videos', 'ynvideo');
        $rName = $table->info('name');

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $select = $table->select()->from($table->info('name'))->setIntegrityCheck(false);

        if (!empty($params['orderby'])) {
            if (isset($params['order'])) {
                $order = $params['order'];
            } else {
                $order = '';
            }
            switch ($params['orderby']) {
                case 'most_liked' :
                    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
                    $select->joinLeft($likeVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.like_id) DESC");
                    break;
                case 'most_commented' :
                    $commentTable = Engine_Api::_()->getDbTable('comments', 'core');
                    $commentTableName = $commentTable->info('name');
                    $commentVideoTableSelect = $commentTable->select()->where('resource_type = ?', 'video');
                    $select->join($commentVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.comment_id) DESC");
                    break;
                case 'featured' :
                    $select->where('featured = ?', 1);
                    $select->order("$rName.creation_date DESC");
                    break;
                default :
                    $select->order("$rName.{$params['orderby']} DESC");
            }
        } else {
            if ($order_by) {
                $select->order("$rName.creation_date DESC");
            }
        }

        if (!empty($params['text'])) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $db = $searchTable->getAdapter();
            $sName = $searchTable->info('name');
            $select
                ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
                ->where($sName . '.type = ?', 'video')
                ->where($sName . '.title LIKE ?', "%{$params['text']}%")
            //->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
            //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
            ;
        }

        if (!empty($params['title'])) {
            $select->where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        if (!empty($params['status']) && is_numeric($params['status'])) {
            $select->where($rName . '.status = ?', $params['status']);
        }
        if (!empty($params['search']) && is_numeric($params['search'])) {
            $select->where($rName . '.search = ?', $params['search']);
        }
        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (array_key_exists('category', $params) && is_numeric($params['category'])) {
            if ($params['category'] != 0) {
                $select->where("$rName .category_id = {$params['category']} OR  $rName.subcategory_id = {$params['category']}");
            } else {
                $select->where("$rName .category_id = {$params['category']}");
            }
        }

        if (!empty($params['tag'])) {
            $select->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
                ->where($tmName . '.resource_type = ?', 'video')
                ->where($tmName . '.tag_id = ?', $params['tag']);
        }

        if (!empty($params['videoIds']) && is_array($params['videoIds']) && count($params['videoIds']) > 0) {
            $select->where('video_id in (?)', $params['videoIds']);
        }

        if (isset($params['type']) && is_numeric($params['type'])) {
            $select->where('type = ?', $params['type']);
        }

        if (isset($params['featured']) && is_numeric($params['featured'])) {
            $select->where('featured = ?', $params['featured']);
        }

        //Owner in Admin Search
        if (!empty($params['owner'])) {
            $key = stripslashes($params['owner']);
            $select->setIntegrityCheck(false)
                ->join('engine4_users as u1', "u1.user_id = $rName.owner_id", '')
                ->where("u1.displayname LIKE ?", "%$key%");
        }

        if (!empty($params['fieldOrder'])) {
            if ($params['fieldOrder'] == 'owner') {
                $select->setIntegrityCheck(false)
                    ->join('engine4_users as u2', "u2.user_id = $rName.owner_id", '')
                    ->order("u2.displayname {$params['order']}");
            } else {
                $select->order("{$params['fieldOrder']} {$params['order']}");
            }
        }

        if (!empty($params['parent_type'])) {
            $select->where('parent_type = ?', $params['parent_type']);
        }

        if (!empty($params['parent_id'])) {
            $select->where('parent_id = ?', $params['parent_id']);
        }
        return $select;
    }

    public function getCategories($catIds = null) {
        $table = Engine_Api::_()->getDbTable('categories', 'ynvideo');
        $select = $table->select();
        if ($catIds) {
            $select->where('category_id in (?)', $catIds);
        }
        $categories = $table->fetchAll($select->order('category_name ASC'));
        $cats = array();
        foreach ($categories as $category) {
            $cats[$category->getIdentity()] = $category;
        }

        return $cats;
    }

    public function getCategory($category_id) {
        return Engine_Api::_()->getDbtable('categories', 'ynvideo')->find($category_id)->current();
    }

    public function getRating($video_id) {
        $table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
        $rating_sum = $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('SUM(rating)'))
            ->group('video_id')
            ->where('video_id = ?', $video_id)
            ->query()
            ->fetchColumn(0)
        ;

        $total = $this->ratingCount($video_id);
        if ($total)
            $rating = $rating_sum / $this->ratingCount($video_id);
        else
            $rating = 0;

        return $rating;
    }

    public function getRatings($video_id) {
        $table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
        $rName = $table->info('name');
        $select = $table->select()
            ->from($rName)
            ->where($rName . '.video_id = ?', $video_id);
        $row = $table->fetchAll($select);
        return $row;
    }

    public function checkRated($video_id, $user_id) {
        $table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');

        $rName = $table->info('name');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->where('video_id = ?', $video_id)
            ->where('user_id = ?', $user_id)
            ->limit(1);
        $row = $table->fetchAll($select);

        if (count($row) > 0)
            return true;
        return false;
    }

    public function setRating($video_id, $user_id, $rating) {
        $table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
        $rName = $table->info('name');
        $select = $table->select()
            ->from($rName)
            ->where($rName . '.video_id = ?', $video_id)
            ->where($rName . '.user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        if (empty($row)) {
            // create rating
            Engine_Api::_()->getDbTable('ratings', 'ynvideo')->insert(array(
                'video_id' => $video_id,
                'user_id' => $user_id,
                'rating' => $rating
            ));
        }
    }

    public function ratingCount($video_id) {
        $table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
        $rName = $table->info('name');
        $select = $table->select()
            ->from($rName)
            ->where($rName . '.video_id = ?', $video_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    // handle video upload
    public function createVideo($params, $file, $values) 
    {
        if ($file instanceof Storage_Model_File) 
        {
            $params['file_id'] = $file->getIdentity();
        } 
        else 
        {
            // create video item
            $video = Engine_Api::_()->getDbtable('videos', 'ynvideo')->createRow($params);
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->save();
						
						 // Store video in temporary storage object for ffmpeg to handle
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
                ));
						
						// Make sure FFMPEG path is set
						$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynvideo_ffmpeg_path;
						if (!$ffmpeg_path)
						{
							throw new Ynvideo_Model_Exception('Ffmpeg not configured');
						}
						// Make sure FFMPEG can be run
						if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
						{
							$output = null;
							$return = null;
							exec($ffmpeg_path . ' -version', $output, $return);
							if ($return > 0)
							{
								throw new Ynvideo_Model_Exception('Ffmpeg found, but is not executable');
							}
						}
				
						// Check we can execute
						if (!function_exists('shell_exec'))
						{
							throw new Ynvideo_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
						}
						// Check the video temporary directory
						$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'video';
						if (!is_dir($tmpDir))
						{
							if (!mkdir($tmpDir, 0777, true))
							{
								throw new Ynvideo_Model_Exception('Video temporary directory did not exist and could not be created.');
							}
						}
						if (!is_writable($tmpDir))
						{
							throw new Ynvideo_Model_Exception('Video temporary directory is not writable.');
						}
						$originalPath = $storageObject -> temporary();
								
						$ffprobe = str_replace('ffmpeg', 'ffprobe', $ffmpeg_path);
						$cmd = $ffprobe . " " . $originalPath . " -show_streams 2>/dev/null";
				    $result = shell_exec($cmd);
				    $orientation = 0;
				    if(strpos($result, 'TAG:rotate') !== FALSE) {
				        $result = explode("\n", $result);
				        foreach($result as $line) {
				            if(strpos($line, 'TAG:rotate') !== FALSE) {
				                $stream_info = explode("=", $line);
				                $orientation = $stream_info[1];
				            }
				        }
				    }
						if($orientation)
						{
							$transpose = 1;	
							switch ($orientation) 
							{
								case 90:
									$transpose = 1;
									break;
								
								case 180:
									$transpose = 3;
									break;
									
								case 270:
									$transpose = 2;
									break;
							}
							$outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vrotated.'.$file_ext;
							// Check and rotate video
							$cmd = '';
							$h = '';
							if(strtolower($file_ext) == '3gp')
							{
								$h = '-s 352x288';
							}
							if($transpose == 3)
							{
								$cmd = $ffmpeg_path .' -i ' . escapeshellarg($originalPath) . ' -vf "vflip,hflip'.'" '.$h.' -b 2000k -r 30 -acodec copy -metadata:s:v:0 rotate=0 '.escapeshellarg($outputPath);
							}
							else 
							{
								$cmd = $ffmpeg_path .' -i ' . escapeshellarg($originalPath) . ' -vf "transpose='.$transpose.'" '.$h.' -b 2000k -r 30 -acodec copy -metadata:s:v:0 rotate=0 '.escapeshellarg($outputPath);
							}
	    				shell_exec($cmd);
							$storageObject -> store($outputPath);
							@unlink($outputPath);
						}
						 // Remove temporary file
            @unlink($file['tmp_name']);
						@unlink($originalPath);
						
            $video->file_id = $storageObject->file_id;
            $video->save();
						
            // Add to jobs
            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynvideo_encode', array(
                'video_id' => $video->getIdentity(),
            ));
        }
        return $video;
    }

    public function deleteVideo($video) {

        // delete video ratings
        Engine_Api::_()->getDbtable('ratings', 'ynvideo')->delete(array(
            'video_id = ?' => $video->video_id,
        ));

        // check to make sure the video did not fail, if it did we wont have files to remove
        if ($video->status == 1) {
            // delete storage files (video file and thumb)
            if ($video->type == Ynvideo_Plugin_Factory::getUploadedType())
			{
				try
				{
					Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();	
				}catch (Exception $e){}
			}
            if ($video->photo_id)
			{
				try
				{
					Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
				}
				catch (Exception $e){}
			}
        }

        // delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('video', $video->video_id);
        if ($item) {
            $item->delete();
        }
    }

    public function getPlaylists($userId) {
        $table = Engine_Api::_()->getDbTable('playlists', 'ynvideo');
        $select = $table->select();
        $select->where('user_id = ?', $userId);
        $select->order('creation_date DESC');
        return $table->fetchAll($select);
    }

    public function checkVideoBelongsToAPlayList($videoId, $userId) {
        $playlistTbl = Engine_Api::_()->getDbTable('playlists', 'ynvideo');
        $playlistName = $playlistTbl->info('name');

        $playlistAssoTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
        $playlistAssoName = $playlistAssoTbl->info('name');

        $select = $playlistTbl->select()->from($playlistName);
        $select->join($playlistAssoName, "$playlistAssoName.playlist_id = $playlistName.playlist_id", null);
        $select->where("$playlistName.user_id = $userId");
        $select->where("$playlistAssoName.video_id = $videoId");
        $select->limit(1);

        $row = $playlistTbl->fetchAll($select);

        if (count($row) > 0)
            return true;
        return false;
    }

    public function addVideoToWatchLater($videoId, $userId) {
        $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');

        $row = $watchLaterTbl->fetchRow(array("video_id = $videoId", "user_id = $userId"));
        if (!$row) {
            $watchLater = $watchLaterTbl->createRow();
            $watchLater->video_id = $videoId;
            $watchLater->user_id = $userId;
            $watchLater->watched = 0;
            $watchLater->creation_date = date('Y-m-d H:i:s');
            $watchLater->save();

            return $watchLater;
        } else {
            throw new Ynvideo_Model_ExistedException();
        }
    }

    public function addVideoToFavorite($videoId, $userId) {
        $favoriteTbl = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
        $row = $favoriteTbl->fetchRow(array("video_id = $videoId", "user_id = $userId"));
        if ($row == null) {
            $favorite = $favoriteTbl->createRow();
            $favorite->video_id = $videoId;
            $favorite->user_id = $userId;
            $favorite->save();

            $video = Engine_Api::_()->getItem('video', $videoId);
            $video->favorite_count = new Zend_Db_Expr('favorite_count + 1');
            $video->save();

//            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            // set the view permission on the favorite, by the view permission on the video
//            foreach ($roles as $role) {
//                if ($auth->isAllowed($video, $role, 'view')) {
//                    $auth->setAllowed($favorite, $role, 'view', true);
//                }
//            }
            // set the comment permission on the favorite, by the comment permission on the video
//            foreach ($roles as $role) {
//                if ($auth->isAllowed($video, $role, 'comment')) {
//                    $auth->setAllowed($favorite, $role, 'comment', true);
//                }
//            }

            return $favorite;
        } else {
            throw new Ynvideo_Model_ExistedException();
        }
    }

    public function removeVideoFromPlaylist($videoId, $playlistId) {
        $playlistAssoc = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo')->fetchRow(array("video_id = $videoId", "playlist_id = $playlistId"));
        if ($playlistAssoc) {
            if ($playlistAssoc->delete()) {
                $playlist = Engine_Api::_()->getItem('ynvideo_playlist', $playlistId);
                if ($playlist->video_count > 0) {
                    $playlist->video_count = new Zend_Db_Expr('video_count - 1');
                    $playlist->save();
                }

                return true;
            }
        }
        return false;
    }

    public function removeVideoFromWatchLater($videoId, $userId) {
        $watchLater = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo')->fetchRow(array("video_id = $videoId", "user_id = $userId"));
        if ($watchLater) {
            return $watchLater->delete();
        }
        return false;
    }

    public function removeVideoFromFavorite($videoId, $userId) {
        $favorite = Engine_Api::_()->getDbTable('favorites', 'ynvideo')->fetchRow(array("video_id = $videoId", "user_id = $userId"));
        if ($favorite) {
            return $favorite->delete();
        }
        return false;
    }

    public function getAllowedMaxValue($type, $levelId, $name) {
        $mtable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $msselect = $mtable->select()
            ->where('type = ?', $type)
            ->where('level_id = ?', $levelId)
            ->where('name = ?', $name);
        $allow = $mtable->fetchRow($msselect);
        
        switch ($allow->value) {
            case 3:
            case 5:
                if (!empty($allow->params)) {
                    return $allow->params;
                } else {
                    return $allow->value;
                }
            default:
                return $allow->value;
        }
    }
    
    public function fetchVideoLargeThumbnail($videos = null) 
    {        
        if ($videos) {            
            if ($videos instanceof Ynvideo_Model_Video) {
                $arrVideos = array($videos);
            } else {
                $arrVideos = $videos;
            }
        }
        if ($arrVideos) {
            foreach ($arrVideos as $video) {
                if (!$video->large_photo_id) {
                    $adapter = Ynvideo_Plugin_Factory::getPlugin($video->type);
                    $adapter->setParams(array('code' => $video->code, 'video_id' => $video->getIdentity()));                    
                    $video->storeThumbnail($adapter->getVideoLargeImage(), 'large');
                }
            }
        }
    }
}
