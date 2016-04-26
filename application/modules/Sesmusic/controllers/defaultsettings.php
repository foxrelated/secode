<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

//Album home Page
$widgetOrder = 1;

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sesmusic_index_home')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_index_home',
      'displayname' => 'Advanced Music - Music Album Home Page',
      'title' => 'Music Album Home',
      'description' => 'This is the music album home page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $topcontainer_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'left',
      'parent_content_id' => $container_id,
      'order' => 4,
      'params' => '',
  ));
  $left_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $topcontainer_id,
      'order' => 6,
      'params' => '',
  ));
  $topmiddle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.music-home-error',
      'parent_content_id' => $topmiddle_id,
      'order' => $widgetOrder++
  ));


  //Left side
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.album-song-playlist-artist-day-of-the',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Album of the Day","contentType":"album","album_title":"","album_id":"","information":["likeCount","commentCount","viewCount","ratingCount","title","postedby","songsCount","favouriteCount"],"starttime":"","endtime":"","nomobile":"0","name":"sesmusic.album-song-playlist-artist-day-of-the"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.album-song-playlist-artist-day-of-the',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Song of the Day","contentType":"albumsong","album_title":"","album_id":"","information":["likeCount","commentCount","viewCount","ratingCount","title","postedby","songsCount","favouriteCount"],"starttime":"","endtime":"","nomobile":"0","name":"sesmusic.album-song-playlist-artist-day-of-the"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"download_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","postedby"],"height":"150","width":"100","limit":"3","title":"Most Downloaded Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"like_count","viewType":"listview","showPhoto":"1","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"110","width":"100","limit":"3","title":"Most Liked Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"recommanded","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recommended Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"comment_count","viewType":"listview","showPhoto":"1","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"100","width":"100","limit":"3","title":"Most Commented Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'parent_content_id' => $left_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));


  //Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.search',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '["[]"]',
  ));


  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"songs","popularity":"creation_date","displayContentType":"upcoming","information":["songsCount","ratingCount","downloadCount","playCount","title","postedby","share","favourite","addplaylist"],"viewType":"horizontal","height":"150","width":"163","limit":"12","title":"New Releases","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"albums","popularity":"creation_date","displayContentType":"featured","information":["likeCount","songsCount","ratingCount","title","postedby","share","favourite","addplaylist"],"viewType":"horizontal","height":"150","width":"163","limit":"10","title":"Featured Music Albums","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"carouselview","popularity":"featured","information":["postedby","viewCount","favouriteCount","songsListShow"],"viewType":"horizontal","height":"220","width":"216","limit":"4","title":"Featured Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.tabbed-widget-songs',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"height":"150","width":"159","showTabType":"1","search_type":["recently1Created","play1Count","most1Rated","most1Downloaded","featured","sponsored"],"default":"play1Count","information":["ratingStars","postedby","favourite","addplaylist","share"],"limit_data":"12","title":"Songs","nomobile":"0","name":"sesmusic.tabbed-widget-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.tabbed-widget',
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"height":"150","width":"159","showTabType":"1","search_type":["recently1Created","song1Count","most1Rated","hot","featured","sponsored"],"default":"recently1Created","information":["songCount","title","postedby","favourite","addplaylist","share"],"limit_data":"12","title":"Music Albums","nomobile":"0","name":"sesmusic.tabbed-widget"}',
  ));


  //Right side
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"songs","popularity":"like_count","displayContentType":"featured","information":["viewCount","downloadCount","playCount","title","postedby","share","favourite","addplaylist"],"viewType":"vertical","height":"220","width":"200","limit":"6","title":"Featured Songs","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recently Uploaded Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.category',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"song","showType":"tagcloud","image":"1","color":"#000","text_height":"17","height":"150","title":"Categories","nomobile":"0","name":"sesmusic.category"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-artists',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"favourite_count","viewType":"listview","height":"100","width":"100","limit":"3","title":"Popular Artists","nomobile":"0","name":"sesmusic.popular-artists"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"gridview","popularity":"view_count","information":["postedby","viewCount","favouriteCount"],"viewType":"horizontal","height":"200","width":"50","limit":"3","title":"Popular Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"play_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Played Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sesmusic.you-may-also-like-album-songs',
      'parent_content_id' => $right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Music You May Also Like","showPhoto":"1","information":["likeCount","commentCount","viewCount","ratingCount","songCount","title","postedby"],"viewType":"listView","height":"200","width":"100","itemCount":"3","nomobile":"0","name":"sesmusic.you-may-also-like-album-songs"}',
  ));
}

