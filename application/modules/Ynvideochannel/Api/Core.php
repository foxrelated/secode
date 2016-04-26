<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */

defined('YOUTUBE_API_URL') or define('YOUTUBE_API_URL', "https://www.googleapis.com/youtube/v3/");

class Ynvideochannel_Api_Core extends Core_Api_Abstract
{
    /**
     * @return mixed
     */
    protected function getApiKey()
    {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M');
    }

    /**
     * @param $url
     * @return bool
     */
    public function checkChannelUrlValid($url)
    {
        $url = trim($url);
        $pattern = "/((http|https):\/\/|)(www\.|)youtube\.com\/(channel\/|user\/)[a-zA-Z0-9]{1,}/";
        if (preg_match($pattern, $url)) {
            return true;
        }
        return false;
    }

    /**
     * @param $channelId
     * @return string
     */
    public function getChannelInfoUrl($channelId)
    {
        return YOUTUBE_API_URL."channels?part=brandingSettings&id=$channelId&key=" . $this->getApiKey();
    }

    /**
     * @param $forUsername
     * @return string
     */
    public function getChannelUserUrl($forUsername)
    {
        return YOUTUBE_API_URL."channels?part=brandingSettings&forUsername=$forUsername&key=" . $this->getApiKey();
    }

    /**
     * @param $channelId
     * @return string
     */
    public  function getChannelVideosUrl($channelId)
    {
        return YOUTUBE_API_URL."search?key=" . $this->getApiKey() . "&channelId=$channelId&part=snippet&order=date";
    }

    /**
     * @param $sQuery
     * @param $sPageToken
     * @param $iMaxResult
     * @return string
     */
    public  function getFindChannelUrl($sQuery, $sPageToken, $iMaxResult)
    {
        return YOUTUBE_API_URL.'search?order=title&part=snippet&q='.$sQuery.'&key='.$this->getApiKey() . '&pageToken='.$sPageToken.'&maxResults='.$iMaxResult;
    }

    /**
     * @param $channelCode
     * @param $userId
     * @return object|null
     */
    public function isExistChannelCode($channelCode, $userId)
    {
        //TODO check channel exists from db, if exists => return channel object
        $channelTable = Engine_Api::_() -> getDbTable('channels', 'ynvideochannel');
        $select = $channelTable->select()->where('channel_code = ?', $channelCode)->where('owner_id =?', $userId)->limit(1);
        return $channelTable -> fetchRow($select)? $channelTable -> fetchRow($select):null;
    }

    /**
     * @param $videoCode
     * @param null $channelId
     * @param null $ownerId
     * @return bool
     */
    public function isExistVideoCode($videoCode, $channelId = NULL, $ownerId = NULL)
    {
        $videoTable = Engine_Api::_() -> getDbTable('videos', 'ynvideochannel');
        if($channelId){
            $select = $videoTable->select()->where('code = ?', $videoCode)->where('channel_id =?', $channelId)->limit(1);
            return $videoTable -> fetchRow($select)?true:false;
        }
        if($ownerId){
            $select = $videoTable->select()->where('code = ?', $videoCode)->where('owner_id =?', $ownerId)->limit(1);
            return $videoTable -> fetchRow($select)?true:false;
        }
            return false;
    }

