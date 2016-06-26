<div>
<?php
if($this->isPostTrue){
 echo "Event name ".$this->name.'<br> Total price '.$this->price*$this->count.'<br>';

}else{
    echo "Error";
}
?>
    </div>