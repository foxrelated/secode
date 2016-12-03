<?php
/**
 * SocialEnginePro
 * @author Isabek Tashiev <isabek2309@gmail.com>
 */
/**
 * @category   Application_Extensions
 * @package    
 */
?>
<script type="text/javascript">
    window.addEvent('domready', function(){      
        var margin_left = 305;
        var margin_right = 0;
        var p_width = -305;
        var max_z_index = 7000;
        var min_z_index = 1000;
        $$('.socialslider_frame').each(function(elem){
            var marginLeft = $(elem).getStyle('margin-left').toInt();
            $(elem).set('morph',{
                duration: 'normal'
            }); 
            $(elem).addEvents({
                mouseover: function(){    
                    if(marginLeft==p_width)
                    {
                        if( $(elem).getStyle('left') == 'auto')
                        {
                            $(elem).setStyle('left',margin_right);
                        }
                        this.morph({
                            'left': margin_left,
                            'z-index': max_z_index
                        });                    
                    }
                    else
                    {  
                        if( $(elem).getStyle('right') == 'auto')
                        {
                            $(elem).setStyle('right',margin_right);
                        }
                        this.morph({
                            'right':  margin_left,
                            'z-index': max_z_index
                        }); 
                    }            
                } ,
                mouseleave: function(){
                    if(marginLeft==p_width)
                    {
                        this.morph({
                            'left': margin_right,
                            'z-index': min_z_index
                        }); 
                    } 
                    else
                    {
                        this.morph({
                            'right': margin_right,
                            'z-index': min_z_index
                        }); 
                    }          
                } 
            });
        });    
    });
</script>
<div id="sep_slider_<?php echo $this->location; ?>" class="sep_slider">

    <?php
    $index = 0;
    ?>
    <?php foreach ($this->buttons as $button): ?>
        <?php
        $url = $button->getFileUrl();

        if ($url === NULL) {
            continue;
        }
        ?>
        <div class="socialslider_frame" id="socialslider_frame"  style="z-index: <?php echo 1000 - $index * 10 ?>;" >
            <div class="socialslider_panel" id="socialslider_panel" style="background: <?php echo '#' . $button->button_color; ?>;">
                <div class="socialslider_inner">

                    <div style="margin: 0; padding-top:0; padding-bottom: 0;">
                        <?php echo $button; ?>
                    </div>

                    <?php if ($button->button_type == 'youtube'): ?>
                        <div class ="youtube_videos">
                            <ul class="youtube_videos_list">
                                <?php foreach ($this->videos as $video): ?>
                                    <li class=""> 
                                        <a href="<?php echo $video[3]; ?>" target="_blank" class="youtube_videos_link" >
                                            <img class="youtube_videos_list_element_img" style="width: 61px ; height:45px ;" src="<?php echo $video['2']; ?>" alt="" >
                                        </a>
                                        <div class="youtube_videos_list_details">
                                            <a class="youtube_videos_title_link" href="<?php echo $video[3]; ?>" target="_blank" ><?php echo $video[0]; ?></a>
                                            <span class="youtube_videos_views"><?php echo 'views: ' . $video[1]; ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="socialslider_head" id="socialslider_head" style=" margin-top:<?php echo (10 + $index++ * 90) . 'px'; ?>; background-image: url('<?php echo $url; ?>');">    
            </div>
        </div>
    <?php endforeach; ?>
</div>
