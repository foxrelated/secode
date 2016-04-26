<h4><?php echo $this -> translate("Select videos");?></h4>
<div>
    <input type='checkbox' class='checkbox' id = 'selectall'/>
    <label for="selectall" class="optional"><?php echo $this -> translate("Select all");?></label>
</div>
</br></br>
 <?php $itemPerPage = $this -> itemPerPage;
    $videos = $this -> videos;
    $numberPage = ceil(count($videos)/$itemPerPage);
    $vCount = 0;
    for($index = 1; $index <= $numberPage; $index ++): ?>
        <ul id="page_<?php echo $index ?>" class="ynvideochannel_videos_page" <?php if($index !== 1) echo 'style="display: none"'?>>
            <?php for ($vCount; $vCount < count($videos); $vCount++): ?>
                <li>
                    <input type='checkbox' class='checkbox' name="videos[]" id="page_<?php echo $index ?>" value="<?php echo $videos[$vCount]['video_id']?>"/>
                    <div><?php echo $videos[$vCount]['title']?></div>
                    <img src="<?php echo $videos[$vCount]['image_path']?>" width="200">
                    <div><?php echo  date('j F Y', $videos[$vCount]['time_stamp'])?></div>
                </li>
                <?php if((($vCount + 1) % $itemPerPage) == 0)
                {
                    $vCount++;
                    break;
                }?>
            <?php endfor;?>
        </ul>
    <?php endfor;?>
<input type="hidden" id ="current_page" value="1"/>
<div class="pages">
    <ul class="paginationControl">
        <?php
        for($index = 1; $index <= $numberPage; $index ++): ?>
        <li class="ynvideochannel_page_control <?php if($index == 1) echo 'selected';?>" id = "page_control_<?php echo $index?>" >
           <a href="javascript:;" onclick="openPage(<?php echo $index?>)"><?php echo $index?></a>
        </li>
        <?php endfor;?>
    </ul>
</div>
<script type="text/javascript">

    en4.core.runonce.add(function(){
        $('selectall').addEvent('click', function(){
            var checked  = $(this).checked;
            var current_page = $('current_page').value;
            var checkboxes = $$('#page_' + current_page + ' input[type=checkbox]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })
    });
        var openPage = function(pageId){
        $('current_page').value = pageId ;
        $$('.ynvideochannel_videos_page').hide();
        $$('.ynvideochannel_page_control').each(function(ele, index){
            ele.removeClass('selected');
        });
        $('page_' + pageId).show();
        $$('#page_control_' + pageId).addClass('selected');
    }
</script>