//Album Browse Page
$widgetOrder = 1;

//Music album browse page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_index_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_index_browse',
      'displayname' => 'Advanced Music - Browse Music Albums Page',
      'title' => 'Browse Music Albums',
      'description' => 'This page lists all music albums.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();

  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();

  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main Top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-songs-alphabet',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"albums","nomobile":"0","name":"sesmusic.album-songs-alphabet"}',
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '["[]"]',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"albums","popularity":"creation_date","displayContentType":"sponsored","information":["songsCount","ratingCount","title","postedby","share","favourite","addplaylist"],"viewType":"horizontal","height":"150","width":"150","limit":"15","title":"Sponsored Music Albums","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","popularity":"creation_date","information":["featured","sponsored","hot","likeCount","commentCount","viewCount","title","postedby","favourite","addplaylist","share"],"height":"220","width":"225","itemCount":"12","title":"","nomobile":"0","name":"sesmusic.browse-albums"}',
  ));

  //Rigth Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-song-playlist-artist-day-of-the',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Album of the Day","contentType":"album","album_title":"She Wolf","album_id":"12","information":["featured","sponsored","hot","likeCount","commentCount","viewCount","ratingCount","title","postedby","songsCount","favouriteCount"],"starttime":"2015-03-18 01:00:00","endtime":"2021-10-31 01:00:00","nomobile":"0","name":"sesmusic.album-song-playlist-artist-day-of-the"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"albums","popularity":"creation_date","displayContentType":"hot","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby","share","favourite","addplaylist"],"viewType":"vertical","height":"200","width":"200","limit":"5","title":"Hot Music Albums","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"searchOptionsType":["searchBox","category","view","show","artists"],"title":"","nomobile":"0","name":"sesmusic.browse-search"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu-quick',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"rating","viewType":"listview","showPhoto":"0","information":["featured","sponsored","hot","likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"200","limit":"3","title":"Most Rated Music Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_album","viewType":"listView","criteria":"by_me","information":["hot","featured","postedby","sponsored","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Albums","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"song_count","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Having Maximum Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"comment_count","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Commented Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));
}

//Song Browse Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_browse',
      'displayname' => 'Advanced Music - Browse Songs Page',
      'title' => 'Browse Songs',
      'description' => 'This page lists all songs of albums.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Top Main
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-songs-alphabet',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"songs","nomobile":"0","name":"sesmusic.album-songs-alphabet"}',
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"listview","Type":"1","popularity":"view_count","information":["featured","sponsored","hot","playCount","downloadCount","likeCount","commentCount","viewCount","favouriteCount","ratingStars","artists","addplaylist","downloadIcon","share","report","title","postedby","favourite","category"],"itemCount":"10","title":"","nomobile":"0","name":"sesmusic.browse-songs"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.songs-browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"searchOptionsType":["searchBox","category","show","artists"],"title":"","nomobile":"0","name":"sesmusic.songs-browse-search"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.category',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"song","showType":"simple","image":"0","color":"#00f","text_height":"15","height":"150","title":"Categories","nomobile":"0","name":"sesmusic.category"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"songs","popularity":"favourite_count","displayContentType":"sponsored","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","downloadCount","playCount","title","postedby","share","favourite","addplaylist"],"viewType":"vertical","height":"200","width":"200","limit":"6","title":"Sponsored Songs","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"play_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Played Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"recommanded","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recommended Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.featured-sponsored-hot-carousel',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"contentType":"songs","popularity":"creation_date","displayContentType":"hot","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","downloadCount","playCount","title","postedby","share","favourite","addplaylist"],"viewType":"vertical","height":"200","width":"200","limit":"6","title":"Hot Songs","nomobile":"0","name":"sesmusic.featured-sponsored-hot-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"like_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Liked Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"gridview","popularity":"download_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Downloaded Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Highest Rated Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));
}


