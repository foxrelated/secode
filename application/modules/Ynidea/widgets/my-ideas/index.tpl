<?php if( count($this->paginator) > 0 ): ?>
<ul class='ideas_frame ideas_browse'>
    <?php foreach( $this->paginator as $idea ): ?>
    <li>
        <div class="ideas_photo">
            <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.normal')) ?>
        </div>

        <div class="ideas_options">
            <?php if( $idea->isOwner($this->viewer()) ): ?>
            <?php if($idea->publish_status == 'draft'):?>
            <div>
                <?php echo $this->htmlLink(array('route' => 'ynidea_specific', 'action' => 'edit', 'id' =>
                $idea->getIdentity()), $this->translate('Edit Idea'), array(
                'class' => 'buttonlink icon_idea_edit'
                )) ?>
            </div>
            <?php endif;?>
            <div>
                <?php echo $this->htmlLink(array('route' => 'ynidea_specific', 'action' => 'delete', 'id' =>
                $idea->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete idea'), array(
                'class' => 'buttonlink smoothbox icon_idea_delete'
                ));
                ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="ideas_info">
            <div class="ideas_title">
                <h3><?php $idea_name = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),50);
                    echo $this->htmlLink($idea->getHref(), $idea->getTitle());
                    ?></h3>
            </div>
            <div class="ideas_cate">
                <span><?php echo $this->translate('Category: ')?></span>
                <?php $i = 0;
                $category = Engine_Api::_()->getItem('ynidea_category', $idea->category_id) ?>
                <?php if($category) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != 1) :?>
                            <?php if($i != 0) :?>
                                &raquo;
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if($category -> parent_id != 0 && $category -> parent_id != 1) :?>
                                &raquo;
                    <?php endif;?>
                    <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                    <?php else:?>
                    <?php echo $this->translate('None')?>
                <?php endif;?>
            </div>
            <div class="ideas_desc">
                <?php echo wordwrap(Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),250), 55, "\n",
                true); ?>
            </div>
        </div>

    </li>
    <?php endforeach; ?>
</ul>
<?php if( count($this->paginator) > 1 ): ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues,
)); ?>
<?php endif; ?>

<?php else: ?>
<div class="tip">
        <span>
        <?php echo $this->translate('You have not ideas yet.') ?>
            <?php if( $this->canCreate): ?>
            <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'ynidea_general').'">', '</a>') ?>
            <?php endif; ?>
        </span>
</div>
<?php endif; ?>




