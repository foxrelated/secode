<?php

class Ynidea_Api_Core extends Core_Api_Abstract
{
    protected $_moduleName = 'Ynidea';
    
    /**
     *
     * Return a string after substring
     * @param string $string
     * @param int $length
     * @return string
     */
    public function subPhrase($string,$length = 0){
      if(strlen($string)<=$length) return $string;
      $pos = $length;
      for($i=$length-1;$i>=0;$i--){
        if($string[$i]==" "){
          $pos = $i+1;
          break;
        }
      }
      return substr($string,0,$pos)."...";
    }
    
    /**
     * Check existed title
     * return Bool
     */
    public function checkTitle($title)
    {
        $model = new Ynidea_Model_DbTable_Ideas;
        $select = $model -> select()->where('title = ?',$title);        
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
        
    /**
     * Check existed trophy title
     * return Bool
     */
    public function checkTrophyTitle($title)
    {
        $model = new Ynidea_Model_DbTable_Trophies;
        $select = $model -> select()->where('title = ?',$title);        
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
    
    /**
     * Check trophy vote enable
     * return Bool
     */
    public function checkTrophyVote($trophy_id)
    {
        $model = new Ynidea_Model_DbTable_Trophies;
        $select = $model -> select()->where('trophy_id = ?',$trophy_id);        
        $row = $model->fetchRow($select); 
        if($row->status == 'voting')
            return true;
        else
            return false; 
    }
    
    /**
     * Check existed trophy vote 
     * return Bool
     */
    public function checkTrophyExistedVote($trophy_id,$idea_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Trophyvotes;
        $select = $model -> select()->where('trophy_id = ?',$trophy_id)
                                    ->where('idea_id=?',$idea_id)
                                    ->where('user_id=?',$user_id);        
        $row = $model->fetchRow($select); 
        return $row;
    }
    
    /**
     * Check trophy vote enable
     * return Bool
     */
    public function checkIsJudge($trophy_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Judges;
        $select = $model -> select()->where('trophy_id = ?',$trophy_id)->where('user_id=?',$user_id);        
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
    
    /**
     * Number of Potential plus
     * return c
     */    
    public function getPotentialPlus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(potential_plus) AS potential_plus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);     
        return $row->potential_plus;                   
    }
    
    /**
     * Number of Potential plus
     * return Potential
     */    
    public function getPotentialMinus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(potential_minus) AS potential_minus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->potential_minus;                   
    }
    
    
    /**
     * Number of feasibility plus
     * return int
     */    
    public function getFeasibilityPlus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(feasibility_plus) AS feasibility_plus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->feasibility_plus;                   
    }
    
    /**
     * Number of feasibility minus
     * return int
     */    
    public function getFeasibilityMinus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(feasibility_minus) AS feasibility_minus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->feasibility_minus;                   
    }
    
    /**
     * Number of inovation plus
     * return int
     */    
    public function getInovationPlus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(inovation_plus) AS inovation_plus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->inovation_plus;                   
    }
    
    /**
     * Number of inovation minus
     * return int
     */    
    public function getInovationMinus($idea_id,$version = 0){
        
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('SUM(inovation_minus) AS inovation_minus'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->inovation_minus;                   
    }
    
    /**
     * Get vote idea
     * return Row
     */ 
    public function getVote($idea_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Ideavotes;
        $select = $model -> select()->where('user_id = ?',$user_id)->where('idea_id=?',$idea_id);             
        $row = $model->fetchRow($select); 
        
        return $row;
    }
    
    /**
     * Check user vote idea
     * return Bool
     */ 
    public function checkVote($idea_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Ideavotes;
        $select = $model -> select()->where('user_id = ?',$user_id)->where('idea_id=?',$idea_id);             
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
    
    /**
     * Check user vote idea
     * return Bool
     */ 
    public function checkCoauthor($idea_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Coauthors;
        $select = $model -> select()->where('user_id = ?',$user_id)->where('idea_id=?',$idea_id);        
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
    
    /**
     * Check existed idea
     * return Bool
     */ 
    public function checkPublishIdea($idea_id,$verion_id)
    {
        $model = new Ynidea_Model_DbTable_Ideas;
        $select = $model -> select()
                        ->where('idea_id=?',$idea_id)
                        ->where('version_id=?',$verion_id)
                        ->where('publish_status=?','publish');        
        $row = $model->fetchRow($select); 
       
        if($row)
            return true;
        else
            return false; 
    }
    
    /**
     * Check existed idea
     * return Bool
     */ 
    public function getVersionIdea($idea_id,$verion_id)
    {
        $model = new Ynidea_Model_DbTable_Versions;
        $select = $model -> select()
                        ->where('idea_id=?',$idea_id)
                        ->where('version_id=?',$verion_id);        
        $row = $model->fetchRow($select); 
       
        if($row)
            return $row;
        else
            return false; 
    }
    
    /**
     * Check existed idea
     * return Bool
     */ 
    public function checkExistedIdea($idea_id)
    {
        $model = new Ynidea_Model_DbTable_Ideas;
        $select = $model -> select()->where('idea_id=?',$idea_id);        
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
         
    /**
     * Get total vote idea
     * return int
     */ 
    public function getTotalVote($idea_id,$version = 0)
    {
        $model = new Ynidea_Model_DbTable_Ideavotes;        
        $select = $model->select()
        ->from('engine4_ynidea_ideavotes',array('count(*) AS total_vote'))
        ->where('idea_id = ?',$idea_id)->where('version_id=?',$version);        
        $row = $model->fetchRow($select);
        return $row->total_vote ; 
    }
    
    
    /**
     * Get trophyvote
     * return int
     */ 
    public function getTrophyVote($trophy_id,$idea_id,$user_id)
    {
        $model = new Ynidea_Model_DbTable_Trophyvotes;        
        $select = $model->select()        
        ->where('trophy_id = ?',$trophy_id)->where('idea_id = ?',$idea_id)->where('user_id=?',$user_id);   
                 
        $row = $model->fetchRow($select);
		if($row)
        	return $row->value;
		return 0; 
    }
        
    /**
     * Count Judge member
     * return int
     */ 
    public function getCountJudge($trophy_id)
    {
        $model = new Ynidea_Model_DbTable_Judges;        
        $select = $model->select()
        ->from('engine4_ynidea_judges',array('count(user_id) AS total_judge'))
        ->where('trophy_id = ?',$trophy_id);        
        $row = $model->fetchRow($select);
        return $row->total_judge; 
    }
    
    /**
     * Count Judge vote
     * return int
     */ 
    public function getCountJudgeVote($trophy_id,$idea_id)
    {
        $model = new Ynidea_Model_DbTable_Trophyvotes;        
        $select = $model->select()   
        ->from('engine4_ynidea_trophyvotes',array('count(user_id) AS total_judge'))     
        ->where('trophy_id = ?',$trophy_id)->where('idea_id=?',$idea_id);        
        $row = $model->fetchRow($select);
        return $row->total_judge; 
    }
    
    /**
     * Count Score of Judge
     * return float
     */ 
    public function getScoreJudge($trophy_id,$idea_id)
    {
        $model = new Ynidea_Model_DbTable_Trophyvotes;        
        $select = $model->select()   
        ->from('engine4_ynidea_trophyvotes',array('sum(value) AS score_judge'))     
        ->where('trophy_id = ?',$trophy_id)->where('idea_id=?',$idea_id);        
        $row = $model->fetchRow($select);
        return $row->score_judge; 
    }
    
    
    /**
     *
     * Get idea Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
    public function getIdeaPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getIdeas($params));
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     /**
     *
     * Get getIdeas 
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getIdeas($param = null)
     {
        $table = Engine_Api::_()->getDbtable('ideas', 'Ynidea');
        $Name = $table->info('name');
		
		 // Get Tagmaps table
        $tags_table = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tags_name = $tags_table->info('name');
        $trophy_id = 0;
		if(isset($param['trophy_id']) && $param['trophy_id'] != "")
        {
         	$trophy_id = $param['trophy_id'];
		} 
		               
        $select = $table->select()->from($Name,new Zend_Db_Expr("$Name.*, (SELECT SUM(`value`)/COUNT(`user_id`) FROM `engine4_ynidea_trophyvotes` WHERE `engine4_ynidea_trophyvotes`.`idea_id` = $Name.`idea_id` AND `trophy_id` = $trophy_id Group by `trophy_id`) as score"))
        		->setIntegrityCheck(false);
	    
		if (isset($param['category_id']) && $param['category_id'] != 'all') {
            $category = Engine_Api::_()->getItem('ynidea_category', $param['category_id']);
            if ($category) {
                $tree = array();
				$categoryTbl = Engine_Api::_()->getItemTable('ynidea_category');
                $node = $categoryTbl -> getNode($category->getIdentity());
                $categoryTbl -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where("$Name.category_id IN (?)", $categories);
            }
			else {
				if ($param['category_id'] == '0') {
					$select->where("$Name.category_id = ?", '0');
				}
			}
        }
	    
	    if(isset($param['user_id']) && $param['user_id'] != "")
        {
         	$user_id = $param['user_id'];
			$select->where("$Name.user_id = ?",$user_id);
		}  
		if(!isset($param['manage']) && !isset($param['admin']))
			$select->where("$Name.publish_status = 'publish'");
        if(isset($param['name']) && $param['name'] != "")
         {
             $name = $param['name'];
             $select->where("title Like ?","%$name%");
         }
         if(isset($param['owner']) && $param['owner'] != "")
         {
             $name = $param['owner'];
             $select->join("engine4_users","engine4_users.user_id = $Name.user_id","");
             $select->where("engine4_users.displayname Like ?","%$name%");
         }
         if(!isset($param['direction'])) 
            $param['direction'] = "DESC";
		 //Search
		 if(isset($param['search']) && $param['search'] != "")
         {
         	 $search = $param['search'];
             $select->where("(title LIKE '%$search%' OR description LIKE '%$search%')");
         }
		 /*
		 if(isset($param['tags']) && $param['tags'] != "")
         {
         	$tags = $param['tags'];	
         	$select->where("tags LIKE ?","%$tags%");
		 }*/
		 if(isset($param['award']) && $param['award'] != "")
         {
         	$select->where("award = ?",$param['award']);
		 }
		 if(isset($param['trophy_id']) && $param['trophy_id'] != "")
         {
            $trophy_id = $param['trophy_id'];
			$select->join("engine4_ynidea_nominees","engine4_ynidea_nominees.idea_id = $Name.idea_id","");
            $select->where("engine4_ynidea_nominees.trophy_id = ?",$trophy_id);
            if(isset($param['trophy_award']) && $param['trophy_award'] != "")
            {
            	$select->join("engine4_ynidea_awards","engine4_ynidea_awards.idea_id = $Name.idea_id","");
				$select->where("engine4_ynidea_awards.trophy_id = ?",$trophy_id);
            }
         }
		 //end search
		 //Search with tag cloud
	     if( !empty($param['tag']) )
	     {
	          $select
	            ->joinLeft($tags_name, "$tags_name.resource_id = $Name.idea_id","")
	            ->where($tags_name.'.resource_type = ?', 'ynidea_idea')
	            ->where($tags_name.'.tag_id = ?', $param['tag']);
	     }
	     //end tag cloud
	        
		 if(isset($param['decision']) && $param['decision'] != "")
         {
             $select->where("(decision = 'realized' OR decision = 'selected')");
         }

        //Order by filter
        if(isset($param['orderby']) && $param['orderby'] == 'displayname'){
           $select -> join('engine4_users as u',"u.user_id = $Name.user_id",'')
                      -> order("u.displayname ".$param['direction']);
        }
        else{
            if(isset($param['filter']) && $param['filter'] != '')
            {
               $select->order($Name.".".$param['filter'].' '.$param['direction']); 
            }
            else
            {
                $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'creation_date '.$param['direction']);
            }
            
        }
        return $select;
        
        
     }
     
     /**
     *
     * Get trophy Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getTrophyPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getTrophies($params));
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     
     public function getTrophies($param = null)
     {
        $table = Engine_Api::_()->getDbtable('trophies', 'Ynidea');
        $Name = $table->info('name');
                        
        $select = $table->select()->from($Name)->setIntegrityCheck(false);
        if(isset($param['user_id']) && $param['user_id'] != "")
         {
             $user_id = $param['user_id'];
             $select->where("user_id = ?",$user_id);
         }
        if(isset($param['name']) && $param['name'] != "")
         {
             $name = $param['name'];
             $select->where("title Like ?","%$name%");
         }
          if(isset($param['owner']) && $param['owner'] != "")
         {
             $name = $param['owner'];
             $select->join("engine4_users","engine4_users.user_id = $Name.user_id","");
             $select->where("engine4_users.displayname Like ?","%$name%");
         }
         if(!isset($param['direction'])) 
            $param['direction'] = "DESC";

        //Order by filter
        if(isset($param['orderby']) && $param['orderby'] == 'displayname'){
           $select -> join('engine4_users as u',"u.user_id = $Name.user_id",'')
                      -> order("u.displayname ".$param['direction']);
        }
        else
        {            
            $select ->order(!empty($param['orderby'])?"$Name.".$param['orderby']." ".$param['direction'] :"$Name.creation_date");
        }   
		
        return $select;
     }
     
     /**
     *
     * Delete idea
     * @param int $idea    
     * @return Item
     */
     public function deleteIdea($idea){
        //  1. Delete Voting
        //  2. Delete Nominess
        //  3. Delete Adward
        //  4. Delete Activity Feed , Comment, Like
        //  5. Delete thumnail    
        
        $item = Engine_Api::_()->getItem('ynidea_idea', $idea->idea_id);
        if ($item) {
            $item->delete();
        }
     }
      /**
     *
     * Get Judges Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getJudgesPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getJudges($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
             $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     /**
     *
     * Get Judges Paginator
     * @param array $params    
     * @return select
     */
     public function getJudges($param = null)
     {
        $nomineesTable = Engine_Api::_()->getDbtable('judges', 'ynidea');
    	$select = $nomineesTable->select();
		if(!empty($param['trophy_id']))
    		$select->where('trophy_id = ?', $param['trophy_id']);
        return $select;        
     }
	 /**
     *
     * Get Co-author Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getCoauthorsPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getCoauthors($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
             $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     /**
     *
     * Get Co Authors Paginator
     * @param array $params    
     * @return select
     */
     public function getCoauthors($param = null)
     {
        $coauthorsTable = Engine_Api::_()->getDbtable('coauthors', 'ynidea');
    	$select = $coauthorsTable->select();
		if(!empty($param['idea_id']))
    		$select->where('idea_id = ?', $param['idea_id']);
        return $select;        
     }
     /**
     *
     * Get Nominess Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getNomineesPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getNominees($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
             $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     
     public function getNominees($param = null)
     {
        $table = Engine_Api::_()->getDbtable('nominees', 'Ynidea');            
        $Name = $table->info('name');
           
        $select = $table->select()->setIntegrityCheck(false)->from($Name,array(
        "$Name.*","engine4_ynidea_ideas.title as idea_title","engine4_ynidea_trophies.title AS trophy_title"
        ));
        if(isset($param['tropy_id']) && $param['tropy_id'] != "")
        {
            $trophy_id = $param['tropy_id'];
            $select->where("trophy_id = ?",$trophy_id);
        }
        
        if(isset($param['idea_id']) && $param['idea_id'] != "")
        {
            $idea_id = $param['idea_id'];
            $select->where("idea_id = ?",idea_id);
        }
              
        $select->join("engine4_ynidea_ideas","engine4_ynidea_ideas.idea_id = $Name.idea_id","");
        $select->join("engine4_ynidea_trophies","engine4_ynidea_trophies.trophy_id = $Name.trophy_id","");
           
        return $select;        
        
     }
	 public static function partialViewFullPath($partialTemplateFile) 
	 {
		$ds = DIRECTORY_SEPARATOR;
		return "application{$ds}modules{$ds}Ynidea{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
  	 }
	 public function getAllAdmins()
     {
         $table = Engine_Api::_()->getItemtable('user');
         $Name = $table->info('name');
         $select = $table->select();
         $select->where("level_id = 1")
         ->OrWhere("level_id = 2");
         $users = $table->fetchAll($select);
         return $users;
     }
     
     public function getReportsPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getReportsSelect($params));
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     public function getReportsSelect($param = null)
     {
         $table = Engine_Api::_()->getDbtable('reports', 'ynidea');
         $Name = $table->info('name');
         $select = $table->select()->from($Name);
         
         if(isset($param['type']) && $param['type'] != "")
         {
             $select->where("type = ?",$param['type']);
         }
         $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'creation_date '.$param['direction']);
        
        return $select;
     }
	 public function getCountTrophy()
	 {
	 	$table = Engine_Api::_()->getDbtable('trophies', 'ynidea');
		$select = $table->select();
		$rows = $table->fetchAll($select);
	 	return count($rows);
	 }
	 public function getCountAward()
	 {
	 	$table = Engine_Api::_()->getDbtable('awards', 'ynidea');
		$select = $table->select();
		$rows = $table->fetchAll($select);
	 	return count($rows);
	 }
     public function getAwards($idea_id)
	{
	    $awardTable = Engine_Api::_()->getDbtable('awards', 'ynidea');
	    $select = $awardTable->select()
	    ->where('idea_id = ?', $idea_id);	    
	    $rows = $awardTable->fetchAll($select);
		return $rows->toArray();
	}
	 public function getCountIdea()
	 {
	 	$table = Engine_Api::_()->getDbtable('ideas', 'ynidea');
		$select = $table->select()->where("publish_status = 'publish'");
		$rows = $table->fetchAll($select);
	 	return count($rows);
	 }
     
     public function getEditNominee($trophy_id){       
        $model = new Ynidea_Model_DbTable_Nominees;
        $select = $model -> select()->where('trophy_id = ?',$trophy_id);   
        $nominees = $model->fetchAll($select);    
        return $nominees->toArray();
     }
     
     public function checkExistedNominee($idea_id,$trophy_id){       
        $model = new Ynidea_Model_DbTable_Nominees;
        $select = $model -> select()->where('idea_id=?',$idea_id)->where('trophy_id = ?',$trophy_id);   
        $nominees = $model->fetchRow($select);           
        if($nominees)
            return true;
        else
            return false;
     }
     public function getEditJudge($trophy_id){        
        $model = new Ynidea_Model_DbTable_Judges;
        $select = $model -> select()->where('trophy_id = ?',$trophy_id);
        $judges = $model->fetchAll($select);     
        return $judges;
     }
     
     /**
     *
     * Get getIdeas 
     * @param array $params    
     * @return Zend_Paginator
     */
     public function getProfileIdeas($param = null)
     {        
        $table = Engine_Api::_()->getDbtable('coauthors', 'ynidea');
        $Name = $table->info('name');
        
        $select = $table->select()
                ->from($Name,array("$Name.*","engine4_ynidea_ideas.title","engine4_ynidea_ideas.ideal_score","engine4_ynidea_ideas.version_date"))
        		->setIntegrityCheck(false);
	    if(isset($param['user_id']) && $param['user_id'] != "")
        {
         	$user_id = $param['user_id'];
			$select->where("$Name.user_id = ?",$user_id);
		}  
		$select->join("engine4_ynidea_ideas","engine4_ynidea_ideas.idea_id = $Name.idea_id","");        
         
        return $select;
	}
    
    /**
     *
     * Get idea Paginator
     * @param array $params    
     * @return Zend_Paginator
     */
    public function getProfileIdeaPaginator($params = array())
     {
        
        $paginator = Zend_Paginator::factory($this->getProfileIdeas($params));
        
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     
     /**
     *
     * Get all voter for idea
     * @param array $params    
     * @return row
     */
     public function getAllVoters($idea_id){
        $table = Engine_Api::_()->getDbtable('ideavotes', 'ynidea');
		$select = $table->select()->where("idea_id = ?",$idea_id);
		$rows = $table->fetchAll($select);
	 	return $rows;
     }
     
     /**
	  * 
	  * Functions for fundrasing campaign
	  * 
	  */
	  public function checkFundraisingPlugin() {
		$module = 'ynfundraising';
		$modulesTable = Engine_Api::_ ()->getDbtable ( 'modules', 'core' );
		$mselect = $modulesTable->select ()->where ( 'enabled = ?', 1 )->where ( 'name  = ?', $module );
		$module_result = $modulesTable->fetchRow ( $mselect );
		if (count ( $module_result ) > 0) {
			return true;
		}
		return false;
	}
}