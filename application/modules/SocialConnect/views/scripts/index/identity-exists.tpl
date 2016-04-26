<?php 
$description = $this->translate("Email %1s already exists, please enter your details below to login or click %2s to continue use social connect.",$this->email, "<a href = '".$this->url(array(),"connect_map_user")."'>".$this->translate("here")."</a>");
$this->form->setDescription($description);
$this->form->setAction($this->url(array(),"user_login"));
echo $this->form->render($this); ?>