<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
      
      function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }       ?>
<script type="text/javascript">

function multiDelete()
{
  var is_select = false; 
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      if(inputs[i].checked == true)
      {
          is_select = true;
          break;
      }
    }
  }
  if ( is_select == false)
    {
        alert("<?php echo $this->translate('You do not select any item(s) to delete');?>");return false;
    } 
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected items ?');?>");
}
function  deleteItem(url)
{
          if ( confirm("<?php echo $this->translate('Are you sure to delete this item ?');?>"))
          {
              window.location.href = url;
          }
}
function selectAll(obj)
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = obj.checked;
    }
  }
}
</script>
<form action="" method="post" id='multidelete_form'>
<table cellpading="0" cellspacing="0" border="0" width="100%">
           <tr>
               <td>
           <h3  style="margin-bottom: 10px; margin-top: 9px;">
            <?php echo $this->translate('Download List'); ?>
            </h3>
          <ul  align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
                 <?php if (count($this->downloadlist)> 0): ?>                 
                    <table cellpadding="0" cellspacing="0" width="100%">
                    <tr style="background:#EFF4F7 none repeat scroll 0 0;">
                        <td height="25px" width="40%" style="font-weight:bold;padding:2px 2px 2px 10px;">
                        <input type="checkbox" id="selectAllDownload" name="selectAllDownload" onclick="selectAll(this);"/>
                        <span style="padding-left: 10px"><?php echo $this->translate('Name'); ?></span>
                        </td>
                        <td style="font-weight:bold;padding:2px; padding-right: 5px"><?php echo $this->translate('Action'); ?></td>
                
                    </tr>
                     <?php $index = 0; foreach($this->downloadlist as $iSong):  ?>
                     <tr id="download_list_id_<?php echo $index;?>">
                        <td width="82%" style="padding:10px;border-bottom:1px solid #E9F4FA;" >
                             <input type="checkbox" name="delete_<?php echo $iSong->list_id;?>" value="<?php echo $iSong->list_id;?>"/>
                            <div style="padding-left: 10px;" class="mp3_title_link">
                            <?php if ($iSong->album_title == ""): ?><!-- Song Item-->
                             <a rel="balloon_<?php echo $iSong->song_id; ?>" href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$iSong->album_id,'song_id'=>$iSong->song_id), 'mp3music_album_song');?>',800,565)"><?php echo $iSong->title;?></a> 
                                <div class="mp3_album_info" style="padding-top:5px;">
                                    <?php echo $this->translate('Type : '); ?><span><?php echo $this->translate("Song") ?></span>  | 
                                   <?php if ($iSong->singer_id == 0): echo $this->translate('Artists'); else: echo $this->translate('Singer'); endif;?> :   
                                  <?php if($iSong->singer_id == 0 && $iSong->other_singer == ""):
                                        echo $this->translate(" Not Update"); 
                                    endif;   ?> 
                                <?php if($iSong->singer_id != 0):  
                                     $singer =  Engine_Api::_()->getItem('mp3music_singer', $iSong->singer_id);
                                      if($singer):     
                                            echo $this->htmlLink($this->url(array('search'=>'singer','id'=>$singer->singer_id,'title'=>null,'type'=>null), 'mp3music_search'),
                                            strlen($singer->title)>20?substr($singer->title,0,20).'...':$singer->title,
                                            array('class'=>''));
                                         else:
                                            echo $this->translate("Not Update");
                                             endif; 
                                     endif;
                                    
                                    if($iSong->other_singer != "" && $iSong->singer_id == 0): 
                                    echo $this->htmlLink($this->url(array('search'=>'singer','title'=>$iSong->other_singer,'id'=>null), 'mp3music_search'),
                                            strlen($iSong->other_singer)>20?substr($iSong->other_singer,0,20).'...':$iSong->other_singer,
                                array('class'=>''));
                        endif;?>
                                </div>
                            <?php else:  $album = Engine_Api::_()->getItem('mp3music_album', $iSong->album);?> <!-- Album Item-->
                               <a href="javascript:;"  class ='title_thongtin2'  onClick="return openPage('<?php echo $this->url(array('album_id'=>$iSong->album), 'mp3music_album');?>',800,565)"><?php echo $album->getTitle();?></a> 
                                <div class="mp3_album_info" style="padding-top:5px;">
                                    <?php echo $this->translate('Type : '); ?><span> <?php  echo $this->translate("Album"); ?></span>
                                     |
                                    <?php echo $this->translate("Seller : "); ?>
                                    <?php echo $album->getOwner(); ?>
                                </div>
                                <div id="album_item_download_<?php echo $iSong->album; ?>" ></div>
                            <?php endif; ?>
                             </div>    
                        </td>
                      
                        <td style="border-bottom:1px solid #E9F4FA;">
                        <div>
                         <?php if ($iSong->album_title == ""):  ?><!-- Song Item-->                          
                               <a class="mp3music_playmusic" href="javascript:;" title="Play" onClick="return openPage('<?php echo $this->url(array('album_id'=>$iSong->album_id,'song_id'=>$iSong->song_id), 'mp3music_album_song');?>',800,565)"><img src="./application/modules/Mp3music/externals/images/music/icon_play.png" /></a>
                                 <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() > 0): 
                                   echo $this->htmlLink(
                                   $this->url(array('playlist_id'=>'0','song_id'=>$iSong->song_id), 'mp3music_playlist_append'),
                                    '<img src="./application/modules/Mp3music/externals/images/music/icon_add.png" />',
                                    array('class'=>'smoothbox music_player_tracks_add','title'=>'Add to Playlist') );   ?>
                                  <?php endif;  $song = Engine_Api::_()->getItem('mp3music_album_song', $iSong->song_id);   
                                   echo $this->htmlLink('./application/modules/Mp3music/externals/scripts/download.php?idsong='.$song->song_id,'<img src="./application/modules/Mp3music/externals/images/music/icon_download.png" />',array(
                                  'class'=>'music_player_tracks_url',
                                  'type'=>'audio','title'=>$this->translate('Download Item'),
                                  'rel'=>$song->song_id) );  ?>
                                   <a title="<?php echo $this->translate('Remove Item');?>" href="javascript:deleteItem('<?php echo $this->url(array(),'mp3music_cart_deletedownload');?>/delete/<?php echo $iSong->list_id; ?>')" ><img src="./application/modules/Mp3music/externals/images/music/icon_delete.png" /></a>               
                       <?php else: $album = Engine_Api::_()->getItem('mp3music_album', $iSong->album); ?>
                            <?php echo $this->htmlLink('./application/modules/Mp3music/externals/scripts/download.php?idalbum='.$album->album_id,'<img src="./application/modules/Mp3music/externals/images/music/icon_download_zip.png" />',array(
                                  'class'=>'music_player_tracks_url',
                                  'type'=>'audio',
                                  'title'=> $album->type == 1?$this->translate('Download Track'):$this->translate('Download Album'),
                                  'rel'=>$song->song_id) );  ?>
                            <a class="mp3music_playmusic" href="javascript:;" title="<?php if($album->type == 1) echo $this->translate('Play Track'); else echo $this->translate('Play Album');?>" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><img src="./application/modules/Mp3music/externals/images/music/icon_play.png" /></a> 
                            <a class="mp3music_download" id="download_button_<?php echo $iSong->album;?>" title="<?php if($album->type == 1) echo $this->translate('View Track'); else echo $this->translate('View Song List');?>" href="javascript:loadanblumitem(<?php echo $iSong->album;?>)"><img src="./application/modules/Mp3music/externals/images/music/icon_download.png" /></a>
                            <a title="<?php echo $this->translate('Remove Item');?>" href="javascript:deleteItem('<?php echo $this->url(array(),'mp3music_cart_deletedownload');?>/delete/<?php echo $iSong->list_id; ?>')" ><img src="./application/modules/Mp3music/externals/images/music/icon_delete.png" /></a>               
                       <?php endif; ?>
                        </div>    
                        </td>  
                    </tr>
                     <?php $index++;  endforeach; ?>
                    <tr  style="background:#EFF4F7 none repeat scroll 0 0;">
                    <td colspan="3" align="center" style="padding: 10px 30px 10px 10px;">
                    <div>
                        <div style="padding:5px"></div> 
                          <input style="margin-top: 8px;" type="checkbox"  onclick="selectAll(this);"/>
                            <span style="font-weight: bold; padding-left: 10px; padding-right: 10px;" >
                            <a style="text-decoration: none">
                            <?php echo $this->translate("Select all"); ?>
                            </a>
                            </span>
                           <button style="width: 100px" onclick="return multiDelete();"><?php echo $this->translate('Remove'); ?> </button>
                      </div> 
                      <div style="float:right"> 
                         <?php echo  $this->paginationControl($this->downloadlist); ?> 
                      </div>
                    </td>
                  
                 </tr>  
                </table>  
                 <?php else: ?>
                        <div style="text-align:center; margin:10px"><?php echo $this->translate('There are no items in your download list.'); ?></div> 
                 <?php endif; ?>
                </ul>
               </td>               
           </tr>
   </table>
   </form>
