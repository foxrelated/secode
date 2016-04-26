<?php

class Ynidea_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function getSettingsValues() {
    $values = $this->getValues();
    $ideaValues = array();
    $trophyValues = array();
    foreach($values as $key => $val) {
      $data = explode('_', $key, 2);
      if ($data[0] == 'idea') {
        $ideaValues[$data[1]] = $val;
      } else if ($data[0] == 'trophy') {
        $trophyValues[$data[1]] = $val;
      }
    }
    return array('idea' => $ideaValues, 'trophy' => $trophyValues);
  }

  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    // Element: view
    $this->addElement('Radio', 'idea_view', array(
      'label' => 'Allow Viewing Of Idea?',
      'description' => 'Do you want to let members view idea? If set to no, some other settings on this idea may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all ideas, even private ones.',
        1 => 'Yes, allow viewing of idea.',
        0 => 'No, do not allow idea to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->idea_view->options[2]);
    }

    if( !$this->isPublic() ) {

    // Element: votes
      $this->addElement('Radio', 'idea_vote', array(
        'label' => 'Allow Vote Of Idea?',
        'description' => 'Do you want to let members vote idea? If set to no, some other settings on this idea may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow vote of idea.',
          0 => 'No, do not allow idea to be created.'
        ),
        'value' => 1,
      ));
           
        // Element: create
      $this->addElement('Radio', 'idea_create', array(
        'label' => 'Allow Creation Of Idea ?',
        'description' => 'Do you want to let members create idea? If set to no, some other settings on this idea may not apply. This is useful if you want members to be able to view idea, but only want certain levels to be able to create idea.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of idea.',
          0 => 'No, do not allow idea to be created.'
        ),
        'value' => 1,
      ));
      
      
      // Element: edit
      $this->addElement('Radio', 'idea_edit', array(
        'label' => 'Allow Editing Of Idea?',
        'description' => 'Do you want to let members edit idea? If set to no, some other settings on this idea may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all ideas, even private ones',
          1 => 'Yes, allow members to edit their own ideas.',
          0 => 'No, do not allow members to edit their ideas.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->idea_edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'idea_delete', array(
        'label' => 'Allow Deletion Of Idea?',
        'description' => 'Do you want to let members delete idea? If set to no, some other settings on this idea may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all ideas, even private ones',
          1 => 'Yes, allow members to delete their own ideas.',
          0 => 'No, do not allow members to delete their ideas.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->idea_delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'idea_comment', array(
        'label' => 'Allow Commenting On Idea?',
        'description' => 'Do you want to let members of this level comment on idea?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all ideas, including private ones.',
          1 => 'Yes, allow members to comment on idea.',
          0 => 'No, do not allow members to comment on idea.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->idea_comment->options[2]);
      }

      // Element: auth_html
      $this->addElement('Text', 'idea_auth_html', array(
        'label' => 'HTML In Idea?',
        'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
        'value' => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));
    }
    //Member level settings for Trophy
    // Element: view
    $this->addElement('Radio', 'trophy_view', array(
        'label' => 'Allow Viewing Of Trophy?',
        'description' => 'Do you want to let members view trophy? If set to no, some other settings on this trophy may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all trophy, even private ones.',
            1 => 'Yes, allow viewing of trophy.',
            0 => 'No, do not allow trophy to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->trophy_view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: votes
      $this->addElement('Radio', 'trophy_vote', array(
          'label' => 'Allow Vote Of Trophy?',
          'description' => 'Do you want to let members vote trophy? If set to no, some other settings on this trophy may not apply.',
          'multiOptions' => array(
              1 => 'Yes, allow vote of trophy.',
              0 => 'No, do not allow trophy to be created.'
          ),
          'value' => 1,
      ));

      // Element: create
      $this->addElement('Radio', 'trophy_create', array(
          'label' => 'Allow Creation Of Trophy ?',
          'description' => 'Do you want to let members create trophy? If set to no, some other settings on this trophy may not apply. This is useful if you want members to be able to view trophy, but only want certain levels to be able to create trophy.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of trophy.',
              0 => 'No, do not allow trophy to be created.'
          ),
          'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'trophy_edit', array(
          'label' => 'Allow Editing Of Trophy?',
          'description' => 'Do you want to let members edit trophy? If set to no, some other settings on this trophy may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all trophy.',
              1 => 'Yes, allow members to edit their own trophy.',
              0 => 'No, do not allow members to edit their trophy.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->trophy_edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'trophy_delete', array(
          'label' => 'Allow Deletion Of Trophy?',
          'description' => 'Do you want to let members delete trophy? If set to no, some other settings on this trophy may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all trophy.',
              1 => 'Yes, allow members to delete their own trophy.',
              0 => 'No, do not allow members to delete their trophy.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->trophy_delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'trophy_comment', array(
          'label' => 'Allow Commenting On Trophy?',
          'description' => 'Do you want to let members of this level comment on trophy?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all trophy, including private ones.',
              1 => 'Yes, allow members to comment on trophy.',
              0 => 'No, do not allow members to comment on trophy.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->trophy_comment->options[2]);
      }

      // Element: auth_html
      $this->addElement('Text', 'trophy_auth_html', array(
          'label' => 'HTML In Trophy?',
          'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
          'value' => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));
    }
  }
}