    /**
     * @param $channelFeedUrl
     * @param int $maxNum
     * @param int $iTotalVideosOfChannel
     * @param null $channnel_id
     * @return array
     */
    public function getVideosFromChannelUrl($channelFeedUrl, $maxNum = 10, $iTotalVideosOfChannel = 0, $channnel_id = null)
    {
        $videoCnt = 0;
        $outVideos = array();
        // iterate 1000 times gonna be enough
        $iThreshold = 1000;

        $i = -1;
        $iMaxResultForEachQuery = 40;

        //total videos in this channel on YouTube
        // 1 is enough to be greater than iStart
        $iTotalVideosOfChannelOnYouTube = 1;

        //this is maximum number of all videos we got
        $iMaxResult = 0;

        // this flag will true if encoutering the first existing videos
        $bIsEncoutnerFirstExist = false;
        $iStart = 0;
        $sNextPageToken = '';

        //videoCnt is number of video we got
        // maxNum is number of video we want to get
        // at first iStart have no way to greater than or equal $iTotalVideosOfChannelOnYouTube
        while ($videoCnt < $maxNum && $iStart < $iTotalVideosOfChannelOnYouTube) {
            // 0,1 ,2....
            $i++;
            if ($i >= $iThreshold) {
                break;
            }

            //we gonna make iMaxResult be standard to calculate iStart
            // each time we set iMaxResult, iStart will be changed accordingly
            $iMaxResult = $iMaxResult + $iMaxResultForEachQuery;
            $iStart = $iMaxResult - $iMaxResultForEachQuery + 1;

            // we overwrite iMaxResult here to get 50 query result each time
            $sUrl = $channelFeedUrl . '&pageToken=' . $sNextPageToken . '&maxResults=' . $iMaxResultForEachQuery;
            $oChannel = @file_get_contents($sUrl);
            $oChannel = json_decode($oChannel);
            if (!$oChannel) {
                break;
            } else if (isset($oChannel->items)) {
                if (isset($oChannel->nextPageToken))
                    $sNextPageToken = $oChannel->nextPageToken;
                // it is used to count current position of video in entry array
                $iOffsetInChannel = -1;
                if ($iTotalVideosOfChannelOnYouTube == 1) {
                    // we assume everything working correctly, this value will be assigned at the first time running
                    $iTotalVideosOfChannelOnYouTube = (int)(isset($oChannel->pageInfo->totalResults) ? $oChannel->pageInfo->totalResults : 0);
                }
                foreach ($oChannel->items as $oVideo) {
                    $iOffsetInChannel++;

                    if (!$oVideo) {
                        continue;
                    }

                    //Check video info
                    if (!isset($oVideo->snippet->title) || !isset($oVideo->id->videoId)) {
                        continue;
                    }

                    $outVideo['video_id'] = $oVideo->id->videoId;
                    $outVideo['title'] = $oVideo->snippet->title;
                    $outVideo['url'] = 'https://www.youtube.com/watch?v=' . $oVideo->id->videoId;

                    $youtubeId = $oVideo->id->videoId;

                    //check to see whether we added it or not
                    if ($this->isExistVideoCode($youtubeId, $channnel_id, null)) {
                        //until we found the first existing video
                        //because the newest videos are added to head of queue so we must check this
                        if (!$bIsEncoutnerFirstExist) {
                            $bIsEncoutnerFirstExist = true;
                            // we assume videos are added continuously
                            // this is gonna be the the last one in our video list
                            $iMaxResult = $iStart + $iOffsetInChannel + $iTotalVideosOfChannel - 1;
                            //** should notice that this calculation will show the effectiveness with large channel
                            //we will escape here, and make a request again
                        }
                        continue;
                    };

                    $outVideo['duration'] = 0;
                    if (isset($oVideo->snippet->description)) {
                        $outVideo['description'] = $oVideo->snippet->description;
                    } else {
                        $outVideo['description'] = '';
                    }
                    $outVideo['time_stamp'] = isset($oVideo->snippet->publishedAt) ? strtotime($oVideo->snippet->publishedAt) : 0;
                    $thumbnails = end($oVideo->snippet->thumbnails);
                    $outVideo['image_path'] = sprintf("%s", $thumbnails->url);
                    $outVideos[] = $outVideo;

                    $videoCnt++;

                    // get enought data, let's get out of this mess
                    if ($videoCnt >= $maxNum) {
                        break;
                    }
                }
            }
        }
        return $outVideos;
    }

