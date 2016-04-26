<?php
$this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js'); 
      
?>
<ul class="global_form_box" style="margin-bottom: 15px;">
<div class="mp3_album_widgets">  
<?php 
        $index = 0;
        foreach ($this->paginatorNewMusic as $album): 
        if($album->getSongIDFirst()): $index++ ;?>
        <li class="mp3_title_link">
          <div class="mp3_image_album" title="<?php echo $album->title ?>"> 
                <a onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,890)">   
               <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>
               </a>
          </div>
          <div class="mp3_title_album_right" >    
                 <a title="<?php echo $album->title; ?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,890)"><?php echo strlen($album->title)>14?substr($album->title,0,10).'...':$album->title;?></a><br/>
                  <div class="mp3_album_info">  
                  <?php echo $this->translate('Downloads: %s', $album->download_count) ?> <br />       
                  <?php echo $this->translate('Author: ');?>
                  <a  href="<?php echo $album->getOwner()->getHref()?>" class ='title_thongtin3'><?php echo strlen($album->getOwner()->getTitle())>10?substr($album->getOwner()->getTitle(),0,7).'...':$album->getOwner()->getTitle();?></a>
                  </div>
          </div>
          </li>
         <?php endif; endforeach; ?>    
           
        <?php if($index >= $this->limit): ?>
        <li class="mp3_link_more" style="padding-top: 10px;">  
        <?php echo $this->htmlLink($this->url(array('typesearch'=>'browse_new_albums'), 'mp3music_browse_new_albums'),
                     $this->translate('&raquo; View more'),   
                    array('class'=>'')); ?>
       </li>
       <?php endif; ?>  
</div>
 </ul>

