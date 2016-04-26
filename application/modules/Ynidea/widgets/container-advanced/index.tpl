<div class='generic_layout_container ynideabox-layout-container'>
    <ul>        
    <?php
		$index = 0;
        $total = count($this->elements);
		foreach ($this->elements as $child):
            // $width = (isset($this->widths[$index]) && $this->widths[$index])?$this->widths[$index]:'auto'; 
            $index +=1;
    ?>
        <li style="width: <?php echo (100/$total-2) ?>%; padding: 0 1%; float: left;">
            <?php echo $child -> render();?>
        </li>
		<?php endforeach;?>
	</ul>
    <div style="clear: left;"></div>
</div>
