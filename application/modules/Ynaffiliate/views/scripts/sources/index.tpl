<div id="formborder" class="ynaffiliate_list_link_block">
    <div class="ynaffiliate_list_link_items">
        <div class="ynaffiliate_descripton">
            <?php echo $this->translate('You can copy these links and share them with others') ?>
        </div>

        <?php
        if (count($this->suggests) > 0) :
            foreach ($this->suggests as $suggest) :
                if ($suggest['href'] == '') {
                    continue;
                }
        ?>
            <div class="ynaffiliate_list_link_item">
                <span class="label-cell-block"><?php echo $this->translate($suggest['suggest_title']); ?></span>
                <input class="ynaffiliate_text "type="text" readonly="readonly" id = "ynaffiliate_textarea" onclick="javascript:SelectAll($(this));" value = "<?php
                            echo Engine_Api::_()->ynaffiliate()->getAffiliateUrl($suggest['href'], $this->viewer_id);
                ?>"/>
            </div>
        <?php
            endforeach;
        endif;
        ?>
    </div>
</div>


<script type="text/javascript">
    function SelectAll(id)
    {
    	id.focus();
        id.select();
    }
</script>