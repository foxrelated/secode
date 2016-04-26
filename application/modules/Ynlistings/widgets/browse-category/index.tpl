<div class="ynlisting_category_main clearfix">
<?php foreach ($this->categories as $category) :?>
    <div class="browse_category">
        <div class="item-category-browser">
            <?php 
                $children = $category->getChildList();
                $children_length = count($children);
                $table = Engine_Api::_() -> getDbTable('categories', 'ynlistings');
                $tree = array();
                $node = $table -> getNode($category->getIdentity());
                Engine_Api::_() -> getItemTable('ynlistings_category') -> appendChildToTree($node, $tree);
                $num_listings = 0;
                foreach ($tree as $node) {
                    $num_listings += $node->getNumOfListings();
                }
            ?>

            <div class="category-browser-top">
                <span class="category_icon"><?php echo $this->itemPhoto($category, 'thumb.icon')?></span>
                <span class="category-stat"><?php echo '('. $num_listings .')'?></span>
                <span class="category-title"><?php echo $this->htmlLink($category->getHref(), $category->getTitle())?></span>                
            </div>
            
            <?php if (($children_length > 0) && ($children_length <= 5)) : ?>
                <ul>
                    <?php foreach($children as $child) : ?>
                        <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $this -> translate($child->title))?></li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ($children_length > 5) : ?>
                <ul>
                    <?php 
                        $i = 1;
                        foreach($children as $child) : ?>
                            <?php if ($i<=5) : ?>
                                <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $this -> translate($child->title))?></li>
                            <?php endif; ?>
                    <?php 
                        $i++;
                        endforeach; ?>
                </ul>
                <span class="btn-toggle-more-category"></span>
                <div class="category-more-data">
                    <ul>
                        <?php 
                            $i = 1;
                            foreach($children as $child) : ?>
                                <?php if ($i>5) : ?>
                                    <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $this -> translate($child->title))?></li>
                                <?php endif; ?>
                        <?php 
                            $i++;
                            endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach;?>
</div>

<script type="text/javascript">

    //Function outerclick
    (function($,$$){
      var events;
      var check = function(e){
        var target = $(e.target);
        var parents = target.getParents();
        events.each(function(item){
          var element = item.element;
          if (element != target && !parents.contains(element))
            item.fn.call(element, e);
        });
      };
      Element.Events.outerClick = {
        onAdd: function(fn){
          if(!events) {
            document.addEvent('click', check);
            events = [];
          }
          events.push({element: this, fn: fn});
        },
        onRemove: function(fn){
          events = events.filter(function(item){
            return item.element != this || item.fn != fn;
          }, this);
          if (!events.length) {
            document.removeEvent('click', check);
            events = null;
          }
        }
      };
    })(document.id,$$);


    en4.core.runonce.add(function() {
        $$('.btn-toggle-more-category').addEvent('click', function(){
            var category_item = this.getParent(),
                layout_middle = $$('.layout_main .layout_middle')[0];

            category_item.toggleClass('ynlisting-category-expand');

            if ( category_item.hasClass('ynlisting-category-expand') ) {
                var category_expand = category_item.getElement('.category-more-data');
                layout_middle.setStyle(
                    'min-height', category_expand.getSize().y + category_expand.getPosition().y - layout_middle.getPosition().y
                );
            } else {
                layout_middle.erase('style'); 
            }

        }); 
    });

     $$('.btn-toggle-more-category').addEvent('outerClick', function(){
        var category_item = this.getParent();

        if(category_item.hasClass('ynlisting-category-expand')){
            category_item.removeClass('ynlisting-category-expand');
        }
     })



</script>