//Playlists Browse Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_playlist_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_playlist_browse',
      'displayname' => 'Advanced Music - Browse Playlists Page',
      'title' => 'Browse Playlists',
      'description' => 'This page lists all playlists.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Top Main
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-songs-alphabet',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"","contentType":"playlists","nomobile":"0","name":"sesmusic.album-songs-alphabet"}',
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"paginationType":"0","information":["viewCount","title","postedby","share"],"height":"200","width":"200","itemCount":"5","title":"","nomobile":"0","name":"sesmusic.browse-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"paginationType":"0","information":["viewCount","title","postedby","share"],"height":"200","width":"200","itemCount":"5","title":"","nomobile":"0","name":"sesmusic.browse-playlists"}',
  ));


  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"featured","information":["postedby","viewCount","favouriteCount","songsListShow"],"width":"100","limit":"1","title":"Featured Playlist","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"creation_date","information":["postedby","viewCount","favouriteCount"],"width":"100","limit":"3","title":"Most Recent Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"view_count","information":["postedby","viewCount","favouriteCount"],"width":"100","limit":"3","title":"Most Viewed Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"recommanded","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recommended Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"gridview","criteria":"by_me","information":["postedby","sponsored","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"140","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//Artist Browse Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_artist_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_artist_browse',
      'displayname' => 'Advanced Music - Browse Artists Page',
      'title' => 'Browse Artists',
      'description' => 'This page display lists of artists.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Top Main
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"paginationType":"1","information":"","height":"200","width":"225","itemCount":"16","title":"","nomobile":"0","name":"sesmusic.browse-artists"}',
  ));

  //Right
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-song-playlist-artist-day-of-the',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Artist of the Day","contentType":"artist","album_title":"","album_id":"","information":["featured","sponsored","hot","likeCount","commentCount","viewCount","ratingCount","title","postedby","songsCount","favouriteCount"],"starttime":"","endtime":"","nomobile":"0","name":"sesmusic.album-song-playlist-artist-day-of-the"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"rating","viewType":"listview","height":"200","width":"100","limit":"3","title":"Most Popular Artist","nomobile":"0","name":"sesmusic.popular-artists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"favourite_count","viewType":"listview","height":"200","width":"100","limit":"3","title":"Most Favorite Artists","nomobile":"0","name":"sesmusic.popular-artists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recommended Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//Album Manage Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_index_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_index_manage',
      'displayname' => 'Advanced Music - Manage Music Albums Page',
      'title' => 'Manage Music Albums',
      'description' => 'This page is the manage music album page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_album","viewType":"listView","criteria":"by_me","information":["postedby","songsCount","ratingCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Albums","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"recommanded","popularity":"creation_date","viewType":"listview","showPhoto":"0","information":["featured","sponsored","hot","likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"200","limit":"3","title":"Recommended Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));
}

//My Album Favourite Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_album_favourite-albums')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_album_favourite-albums',
      'displayname' => 'Advanced Music - Manage Favorite Music Albums Page',
      'title' => 'Manage Favorite Music Albums',
      'description' => 'This page is the manage favorite music albums page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-music-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["ratingStars","favouriteCount","ratingCount","category","description","songCount","title","postedby","favourite","addplaylist","share","showSongsList"],"height":"200","width":"225","itemCount":"10","title":"","nomobile":"0","name":"sesmusic.manage-music-albums"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"favourite_count","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Favorite Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_album","viewType":"listView","criteria":"by_me","information":["postedby","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Albums","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}