    /**
     * @param $channelFeedUrl
     * @param $pageTokenPrev
     * @param $pageTokenNext
     * @return array
     */
    public function getChannels($channelFeedUrl, $userId)
    {
        $data = @file_get_contents($channelFeedUrl);
        $data = json_decode($data);
        $items = $data->items;
        $pageTokenPrev = $pageTokenNext = "";

        $aChannels = array(); //Result Array
        $aExist = array();    //Array for exist channels
        if ($data) {
            if (isset($data->prevPageToken))
                $pageTokenPrev = $data->prevPageToken;
            if (isset($data->nextPageToken))
                $pageTokenNext = $data->nextPageToken;
        }

        foreach ($items as $entry) {
            if ($entry->snippet->title != "") {
                $channel = array();
                $channel['channel_id'] = $entry->snippet->channelId;
                $channel['link'] = 'https://www.youtube.com/channel/' . $entry->snippet->channelId;
                $channel['title'] = strip_tags($entry->snippet->title);
                $channel['url'] = $this -> getChannelVideosUrl($entry->snippet->channelId);
                $channel['summary'] = $entry->snippet->description;
                //Check if channel is exist
                $existChannel = $this->isExistChannelCode($entry->snippet->channelId, $userId);
                if ($existChannel) {
                    $channel['isExist'] = $existChannel->getIdentity();
                    $channel['link'] = $existChannel->getHref(); //show chanel detail when use want check it existed.
                }
                $thumbnails = end($entry->snippet->thumbnails);
                $channel['video_image'] = sprintf("%s", $thumbnails->url);

                if ($existChannel)
                    $aExist[] = $channel;
                else
                    $aChannels[] = $channel;

            }
        }
        $aChannels = array_reverse($aChannels);
        $aChannels = array_merge($aExist, $aChannels);

        return array($aChannels, $pageTokenPrev, $pageTokenNext);
    }

    /**
     * @param $code
     * @return array|null
     */
    public function fetchVideoLink($code)
    {
        $api_key = $this -> getApiKey();
        $url = YOUTUBE_API_URL."videos?id=$code&key=$api_key&part=snippet,contentDetails";
        $data = @file_get_contents($url);
        $data = json_decode($data);
        if (empty($data->items)) {
            return null;
        } else {
            $data = $data->items[0];
        }
        $information = null;
        if($data) {
            $information = array();
            $information['title'] = sprintf("%s", $data->snippet->title);
            $start = new DateTime('@0'); // Unix epoch
            $start->add(new DateInterval($data->contentDetails->duration));
            $duration = $start->format('H') * 60 * 60 + $start->format('i') * 60 + $start->format('s');
            $information['duration'] = sprintf("%s", $duration);
            $information['description'] = sprintf("%s", $data->snippet->description);
            $thumbnails = $data->snippet->thumbnails;
            $thumbnail = end($thumbnails);
            $information['large-thumbnail'] = sprintf("%s", $thumbnail->url);
            if(isset($thumbnails -> medium))
                $thumbnail = $thumbnails -> medium;
            $information['medium-thumbnail'] = sprintf("%s", $thumbnail->url);
        }
        return $information;
    }
    public function typeCreate($label)
    {
        $field = Engine_Api::_() -> fields() -> getField('1', 'ynvideochannel_video');
        // Create new blank option
        $option = Engine_Api::_() -> fields() -> createOption('ynvideochannel_video', $field, array(
            'field_id' => $field -> field_id,
            'label' => $label,
        ));
        // Get data
        $mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynvideochannel_video');
        $metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynvideochannel_video');
        $optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynvideochannel_video');
        // Flush cache
        $mapData -> getTable() -> flushCache();
        $metaData -> getTable() -> flushCache();
        $optionData -> getTable() -> flushCache();

        return $option -> option_id;
    }

    public function getCategory($category_id)
    {
        return Engine_Api::_() -> getDbtable('categories', 'ynvideochannel') -> find($category_id) -> current();
    }

    public function getFavoriteVideos($user_id)
    {
        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $rName = $videoTable->info('name');
        $select = $videoTable->select()->from($videoTable->info('name'))->setIntegrityCheck(false);
        $videoTableName = $videoTable->info('name');
        $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideochannel');
        $favoriteTableName = $favoriteTable->info('name');
        $select ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
            ->where("$favoriteTableName.user_id = ?", $user_id);
        return $select;
    }
}

