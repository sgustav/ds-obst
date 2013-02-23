<?php
    /**
    * OBST, a tool for the multilingual browsergame TribalWars.
    * Copyright (C) 2006-2007 Robert Nitsch
    *
    * This program is free software; you can redistribute it and/or
    * modify it under the terms of the GNU General Public License
    * as published by the Free Software Foundation; either version 2
    * of the License, or (at your option) any later version.
    *
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU General Public License for more details.
    *
    * You should have received a copy of the GNU General Public License
    * along with this program; if not, write to the Free Software
    * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
    **/
    class PageReports extends Page {
    
        public $title = 'Berichte';
        public $template = 'reports';
        
        
        function init()
        {
            // description of what is done here:
            // if the user is admin, he must have access to the groups on each site of "PageReports", because
            // on the bottom of each reportlisting there are mass_edit - options, including a list of groups
            
            // if the user is not an admin, he must have access to the groups on the "view" site only
            // (as implemented in action_view())
            $model_group = new ReportGroupsModel($this->mysql);
            if($this->user->isAdmin() or $this->user->can('reports_mass_edit'))
            {
                $this->smarty->assign('report_groups', $model_group->get());
            }
            
            // how many reports do we have...
            $model_reports = new ReportsModel($this->mysql);
            $reports_count = $model_reports->count();
            if($reports_count > 0)
                $pages = range(1,ceil($reports_count / 20));
            else
                $pages = array(0 => 1);
            $this->smarty->assign('pages', $pages);
        }
        
        /**
          * List the last 10 reports.
          */
        function defaultAction()
        {
            $this->smarty->assign('no_page_select', true);
            $this->smarty->assign('reports', $this->getAllReports(10));
            $this->content = "index";
        }
        
        /**
          * List all reports.
          */
        function action_all()
        {
            $this->title = "Alle Berichte";
            
            $offset = 0;
            $page = 1;
            if(!empty($_GET['p']) and is_numeric($_GET['p']))
            {
                $page = $_GET['p'];
                $offset  = ($page-1) * 20;
            }
            
            // get the reports
            $reports = $this->getAllReports(20,$offset);
            
            // display
            $this->smarty->assign('reports', $reports);
            $this->smarty->assign('page', $page);
            $this->content = "list";
        }
	
	/**
	  * Mass editing...
	  */
	function action_mass_edit()
	{
	   if(!$this->user->isAdmin() and !$this->user->can('reports_mass_edit'))
	       $this->accessDenied('Zugriff auf die Massenbearbeitung nicht möglich! Du hast keine Administratorrechte!');
	       
	   $action = !empty($_POST['mass_edit_action']) ? $_POST['mass_edit_action'] : '';
	   
	   // check the action
	   if(empty($action))
	   {
	       $this->errors[] = 'Keine Massenbearbeitung ausgewählt!';
	   }
	   elseif( $action != 'regroup' and 
	           $action != 'setworld' and
	           $action != 'delete')
	   {
	       $this->errors[] = 'Ungültige Massenbearbeitung!';
	   }
	   
	   // check the required privileges and the specific parameters for each action
	   if($action == 'regroup')
	   {
	       if(!$this->user->isAdmin())
	           $this->checkAccess('reports_mass_edit_regroup');
	       
	       if(empty($_POST['group']) or !is_numeric($_POST['group']))
	           $this->errors[] = 'Keine gültige Gruppe zum Einordnen angegeben!';
	   }
	   elseif($action == 'setworld')
	   {
	       if(!$this->user->isAdmin())
	           $this->checkAccess('reports_mass_edit_setworld');
	       
	       if(empty($_POST['world']) or !is_numeric($_POST['world']))
	           $this->errors[] = 'Keine gültige Welt zum Zuordnen angegeben!';
	   }
	   elseif($action == 'delete')
	   {
	       if(!$this->user->isAdmin())
	           $this->checkAccess('reports_mass_edit_delete');
	       
	       if(empty($_POST['delete_sure']) or $_POST['delete_sure'] != 'yes')
	           $this->errors[] = 'Zum Löschen mehrerer Berichte muss die "sicher"-Checkbox angekreuzt sein!';
	   }
	   
	   // if there already occured an error, exit the function
	   if(count($this->errors) > 0)
	       return;
	       
	   // get all selected reports
	   $selected = array();
	   $matches = array();
	   foreach($_POST as $key => $value)
	   {
	       if(preg_match('/^select_([0-9]+)$/', $key, $matches))
	       {
	           $selected[] = $matches[1];
	       }
	   }
	   
	   // if no report is selected => error
	   if(count($selected) == 0)
	   {
	       $this->errors[] = 'Keine Berichte zur Massenbearbeitung ausgewählt!';
	   }
	   
	   if(count($this->errors) > 0)
	       return;
	   
	   switch($action)
	   {
	       case 'regroup':
	           $group = addslashes($_POST['group']);
	           $sql_cmd = "UPDATE xdb_reports SET group_id = '$group' WHERE id IN (".implode(',', $selected).")";
	           break;
	       case 'setworld':
	           $world = addslashes($_POST['world']);
	           $sql_cmd = "UPDATE xdb_reports SET world = '$world' WHERE id IN (".implode(',', $selected).")";
	           break;
	       case 'delete':
	           $world = addslashes($_POST['world']);
	           $sql_cmd = "DELETE FROM xdb_reports WHERE id IN (".implode(',', $selected).")";
	           break;
	   }
	   
	   $success = $this->mysql->sql_query($sql_cmd);
	   if(!$success)
	       $this->sqlError();
	   else
	       $this->redirect('index.php?page=reports');
	}
	
	/**
	  * Search for reports.
	  */
	function action_search()
	{
	    $this->title = 'Berichte durchsuchen';
	    $this->content = 'search';
            
            $this->smarty->assign('no_page_select', true);
            
            $general = array();
            $where_general = '';
            $search = array('general',
                            'attacker_nick',
                            'defender_nick',
                            'attacker_coords',
                            'defender_coords'
                            );
            
            if(isset($_POST['filled']))
            {
                $search['general'] = (isset($_POST['search_general']) and $_POST['search_general'] == 'yes') ? 'yes' : 'no';
                $search['attacker_nick'] = isset($_POST['attacker_nick']) ? $_POST['attacker_nick'] : '';
                $search['defender_nick'] = isset($_POST['defender_nick']) ? $_POST['defender_nick'] : '';
                $search['attacker_coords'] = isset($_POST['attacker_coords']) ? $_POST['attacker_coords'] : '';
                $search['defender_coords'] = isset($_POST['defender_coords']) ? $_POST['defender_coords'] : '';
                $search['group_id'] = (isset($_POST['group_id']) and is_numeric($_POST['group_id'])) ? $_POST['group_id'] : '';
                
                if($search['general'] == 'yes')
                {
                    if(!empty($search['attacker_nick']))
                        $general[] = 'attacker_nick LIKE "%'.addslashes($search['attacker_nick']).'%"';
                    
                    if(!empty($search['defender_nick']))
                        $general[] = 'defender_nick LIKE "%'.addslashes($search['defender_nick']).'%"';
                        
                    if(!empty($search['attacker_coords']))
                        $general[] = 'attacker_coords = "'.addslashes($search['attacker_coords']).'"';
                        
                    if(!empty($search['defender_coords']))
                        $general[] = 'defender_coords = "'.addslashes($search['defender_coords']).'"';
                    
                    if(!empty($search['group_id']) and $search['group_id'] != '-1')
                        $general[] = 'group_id = "'.addslashes($search['group_id']).'"';
                        
                    $where_general = implode(' AND ', $general);
                }
                
                $where_all = implode(' AND ', array($where_general));
                
                if(count($this->user->worlds) > 0 and !$this->user->isAdmin())
                {
                    if(strlen($where_all) > 0)
                        $where_all .= ' AND ';
                    $where_all .= 'world IN (0,'.$this->user->getVal('worlds').')';
                }
                
                $model = new ReportsModel($this->mysql);
                $reports = $model->get('id, group_id, time, realtime, world, attacker_nick, defender_nick, defender_village', 'realtime DESC', $where_all);
                

                
                if($reports === FALSE)
                    $this->sqlError();
                
                // get group names
                $model_report_groups = new ReportGroupsModel($this->mysql);
                foreach($reports as $key => $value)
                {
                    $reports[$key] = array_merge($reports[$key], array('group' => $model_report_groups->getGroupName($reports[$key]['group_id'])));
                }
                
                $this->smarty->assign('reports', $reports);
                $this->smarty->assign('search', $search);
            }
            
            // provide the template the possible usernames and report groups
            $usermodel = new UserModel($this->mysql);
            $usernames = $usermodel->get('name', 'name', 'activated = 1');
            $this->smarty->assign('usernames', $usernames);
            
            $model_report_groups = new ReportGroupsModel($this->mysql);
            $this->smarty->assign('report_groups', $model_report_groups->get());
        }
        
        /**
          * This action is for displaying the parse form.
          * Moreover it handles the parsing requests.
          */
        function action_parse()
        {
            $this->title = "Bericht einlesen";
            
            $this->checkAccess('reports_parse');
            
            $model_group = new ReportGroupsModel($this->mysql);
            
            if(!isset($_POST['filled']))
            {
                $this->content = "parse";
                if(!$this->user->isAdmin())
                    $this->smarty->assign('report_groups', $model_group->get());
            }
            else
            {
                if(empty($_POST['report']))
                {
                    $this->errors[] = 'Du hast keinen Bericht in das Textfeld eingefügt!';
                }
                if(empty($_POST['group']) or !is_numeric($_POST['group']))
                {
                    $this->errors[] = 'Du hast keine Gruppe angegeben!';
                }
                if(empty($_POST['world']) or !is_numeric($_POST['world']))
                {
                    $this->errors[] = 'Du hast keine gültige Welt angegeben.';
                }
                
                $report = $_POST['report'];
                $group = addslashes($_POST['group']);
                $world = addslashes($_POST['world']);
                
                // check if the group exists
                if(!$model_group->exists($group))
                {
                    $this->errors[] = 'Die angegebene Gruppe existiert nicht!';
                }
                
                // check if user has access to the world
                $this->require_access_to_world($world);
                
                if(count($this->errors) > 0)
                    return;
                    
                // save the report
                global $obst_units;
                $parser = new dsBericht($obst_units[intval($world)]);
                if(!$parser->parse($_POST['report']))
                {
                    $this->errors[] = 'Der Bericht konnte nicht eingelesen werden! Möglicherweise musst du eine neue Version von OBST installieren!';
                    return;
                }
                
                $max_id = $this->mysql->sql_result($this->mysql->sql_query("SELECT MAX(id) AS max_id FROM xdb_reports"), 0, 'max_id');
                if(empty($max_id))
                    $max_id = 0;
                
                $data = serialize($parser->getReport());
                $data_hash = md5($data);
                
                $extra_columns = array(
                                    'id' => ($max_id+1),
                                    'user_id' => $this->user->getVal('id'),
                                    'group_id' => $group,
                                    'ip' => $_SERVER['REMOTE_ADDR'],
                                    'realtime' => time(),
                                    'lastcomment' => 0,
                                    'hash' => $data_hash,
                                    'world' => $world
                                    );
                $stmt = $parser->buildSQL('xdb_reports', $extra_columns);

                
                //$sql = "INSERT INTO xdb_reports VALUES (".($max_id+1).", ".$this->user->getVal('id').", '".$group."', '".$_SERVER['REMOTE_ADDR']."', ".time().", 0, '".$data_hash."', '".$world."', ".$stmt.")";
                $sql = $stmt;
                
                if($this->mysql->sql_query($sql))
                {
                    $this->flash("index.php?page=reports&action=view&id=".($max_id+1), "Der Bericht wurde erfolgreich eingelesen.");
                }
                else
                {
                    $this->errors[] = "Der Bericht konnte aufgrund eines SQL-Fehlers nicht eingelesen werden.";
                    if(OBST_DEBUG)
                    {
                        $this->debuginfo[] = "SQL-Fehlermeldung: ".$this->mysql->lasterror;
                        $this->debuginfo[] = "SQL-Abfrage:<br /><pre>".$this->mysql->lastquery."</pre>";
                    }
                }
            }
        }
        
        /**
          * This action is for viewing a specific report.
          */
        function action_view()
        {
            $this->title = "Bericht anzeigen";
            
            $this->checkAccess('reports_view');
            
            // validate data
            if(empty($_GET['id']) or !is_numeric($_GET['id']))
            {
                $this->errors[] = "Keine Bericht-ID angegeben.";
                return;
            }
            
            $id = addslashes($_GET['id']);
            $this->content = "view";
            
            $this->require_access_to_report($id);
            
            // get datamodel instances
            $model = new ReportsModel($this->mysql);
            $model_users = new UserModel($this->mysql);
            $model_report_groups = new ReportGroupsModel($this->mysql);
            
            $this->smarty->assign('report_groups', $model_report_groups->get());
            
            // get the report
            $report = $model->getById($id);
            if($report)
            {
                // for mathematical purposes (see dsbericht.tpl - template file)
                $report['luck_i'] = intval($report['luck']);
                
                // group name
                $group_name = $model_report_groups->getGroupName($report['group_id']);
                
                // units
                global $obst_units;
                $this->smarty->assign('units', $obst_units[intval($report['world'])]);
                
                // get the units out of the mysql result
                $units_att = array();
                $units_attl = array();
                $units_deff = array();
                $units_deffl = array();
                $units_spied = array();
                $units_out = array();
                foreach($obst_units[intval($report['world'])] as $unit)
                {
                    $units_att[] = $report['troops_att_'.$unit->iname];
                    $units_attl[] = $report['troops_attl_'.$unit->iname];
                    $units_deff[] = $report['troops_def_'.$unit->iname];
                    $units_deffl[] = $report['troops_defl_'.$unit->iname];
                    $units_spied[] = $report['spied_troops_out_'.$unit->iname];
                    $units_out[] = $report['troops_out_'.$unit->iname];
                }
                
                // stick all data to one array ;-)
                $report = array_merge($report,
                                          array('units_att' => $units_att,
                                                'units_attl' => $units_attl,
                                                'units_deff' => $units_deff,
                                                'units_deffl' => $units_deffl,
                                                'units_spied' => $units_spied,
                                                'units_out' => $units_out,
                                                'poster' => $model_users->getUserName($report['user_id']),
                                                'group' => $group_name));
                $this->smarty->assign('report', $report);
                
                
                // page <-> offset
                $page = 1;
                $offset = 0;
                if(!empty($_GET['comments_page']) && is_numeric($_GET['comments_page']))
                {
                    echo $_GET['comments_page'];
                    $page = $_GET['comments_page'];
                    $offset = ($page-1) * 10;
                }
                
                // get the comments of this report
                $model_comments = new ReportCommentsModel($this->mysql);
                $comments = $model_comments->get('*', 'time DESC', "report_id = '$id'", 10, $offset);
                
                $comments_count = $model_comments->count("report_id = '$id'");
                if($comments_count > 0)
                    $comments_pages = range(1,ceil($comments_count / 10));
                else
                    $comments_pages = Array(0 => '1');
                    
                $this->smarty->assign('comments_pages', $comments_pages);
                $this->smarty->assign('comments_page', $page);
                
                $i = 0;
                for($i; $i<count($comments); $i++)
                {
                    $user_id = $comments[$i]['user_id'];
                    $user_name = $model_users->getUserName($user_id);
                    $comments[$i]['user_name'] = $user_name;
                    
                    // and strip slashes / encode html
                    $comments[$i]['text'] = nl2br(htmlspecialchars(stripslashes($comments[$i]['text'])));
                }
                $this->smarty->assign('comments', $comments);
            }
            else
            {
                $this->errors[] = "Diesen Bericht gibt es nicht.";
            }
        }
        
        /**
          * This action is for deleting reports.
          */
        function action_delete()
        {
            $this->title = "Bericht löschen";
            
            $this->checkAccess('reports_delete');
            
            if(empty($_GET['delete']) or !is_numeric($_GET['delete']))
            {
                $this->errors[] = "Keine Bericht-ID angegeben.";
                return;
            }
            
            $model = new ReportsModel($this->mysql);
            $model_comments = new ReportCommentsModel($this->mysql);
            
            $id = addslashes($_GET['delete']);
            
            $this->require_access_to_report($id);
            
            $success = $model->delete("id = '$id'", 1) and $model_comments->delete("report_id = '$id'", 99999);
            if($success)
                $this->flash("index.php?page=reports","Der Bericht wurde erfolgreich gelöscht.");
            else
            {
                $this->errors[] = "Der Bericht mit der ID $id konnte nicht gelöscht werden.";
                $this->debuginfo[] = "SQL-Fehlermeldung: ".$this->mysql->lasterror;
            }
        }
        
        /**
          * This action is for commenting reports...
          */
        function action_comment()
        {
            $this->title="Kommentar hinzufügen";
            
            $this->checkAccess('reports_comment');
            
            if(empty($_GET['report_id']) or !is_numeric($_GET['report_id']))
            {
                $this->errors[] = 'Keine gültige BerichteID übertragen.';
            }
            if(empty($_POST['comment']))
            {
                $this->errors[] = 'Kein Kommentar eingegeben!';
            }
            else
            {
                $_POST['comment'] = trim($_POST['comment']);
                if(strlen($_POST['comment']) < 3 or strlen($_POST['comment']) > 5000)
                {
                    $this->errors[] = 'Kommentare müssen mindestens 3 und dürfen maximal 5000 Zeichen lang sein!';
                }
            }
            
            if(count($this->errors) > 0)
                return;
            
            $id = addslashes($_GET['report_id']);
            $user_id = $this->user->getVal('id');
            $text = addslashes(trim($_POST['comment']));
            
            $this->require_access_to_report($id);
            
            $model = new ReportCommentsModel($this->mysql);
            $time = time();
            $success = $model->insert('report_id, user_id, time, text', "$id, $user_id, ".$time.", '$text'");
            $success = $success and $this->mysql->sql_query("UPDATE xdb_reports SET lastcomment = '$time' WHERE id = '$id' LIMIT 1");
            
            if($success)
            {
                $this->redirect('index.php?page=reports&action=view&id='.$id.'#comments');
            }
            else
            {
                $this->sqlError();
            }
        }
        
        /**
          * This action enables users to regroup a report.
          */
        function action_regroup()
        {
            $model_reports = new ReportsModel($this->mysql);
            $model_report_groups = new ReportGroupsModel($this->mysql);
            
            if(empty($_GET['regroup']) or !is_numeric($_GET['regroup']))
            {
                $this->errors[] = 'Keine BerichteID angegeben!';
            }
            if(empty($_POST['group']) or !is_numeric($_POST['group']))
            {
                $this->errors[] = 'Keine GruppenID angegeben!';
            }
            
            $regroup = addslashes($_GET['regroup']); // the id of the report which ought to be regrouped
            $group = addslashes($_POST['group']); // the id of the new group
            
            // check if the report exists
            if(!$model_reports->exists($regroup))
            {
                $this->errors[] = 'Dieser Bericht existiert nicht!';
            }
            
            // check the report's ownership
            $this->require_ownership($regroup);
            
            // check if the group exists
            if(!$model_report_groups->exists($group))
            {
                $this->errors[] = 'Diese Gruppe existiert nicht!';
            }
            
            if(count($this->errors) > 0)
                return;
            
            $this->require_access_to_report($regroup);
            
            $success = $model_reports->update("group_id = '$group'","id = '$regroup'");
            if(!$success)
            {
                $this->sqlError();
            }
            else
            {
                $this->redirect("index.php?page=reports&action=view&id={$regroup}");
            }
        }
        
        /**
          * This action makes it possible to change the world to which a report is related.
          */
        function action_setworld()
        {
            global $obst;
            
            $model_reports = new ReportsModel($this->mysql);
            
            if(empty($_GET['id']) or !is_numeric($_GET['id']))
            {
                $this->errors[] = 'Keine BerichteID angegeben!';
            }
            if(empty($_POST['world']) or !is_numeric($_POST['world']))
            {
                $this->errors[] = 'Keine Welt angegeben!';
            }
            
            $id = addslashes($_GET['id']); // the id of the report
            $world = addslashes($_POST['world']); // the new world
            
            // check if the report exists
            if(!$model_reports->exists($id))
            {
                $this->errors[] = 'Dieser Bericht existiert nicht!';
            }
            
            // check the report's ownership
            $this->require_ownership($id);

            // check if the world is activated in the config
            if(array_search(intval($world), $obst['worlds']) === false)
            {
                $this->errors[] = 'Diese Welt ist in dieser OBST-Installation nicht verfügbar!';
            }
            
            if(count($this->errors) > 0)
                return;
            
            $this->require_access_to_world($world);
            
            $success = $model_reports->update("world = '$world'","id = '$id'");
            if(!$success)
            {
                $this->sqlError();
            }
            else
            {
                $this->redirect("index.php?page=reports&action=view&id={$id}");
            }
        }
        
        /**
         * This action deletes a comment
         */
        function action_delete_comment()
        {
            $this->title="Kommentar löschen";
            
            $this->checkAccess('reports_comments_delete');
            
            if(empty($_GET['delete']) or !is_numeric($_GET['delete']))
            {
                $this->errors[] = 'Keine gültige KommentarID übertragen.';
            }
            if(empty($_GET['report_id']) or !is_numeric($_GET['report_id']))
            {
                $this->errors[] = 'Keine gültige BerichteID übertragen.';
            }
            
            if(count($this->errors) > 0)
                return;
            

            $id = addslashes($_GET['delete']);
            $report_id = addslashes($_GET['report_id']);
            
            $this->require_access_to_report($report_id);
            
            $model = new ReportCommentsModel($this->mysql);
            $success = $model->delete("id = '$id' AND report_id = '$report_id'", 1);
            
            if($success)
            {
                $this->redirect('index.php?page=reports&action=view&id='.$report_id.'#comments');
            }
            else
            {
                $this->sqlError();
            }
        }
        
        /**
          * This method uses has_access_to_report($id) to examine if the user has access to the report specified by $id.
          * If not, it executes accessDenied().
          * If the user has access, nothing happens.
          */
        function require_access_to_report($id)
        {
            if(!$this->has_access_to_report($id))
            {
                $this->accessDenied('Sorry, aber dieser Bericht gehört zu einer Welt, auf die du keinen Zugriff hast. Du kannst diesen Bericht weder ansehen, löschen noch kommentieren oder gruppieren.<br />Es ist auch nicht möglich, einen Bericht zu einer Welt zuzuordnen, auf die du keinen Zugriff hast.', 'Zugriff auf diesen Bericht wegen Weltenbeschränkung verweigert.');
            }
        }
        
        function require_access_to_world($world)
        {
            if(!$this->user->has_access_to_world($world))
                $this->accessDenied('Es ist nicht möglich, einen Bericht zu einer Welt zuzuordnen, auf die du keinen Zugriff hast.', 'Zuordnung zu einer Welt des Berichts wegen Weltenbeschränkung verweigert.');
        }
        
        function require_ownership($reportid)
        {
            if($this->user->isAdmin())
                return;
                
            $model = new ReportsModel($this->mysql);
            if(!$model->checkOwnership($this->user->getId(), $reportid))
                $this->accessDenied('Sorry, aber diese Aktion ist nur möglich, wenn du der Besitzer des Berichts bist. (Es sei denn du hast Administratorrechte.)');
        }
        
        /**
         * This function checks whether the current user has the right to access the report specified by $id in any way.
         */
        function has_access_to_report($id)
        {
            // first check if the user has a world access restriction at all
            if(count($this->user->worlds) == 0)
                return true; // no restriction
            
            
            $model_reports = new ReportsModel($this->mysql);
            $report = $model_reports->getById($id);
            
            if($report === false)
                throw new Exception("there is no report with the id $id");
            
            // the report is not (yet) related to a specific world
            if($report['world'] == 0)
                return true;
                
            // check if the user has access
            if($this->user->has_access_to_world($report['world']))
                return true;
                
            // no access
            return false;
        }
        
        /**
          * This function gets a bunch of reports limited by $limit
          * Mostly the returned array is used in the reports_list.tpl template, because it does only contain
          * some standard data of the reports --> Its purpose is to be used in different report listings.
          */
        function getAllReports($limit=10, $offset=0)
        {
            global $obst;
            
            $model = new ReportsModel($this->mysql);
            $model_report_groups = new ReportGroupsModel($this->mysql);
            
            $result = $model->get(  'id, group_id, time, realtime, lastcomment, world, attacker_nick, defender_nick, defender_village',
                                    'realtime DESC',
                                    ((count($this->user->worlds) > 0 and ($this->user->worlds != $obst['worlds']) and !$this->user->isAdmin()) ? ('world IN (0,'.$this->user->getVal('worlds').')') : ''),
                                    $limit,
                                    $offset);
            
            if($result === false)
            {
                $this->sqlError();
                return false;
            }
            
            foreach($result as $key => $value)
            {
                $result[$key] = array_merge($result[$key], array('group' => $model_report_groups->getGroupName($result[$key]['group_id'])));
            }
            
            return $result;
        }
        
  
    };

?>