<?php 
    //Get Page
    $page = $this->page
?>
<h2>
    <?php
        //Page Name
        echo $this->translate("$page->title")
    ?>
</h2>
<div>
    <?php 
        //Page Conntent
        echo $page->body
    ?>
</div>

