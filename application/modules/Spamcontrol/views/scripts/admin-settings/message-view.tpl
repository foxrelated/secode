<?php
/**
 * Social Engine
 *
 * @category   Application_Extensions
 * @package    
 * @author     Yoyo
 */

?>

<h2>Spam Control</h2>
<div class="tabs">
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>	
</div>
<h3>
    <?php if ('' != ($title = trim($this->conversation->getTitle()))): ?>
        <?php echo $title ?>
    <?php else: ?>
        <em>
            <?php echo $this->translate('(No Subject)') ?>
        </em>
    <?php endif; ?>
</h3>

<div class="message_view_header">
    <div class="message_view_between">
        <?php
        // Resource
        if ($this->resource) {
            echo $this->translate('To members of %1$s', $this->resource->toString());
        }
        // Recipients
        else {
            $you = array_shift($this->recipients);
            $you = $this->htmlLink($you->getHref(), ($this->viewer()->isSelf($you) ? $you->getTitle() : $you->getTitle()));
            $them = array();
            foreach ($this->recipients as $r) {
                if ($r != $this->viewer()) {
                    $them[] = ($r == $this->blocker ? "<s>" : "") . $this->htmlLink($r->getHref(), $r->getTitle()) . ($r == $this->blocker ? "</s>" : "");
                } else {
                    $them[] = $this->htmlLink($r->getHref(), $r->getTitle());
                }
            }

            if (count($them))
                echo $this->translate('Between %1$s and %2$s', $you, $this->fluentList($them));
            else
                echo 'Conversation with a deleted member.';
        }
        ?>
    </div>
   
</div>

<div style="clear: both;"></div>
<table class="admin_table">
    <thead>
        <tr>
            <th><?php echo $this->translate('User') ?></th>
            <th><?php echo $this->translate('Date') ?></th>
            <th><?php echo $this->translate('Body') ?></th>
            <th><?php //echo $this->translate('Option') ?></th>
        </tr>    
    </thead> 

<?php foreach ($this->messages as $message):
    $user = $this->user($message->user_id);
    ?>
       <tr> 
           <td><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></td>
           <td><?php echo $this->timestamp($message->date) ?></td>
           <td> <?php echo nl2br(html_entity_decode($message->body)) ?></td>
           <td> <?php //echo $this->htmlLink(array(), $this->translate('delete'))?></td>
        </tr>
<?php endforeach; ?>
</table>


