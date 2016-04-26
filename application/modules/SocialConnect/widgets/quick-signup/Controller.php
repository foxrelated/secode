<?php
class SocialConnect_Widget_QuickSignupController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$rs = $this -> view -> rs = Engine_Api::_() -> getDbTable('Services', 'SocialConnect') -> getServices(100, 1);

		if ($rs -> count() == 0)
		{
			$this -> setNoRender(TRUE);
		}
	}

}
