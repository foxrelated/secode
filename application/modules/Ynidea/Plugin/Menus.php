<?php

class Ynidea_Plugin_Menus
{

    public function canMyIdeas($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('ynidea_idea', $viewer, 'create'))
        {
            return false;
        }
        return true;
    }
	public function canMyTrophies($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('ynidea_trophy', $viewer, 'create'))
        {
            return false;
        }

        return true;
    }
    public function canCreateIdea($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        if (!Engine_Api::_() -> authorization() -> isAllowed('ynidea_idea', $viewer, 'create'))
        {
            return false;
        }

        return true;
    }

    public function canCreateTrophy($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        if (!Engine_Api::_() -> authorization() -> isAllowed('ynidea_trophy', $viewer, 'create'))
        {
            return false;
        }

        return true;
    }

}
