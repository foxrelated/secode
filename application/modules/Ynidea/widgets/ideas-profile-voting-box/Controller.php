<?php

class Ynidea_Widget_IdeasProfileVotingBoxController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance()->getRequest();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
        $subject = Engine_Api::_()->core()->getSubject('ynidea_idea');                  
        $permitvote = true;
        //check login       
        if(!$viewer->getIdentity() || $subject->isOwner($viewer) || !Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($subject, $viewer, 'vote')->checkRequire())
        {
            $permitvote = false;
        }            
           
		$message = '';  
		if(!$viewer->getIdentity())
		{
			$message = 'Please login to can vote on this idea!';
		} 
		if($subject->isOwner($viewer))
		{
			$message = 'You can not vote on your own ideas.';
		}   
		if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($subject, $viewer, 'vote')->checkRequire())
		{
			$message = 'You have not permission to vote on this idea.';
		}          
		$this->view-> message = $message;       
        //Check vote idea
		$permitvote = true;
		
        //check login       
        if(!$viewer->getIdentity() || $subject->isOwner($viewer) || !Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($subject, $viewer, 'vote')->checkRequire())
        {
            $permitvote = false;
        }            
                              
       // check co-author
        if(Engine_Api::_()->ynidea()->checkCoauthor($subject->idea_id,$viewer->getIdentity()) == true){
            $permitvote = false;
        }        
		if(isset($_POST) && isset($_POST['potential_plus']))		
        {
             if($permitvote)
             {                                          
        	    // Get subject and check auth       
                //$totalvoter =  Engine_Api::_()->ynidea()->getTotalVote($subject->idea_id,$subject->version_id);
                
                // Add him self BA Hoang say that
                //$totalvoter = $totalvoter + 1;
                //$totalvoter = ($totalvoter == 0) ? 0.5 : $totalvoter; 
                
                
                                         
                
                // Process
                $table = Engine_Api::_()->getItemTable('ynidea_ideavote');
                $db = $table->getAdapter();
                $db->beginTransaction();
        
                try
                {
                    $values['idea_id'] = $subject->idea_id;
                    $values['user_id'] = $viewer->getIdentity();
                    $values['version_id'] = $subject->version_id;
                    
                    $values['potential_plus'] = intval($_POST['potential_plus']); 
                    $values['potential_minus'] = intval($_POST['potential_minus']);
                    $values['feasibility_plus'] = intval($_POST['feasibility_plus']);
                    $values['feasibility_minus'] = intval($_POST['feasibility_minus']); 
                    $values['inovation_plus'] = intval($_POST['innovation_plus']);
                    $values['inovation_minus'] = intval($_POST['innovation_minus']);
                    
                    
                     
                    $vote = Engine_Api::_()->ynidea()->getVote($subject->idea_id,$viewer->getIdentity());   
                    $countvote = false;                    
                    if(!$vote)
					{   
                        $countvote = true;       
                        $vote = $table->createRow();
                        $vote->setFromArray($values);  
                    }  
					else {               
                        $vote->potential_plus =   $values['potential_plus'];
                        $vote->potential_minus =  $values['potential_minus'];
                        $vote->feasibility_plus =   $values['feasibility_plus'];
                        $vote->feasibility_minus = $values['feasibility_minus'];
                        $vote->inovation_plus = $values['inovation_plus'];
                        $vote->inovation_minus = $values['inovation_minus'];                             
                    }
                                     
                    $vote->save();
                    
                    $idea_table = Engine_Api::_()->getItemTable('ynidea_idea');
                    $idea = $idea_table->fetchRow($idea_table->select()->where('idea_id=?',$subject->idea_id));
                      
                      
                    $nbpotentialplus = Engine_Api::_()->ynidea()->getPotentialPlus($subject->idea_id,$subject->version_id);
                    $nbpotentialminus = Engine_Api::_()->ynidea()->getPotentialMinus($subject->idea_id,$subject->version_id);
                                       
                
                    $bninnovationplus = Engine_Api::_()->ynidea()->getInovationPlus($subject->idea_id,$subject->version_id);
                    $bninnovationminus = Engine_Api::_()->ynidea()->getInovationMinus($subject->idea_id,$subject->version_id);
               
                                                
                    $bnfeasibilityplus = Engine_Api::_()->ynidea()->getFeasibilityPlus($subject->idea_id,$subject->version_id);
                    $bnfeasibilityminus = Engine_Api::_()->ynidea()->getFeasibilityMinus($subject->idea_id,$subject->version_id);
                    
                    //$nbpotentialplus  = $nbpotentialplus + $values['potential_plus'];                           
                    //$nbpotentialminus = $nbpotentialminus + $values['potential_minus'];
                    //$bninnovationplus = $bninnovationplus + $values['inovation_plus'];
                    //$bninnovationminus = $bninnovationminus + $values['inovation_minus'];
                    //$bnfeasibilityplus = $bnfeasibilityplus + $values['feasibility_plus'];
                    //$bnfeasibilityminus  = $bnfeasibilityminus + $values['feasibility_minus']; 
                     if($countvote)
                        $idea->vote_count = $idea->vote_count + 1;
                        
                    $totalvoter = $idea->vote_count;
                    $totalvoter = ($totalvoter == 0) ? 0.5 : $totalvoter;
                                 
                    $potential_ave = 0.5 + ($nbpotentialplus - $nbpotentialminus)/(2*$totalvoter) ;
                    $innovation_ave = 0.5 + ($bninnovationplus - $bninnovationminus)/(2*$totalvoter) ;     
                    $feasibility_ave = 0.5 + ($bnfeasibilityplus - $bnfeasibilityminus)/(2*$totalvoter) ;
                               
                    $idea->vote_ave = (2*$potential_ave + 2*$feasibility_ave + $innovation_ave)/5;
                    $idea->ideal_score  = 2*($idea->vote_ave - 0.5)*$totalvoter;
                   
                
                    $idea->potential_ave  =  $potential_ave;
                    $idea->innovation_ave = $innovation_ave;
                    $idea->feasibility_ave = $feasibility_ave;
                    $idea->save();
                    
                    if($countvote)
					{
                    
	                    $action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $idea, 'ynidea_idea_vote');
	                    
	                    if( $action != null )
			            {
			                  Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $idea);
			            }        
						// send notification to all follow users
						$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			            $userFollows = $idea->getFollows();
			             foreach($userFollows as $follow)
			             {   if($follow->user_id != $viewer->getIdentity())
			                 {
			                     $userFollow = Engine_Api::_()->getItem('user', $follow->user_id);            
			                     $notifyApi->addNotification($userFollow, $viewer, $idea, 'ynidea_idea_vote', array(
			                      'label' => $idea->title
			                    ));
			                 }
			             }   
	                    
                    }   
                    $db->commit();
                    
                    $subject = $idea;
                }
                catch( Exception $e )
                {
                    $db->rollBack();
                    throw $e;
                }
            }
        
        }
                 
        $this->view->permitvote = $permitvote;
        $this->view->idea = $subject;                       
    }

}
