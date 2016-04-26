<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spam control
 * @author     Yoyo
 */

?>
<table >
    <tr>
        <td  style="padding-right: 3px"><?php echo $this->translate('Warnings:');?>  </td>
        <td><?php 
       
            foreach ($this->warn as $warn){
                echo $this->htmlImage($this->baseUrl().'/application/modules/Spamcontrol/externals/images/spam.png', 'title', array('title'=> $warn->body, 'style' => 'margin: 2px;'));
            }
        
?></td>
        
    </tr>    
</table>
    
