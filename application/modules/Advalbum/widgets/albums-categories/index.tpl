<ul class = "ymb_menuRight_wapper global_form_box" style="margin-bottom: 15px; padding:0px;">
 <?php $cats = $this->categories;
    $index = 0;
    foreach($cats as $cat): $index ++; 
	$category_url = Zend_Controller_Front::getInstance()->getRouter()
		->assemble(array('action'=>'listing','sort'=>'recent','category_id'=>$cat->category_id), 'album_general', null);
	$category_url = str_replace('/albumsearch/1', '', $category_url);
	?>
    <li style="font-weight: bold; padding: 5px 5px 5px 10px;" <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); if($request-> getParam('category_id') == $cat -> category_id) echo 'class = "active"';?>>
    <?php echo $this->htmlLink($category_url,
        $this -> string() -> truncate($this->translate($cat->category_name), 30),
        array('class'=>'')); ?>
    </li>
    <?php endforeach;?>
 </ul>