//My Album Liked Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_album_like-albums')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_album_like-albums',
      'displayname' => 'Advanced Music - Manage Liked Music Albums Page',
      'title' => 'Manage Liked Music Albums',
      'description' => 'This page is the manage liked music albums page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-music-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["ratingStars","favouriteCount","ratingCount","category","description","songCount","title","postedby","favourite","addplaylist","share","showSongsList"],"height":"200","width":"225","itemCount":"10","title":"","nomobile":"0","name":"sesmusic.manage-music-albums"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"like_count","viewType":"listview","showPhoto":"1","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Liked Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_album","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Albums","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//My Album Rated Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_album_rated-albums')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_album_rated-albums',
      'displayname' => 'Advanced Music - Manage Rated Music Albums Page',
      'title' => 'Manage Rated Music Albums',
      'description' => 'This page is the manage rated music albums page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-music-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["ratingStars","favouriteCount","ratingCount","category","description","songCount","title","postedby","favourite","addplaylist","share","showSongsList"],"height":"200","width":"225","itemCount":"10","title":"","nomobile":"0","name":"sesmusic.manage-music-albums"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"rating","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Rated Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_album","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Albums","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//My Songs Like Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_like-songs')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_like-songs',
      'displayname' => 'Advanced Music - Manage Liked Songs Page',
      'title' => 'Manage Liked Songs',
      'description' => 'This page is the manage liked songs page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-album-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["playCount","downloadCount","favouriteCount","ratingStars","artists","addplaylist","downloadIcon","share","report","title","postedby","favourite","category"],"itemCount":"10","height":"200","width":"225","title":"","nomobile":"0","name":"sesmusic.manage-album-songs"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"favourite_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Favorite Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//My Songs Rated Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_rated-songs')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_rated-songs',
      'displayname' => 'Advanced Music - Manage Rated Songs Page',
      'title' => 'Manage Rated Songs',
      'description' => 'This page is the manage rated songs page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-album-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["playCount","downloadCount","favouriteCount","ratingStars","artists","addplaylist","downloadIcon","share","report","title","postedby","favourite","category"],"itemCount":"10","height":"200","width":"225","title":"","nomobile":"0","name":"sesmusic.manage-album-songs"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"rating","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Rated Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//My Songs Favourite Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_favourite-songs')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_favourite-songs',
      'displayname' => 'Advanced Music - Manage Favorite Songs Page',
      'title' => 'Manage Favorite Songs',
      'description' => 'This page is the manage favorite songs page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.manage-album-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"gridview","Type":"1","information":["playCount","downloadCount","favouriteCount","ratingStars","artists","addplaylist","downloadIcon","share","report","title","postedby","favourite","category"],"itemCount":"10","height":"200","width":"225","title":"","nomobile":"0","name":"sesmusic.manage-album-songs"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"favourite_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Favorite Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//Manage Playlist Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_playlist_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_playlist_manage',
      'displayname' => 'Advanced Music - Manage Playlists Page',
      'title' => 'Manage Playlists',
      'description' => 'This page is the manage playlists page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"carouselview","popularity":"featured","information":["postedby","viewCount","favouriteCount","songsListShow"],"viewType":"vertical","height":"240","width":"100","limit":"10","title":"Featured Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"carouselview","popularity":"creation_date","information":["postedby","viewCount","favouriteCount","songsListShow"],"viewType":"vertical","height":"240","width":"90","limit":"6","title":"Most Recent Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"gridview","popularity":"view_count","information":["postedby","viewCount","favouriteCount"],"viewType":"horizontal","height":"200","width":"100","limit":"5","title":"Most Viewed Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));
}

//My Favourite Artist Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_artist_favourite-artists')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_artist_favourite-artists',
      'displayname' => 'Advanced Music - Manage Favorite Artists Page',
      'title' => 'Manage Favorite Artists',
      'description' => 'This page is the manage favorite artists page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.favourites-link',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"favourite_count","viewType":"listview","height":"200","width":"100","limit":"3","title":"Most Favorite Artists","nomobile":"0","name":"sesmusic.popular-artists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-artists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"popularity":"rating","viewType":"listview","height":"200","width":"100","limit":"3","title":"Most Rated Artists","nomobile":"0","name":"sesmusic.popular-artists"}',
  ));
}

//Browse Lyrics Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_lyrics')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_lyrics',
      'displayname' => 'Advanced Music - Browse Lyrics Page',
      'title' => 'Browse Lyrics',
      'description' => 'This page is the browse lyrics page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-lyrics',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewType":"listview","Type":"1","information":["playCount","downloadCount","likeCount","commentCount","viewCount","favouriteCount","ratingStars","artists","addplaylist","downloadIcon","share","report","title","postedby","favourite","category"],"itemCount":"10","height":"200","width":"200","title":"","nomobile":"0","name":"sesmusic.browse-lyrics"}',
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-song-playlist-artist-day-of-the',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Song of the Day","contentType":"albumsong","album_title":"","album_id":"","information":["likeCount","commentCount","viewCount","ratingCount","title","postedby","songsCount","favouriteCount"],"starttime":"","endtime":"","nomobile":"0","name":"sesmusic.album-song-playlist-artist-day-of-the"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","viewType":"listview","popularity":"play_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Most Played Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"recommanded","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Recommended Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-playlists',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"gridview","popularity":"view_count","information":["postedby","viewCount","favouriteCount","songsListShow"],"viewType":"horizontal","height":"200","width":"100","limit":"2","title":"Popular Playlists","nomobile":"0","name":"sesmusic.popular-playlists"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite","addplaylist"],"Height":"180","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"all","popularity":"view_count","viewType":"gridview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"150","width":"100","limit":"3","title":"Popular Music Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));
}


