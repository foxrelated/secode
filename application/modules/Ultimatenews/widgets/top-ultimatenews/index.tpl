<ul>
    <?php
    foreach ($this->items as $item):
    ?>      	
        <li class="layout_ultimatenews_popular_new">          
            <div class='widget_innerholder_ultimatenews'>
            	<?php if($item->logo_icon != "" && $item->mini_logo):?>
                <p style="float: left; margin-top: 1px; height:10px; padding-right: 5px"><img height="16px" width="16px" src="<?php echo $item->logo_icon?>" /></p> 	
                <?php endif;?>
                <h4 class="ynnew-tt3"><?php echo $this->htmlLink($item->getHref(), $item->title, array('target' => '_parent'))?></h4>               
                <p class="ynultimatenews-info3">
                    <?php echo $this->translate('Posted') . " " . $item->pubDate_parse;// . " " . $this->translate('by') . ": " . Engine_Api::_()->getItem('user',$item->owner_id);?>
                </p>
                <p class="ynultimatenews-bd3">
                    <?php
                        if ($item->image != "")
						{
							 if($item->photo_id)
							 {
								 $img_path = $item->getPhotoUrl();
								 echo("<img width = '65' src='" . $img_path . "' align='left'  style='padding-right:10px;' />");
							 }
							 else {
								 $img_path = $item->image;
								 echo $img_path;
							 }
                            //echo $img_path;
						}
						else {
							echo '<img width = "65" align="left" src="./application/modules/Ultimatenews/externals/images/small_news.png" style="padding-right:10px;" alt=""/> ';
						}

	                    $content = $item->description ? $item->description : $item->content;
	                    echo $this->feedDescription($content);
					?>
                </p>	             
                <div style="clear:both;"></div>
                <p class="ynultimatenews-ft3">
                    <?php echo $this->translate('Views') . ": " . "<font style='font-weight:bold'>" . $item->count_view . "</font>";?>
                    <a class="ynultimatenews-viewmore" href="<?php echo($item->getHref());?>" target="_blank" ><?php echo $this->translate('View more') . '...';?></a>
                </p>
            </div>
        </li>
    <?php endforeach;?>
</ul>