//Album Create Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_index_create')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_index_create',
      'displayname' => 'Advanced Music - Music Album Create Page',
      'title' => 'Music Album Create',
      'description' => 'This page is the music album create page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 1,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 1,
  ));
}

//Album Edit Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_album_edit')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_album_edit',
      'displayname' => 'Advanced Music - Music Album Edit Page',
      'title' => 'Music Album Edit',
      'description' => 'This page is the music album edit page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 1,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 1,
  ));
}

//Album View Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_album_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_album_view',
      'displayname' => 'Advanced Music - Music Album View Page',
      'title' => 'Music Album View',
      'description' => 'This page displays a music album view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main Top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.breadcrumb',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"album"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.album-cover',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"height":"360","mainPhotoHeight":"320","mainPhotowidth":"330","information":["postedBy","creationDate","commentCount","viewCount","likeCount","ratingCount","ratingStars","favouriteCount","description","editButton","deleteButton","addplaylist","share","report","addFavouriteButton","photo"],"title":"","nomobile":"0","name":"sesmusic.album-cover"}'
  ));

  //Middle 
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.comments',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.albums-songs-like',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Members Who Liked This","contentType":"albums","showUsers":"all","showViewType":"0","itemCount":"8","nomobile":"0","name":"sesmusic.albums-songs-like"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.albums-songs-like',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Friends Who Liked This","contentType":"albums","showUsers":"friends","showViewType":"1","itemCount":"3","nomobile":"0","name":"sesmusic.albums-songs-like"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"other","popularity":"view_count","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"200","limit":"3","title":"Other Music Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"related","popularity":"creation_date","viewType":"listview","showPhoto":"0","information":["likeCount","commentCount","viewCount","songsCount","ratingCount","title","postedby"],"height":"200","width":"200","limit":"3","title":"Related Music Albums","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.you-may-also-like-album-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Music You May Also Like","showPhoto":"0","information":["likeCount","commentCount","viewCount","ratingCount","title","postedby"],"viewType":"listView","height":"200","width":"200","itemCount":"3","nomobile":"0","name":"sesmusic.you-may-also-like-album-songs"}',
  ));
}

//Song View Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_song_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_song_view',
      'displayname' => 'Advanced Music - Song View Page',
      'title' => 'Song View',
      'description' => 'This page displays a all music album songs.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();

  //Main Top
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.breadcrumb',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"song"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.song-cover',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => $widgetOrder++,
      'params' => '{"height":"300","mainPhotoHeight":"170","mainPhotowidth":"190","information":["postedBy","creationDate","commentCount","viewCount","likeCount","ratingCount","ratingStars","favouriteCount","playCount","playButton","editButton","deleteButton","addplaylist","share","report","printButton","downloadButton","addFavouriteButton","photo","category"],"title":"","nomobile":"0","name":"sesmusic.song-cover"}',
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 1,
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.comments',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => $widgetOrder++,
  ));

  //Right Side
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.albums-songs-like',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Members Who Liked This","contentType":"songs","showUsers":"all","showViewType":"0","itemCount":"8","nomobile":"0","name":"sesmusic.albums-songs-like"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.albums-songs-like',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"title":"Friends Who Likes This","contentType":"songs","showUsers":"friends","showViewType":"1","itemCount":"3","nomobile":"0","name":"sesmusic.albums-songs-like"}',
  ));


  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"other","viewType":"listview","popularity":"like_count","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"200","limit":"3","title":"Other Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.popular-recommanded-other-related-songs',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"showType":"related","viewType":"listview","popularity":"creation_date","information":["likeCount","commentCount","viewCount","downloadCount","playCount","ratingCount","title","postedby"],"height":"200","width":"100","limit":"3","title":"Related Songs","nomobile":"0","name":"sesmusic.popular-recommanded-other-related-songs"}',
  ));

  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => $widgetOrder++,
      'params' => '{"category":"sesmusic_albumsong","viewType":"listView","criteria":"by_me","information":["postedby","viewCount","likeCount","songsCount","ratingCount","commentCount","downloadCount","share","favourite"],"Height":" ","Width":"180","limit_data":"3","title":"Recently Viewed Songs","nomobile":"0","name":"sesmusic.recently-viewed-item"}',
  ));
}

//Playlist View Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_playlist_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_playlist_view',
      'displayname' => 'Advanced Music - Playlist View Page',
      'title' => 'Playlist View',
      'description' => 'This page displays a playlist.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
  ));
  $middle_id = $db->lastInsertId();

  //Top Main
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.breadcrumb',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"playlist"}',
  ));

  //Middle
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.profile-playlist',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"information":["editButton","deleteButton","sharePl","reportPl","addFavouriteButtonPl","viewCountPl","description","postedByPl","featured","sponsored","hot","postedBy","downloadCount","commentCount","viewCount","likeCount","ratingStars","favouriteCount","playCount","addplaylist","share","report","addFavouriteButton","downloadButton","artists","category"],"title":"","nomobile":"0","name":"sesmusic.profile-playlist"}',
  ));
}

//Artist View Page
$widgetOrder = 1;
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmusic_artist_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sesmusic_artist_view',
      'displayname' => 'Advanced Music - Artist View Page',
      'title' => 'View Artist',
      'description' => 'This page displays a artist.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
  ));
  $main_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
  ));
  $middle_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.breadcrumb',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => $widgetOrder++,
      'params' => '{"viewPageType":"artist"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmusic.profile-artist',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => 1,
      'params' => '{"informationArtist":["favouriteCountAr","ratingCountAr","description","ratingStarsAr","addFavouriteButtonAr"],"information":["featured","sponsored","hot","postedBy","downloadCount","commentCount","viewCount","likeCount","ratingStars","favouriteCount","playCount","addplaylist","share","report","downloadButton","artists","addFavouriteButton","category"],"title":"","nomobile":"0","name":"sesmusic.profile-artist"}',
  ));
}

//Add Player in footer
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', 2)
        ->where('name = ?', 'main')
        ->limit(1);
$info = $select->query()->fetch();
if ($info) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content', 'content_id')
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sesmusic.player')
          ->limit(1);
  $welcome = $select->query()->fetch();
  if ($info && !$welcome) {
    $db->query("UPDATE `engine4_core_content` SET `order` = '999' WHERE `engine4_core_content`.`name` = 'core.menu-footer' LIMIT 1;");
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sesmusic.player',
        'parent_content_id' => $info['content_id'],
        'page_id' => 2,
        'order' => 1,
    ));
  }
}

//Member Profile Page
$select = new Zend_Db_Select($db);
$select->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
if ($page_id) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sesmusic.profile-musicalbums');
  $info = $select->query()->fetch();
  if (empty($info)) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->limit(1);
    $container_id = $select->query()->fetchObject()->content_id;
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
    $middle_id = $select->query()->fetchObject()->content_id;
    $select
            ->reset('where')
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->where('page_id = ?', $page_id)
            ->limit(1);
    $tab_id = $select->query()->fetchObject();
    if ($tab_id && @$tab_id->content_id) {
      $tab_id = $tab_id->content_id;
    } else {
      $tab_id = null;
    }
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesmusic.profile-musicalbums',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order' => 999,
        'params' => '{"defaultOptionsShow":["profilemusicalbums","songofyou","playlists","favouriteSong"],"pagging":"auto_load","Height":"222","Width":"222","limit_data":"12","title":"Music","nomobile":"0","name":"sesmusic.profile-musicalbums"}',
    ));
  }
}

$music_playlist_table_exist = $db->query('SHOW TABLES LIKE \'engine4_music_playlists\'')->fetch();
if (!empty($music_playlist_table_exist)) {
  $musicimport = $db->query("SHOW COLUMNS FROM engine4_music_playlists LIKE 'musicimport'")->fetch();
  if (empty($musicimport)) {
    $db->query("ALTER TABLE `engine4_music_playlists` ADD `musicimport` TINYINT(1) NOT NULL DEFAULT '0'");
  }
}

$music_album_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sesmusic_albums\'')->fetch();
if (!empty($music_album_table_exist)) {
  $resource_type = $db->query("SHOW COLUMNS FROM engine4_sesmusic_albums LIKE 'resource_type'")->fetch();
  if (empty($resource_type)) {
    $db->query("ALTER TABLE `engine4_sesmusic_albums` ADD `resource_type` varchar(128) NOT NULL");
  }

  $resource_id = $db->query("SHOW COLUMNS FROM engine4_sesmusic_albums LIKE 'resource_id'")->fetch();
  if (empty($resource_id)) {
    $db->query("ALTER TABLE `engine4_sesmusic_albums` ADD `resource_id` int(11) NOT NULL");
  }
}



