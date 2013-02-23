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
    class PageAdmin extends Page {
    
        public $title = 'Administration';
        public $template = 'admin';
        
        
        function init() {}
        
        function defaultAction()
        {
            $this->content='start';
        }
        
        function action_users_list()
        {
            $this->checkAccess('users');
            
            $this->smarty->assign('users', $this->getAllUsers());
            $this->content='user_list';
        }
        
        function action_user_edit()
        {
            $this->checkAccess('users');
            $this->checkAccess('users_edit');
            
            if(empty($_GET['userid']) or !is_numeric($_GET['userid']))
            {
                $this->errors[] = "Keine BenutzerID angegeben.";
                return;
            }
            
            $id = addslashes($_GET['userid']);

            $user_to_edit = $this->getUser($id);
            if(!$user_to_edit)
                return;
            
            // is the user allowed to edit this user?
            $this->checkLevel($user_to_edit['user_level']-1,
                            'Du kannst nur Benutzer bearbeiten, die ein geringeres Userlevel haben, als du selbst! Du kannst dich auch nicht selbst bearbeiten (benutze dazu "Profil" im Mitgliederbereich).',
                            '(Abgeschickte!!!) Bearbeitung von Benutzer '.$user_to_edit['name'].' verweigert');
            
            global $obst;
            if($user_to_edit['id'] == $obst['sysadmin'] and $this->user->getVal('id') != $obst['sysadmin'])
                $this->accessDenied('Der Systemadministrator kann nicht von dir bearbeitet werden.');
            
            if(count($this->errors) > 0)
                return;
                
            if(isset($_POST['filled']))
            {
                // check the data
                $new['name'] =          isset($_POST['name']) ? $_POST['name'] : '';
                $new['email'] =         isset($_POST['email']) ? $_POST['email'] : '';
                $new['pass'] =          isset($_POST['pass_new']) ? $_POST['pass_new'] : '';
                
                $new['activated'] =     (isset($_POST['activated']) and $_POST['activated']=='1') ? 1 : 0;
                
                $new['worlds'] =        (isset($_POST['worlds']) and preg_match('/^([0-9]{1,2},?)*$/', $_POST['worlds'])) ? trim($_POST['worlds'], ",") : '';
                
                $new['can_reports_view'] =      (isset($_POST['can_reports_view']) and $_POST['can_reports_view']=='1') ? 1 : 0;
                $new['can_reports_parse'] =     (isset($_POST['can_reports_parse']) and $_POST['can_reports_parse']=='1') ? 1 : 0;
                $new['can_reports_delete'] =    (isset($_POST['can_reports_delete']) and $_POST['can_reports_delete']=='1') ? 1 : 0;
                $new['can_reports_comment'] =   (isset($_POST['can_reports_comment']) and $_POST['can_reports_comment']=='1') ? 1 : 0;
                $new['can_reports_comments_delete'] =   (isset($_POST['can_reports_comments_delete']) and $_POST['can_reports_comments_delete']=='1') ? 1 : 0;
                
                if(!Validator::nick($new['name']))
                    $this->errors[] = 'Der Name ist ungültig. Er darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen!';
                if(!Validator::email($new['email']))
                    $this->errors[] = 'Die EMail-Addresse ist ungültig.';
                    
                if(count($this->errors) > 0)
                    return;
                    
                // save the changes
                $new_pass = '';
                if(!empty($new['pass']))
                    $new_pass = "pass = '".md5($new['pass'])."',";
                
                $query = "UPDATE xdb_users SET
                                                name='".$new['name']."',
                                                email='".$new['email']."',
                                                {$new_pass}
                                                activated=".$new['activated'].",
                                                worlds='".$new['worlds']."',
                                                `can_reports_view` = ".$new['can_reports_view'].",
                                                `can_reports_parse` = ".$new['can_reports_parse'].",
                                                `can_reports_delete` = ".$new['can_reports_delete'].",
                                                `can_reports_comment` = ".$new['can_reports_comment'].",
                                                `can_reports_comments_delete` = ".$new['can_reports_comments_delete']."
                                                WHERE id = '$id'
                                                LIMIT 1";
                
                if(!$this->mysql->sql_query($query))
                {
                    $this->sqlError();
                    return;
                }
                else // everything is ok
                {
                    // check if the activation status of this user has been changed
                    // if yes, send an email
                    $old_status = $user_to_edit['activated'];
                    $new_status = $new['activated']=='1' ? true : false;
                    
                    if($old_status != $new_status)
                    {
                        $message = "Hallo {$user_to_edit['name']},\n\n";
                        switch($new_status)
                        {
                            case false:
                                $message .= "dein Account bei OBST [{$obst['name']}] ({$obst['url']}) wurde deaktiviert.";
                                break;
                            case true:
                                $message .= "dein Account bei OBST [{$obst['name']}] wurde aktiviert.";
                                break;
                        }
                        
                        if(!mail($user_to_edit['email'], 'OBST ['.htmlentities($obst['name']).']', $message, 'From:'.$obst['email']))
                            $this->errors[] = 'Die Änderungen wurden durchgeführt, jedoch konnte der User nicht per EMail über die De-/Aktivierung seines Accounts benachrichtigt werden.';
                    }
                    
                    // display the edit page again, with the new data
                    $user_to_edit = $this->getUser($id);
                    $this->smarty->assign('user', $user_to_edit);
                    $this->smarty->assign('saved', true);
                }
            }
            else
            {
                $this->smarty->assign('user', $user_to_edit);
            }
            
            $this->content='user_edit';
        }
        
        function action_user_rights()
        {
            $this->checkAccess('users');
            $this->checkAccess('users_rights');
            
            if(empty($_GET['userid']) or !is_numeric($_GET['userid']))
            {
                $this->errors[] = "Keine BenutzerID angegeben.";
                return;
            }
            
            $id = addslashes($_GET['userid']);
            
            $user_to_edit = $this->getUser($id);
            
            $this->checkLevel($user_to_edit['user_level'],
                            'Du kannst nur Benutzer bearbeiten, die ein geringeres oder gleichhohes Userlevel haben, als du selbst!',
                            'Bearbeitung von Benutzer '.$user_to_edit['name'].' verweigert');
            
            global $obst;
            // only the system administrator can edit himself
            if($user_to_edit['id'] == $obst['sysadmin'] and $this->user->getVal('id') != $obst['sysadmin'])
                $this->accessDenied('Der Systemadministrator kann nicht von dir bearbeitet werden.');
            
            // only the system administrator can edit himself
            if($user_to_edit['id'] == $this->user->getVal('id') and $this->user->getVal('id') != $obst['sysadmin'])
                $this->accessDenied('Du kannst deine eigenen Rechte nicht bearbeiten!', 'Bearbeitung der eigenen Rechte verweigert');
                
            if(count($this->errors) > 0)
                return;
                    
            if(isset($_POST['filled']))
            {
                // check the data
                $new['user_level']          = isset($_POST['user_level']) ? $_POST['user_level'] : '';
                $new['admin']               = (isset($_POST['admin']) and $_POST['admin']=='1') ? 1 : 0;
                $new['can_users']           = (isset($_POST['can_users']) and $_POST['can_users']=='1') ? 1 : 0;
                $new['can_users_edit']      = (isset($_POST['can_users_edit']) and $_POST['can_users_edit']=='1') ? 1 : 0;
                $new['can_users_rights']    = (isset($_POST['can_users_rights']) and $_POST['can_users_rights']=='1') ? 1 : 0;
                $new['can_reports_mass_edit'] = (isset($_POST['can_reports_mass_edit']) and $_POST['can_reports_mass_edit']=='1') ? 1 : 0;
                $new['can_reports_mass_edit_regroup']   =     (isset($_POST['can_reports_mass_edit_regroup']) and $_POST['can_reports_mass_edit_regroup']=='1') ? 1 : 0;
                $new['can_reports_mass_edit_setworld']  =    (isset($_POST['can_reports_mass_edit_setworld']) and $_POST['can_reports_mass_edit_setworld']=='1') ? 1 : 0;
                $new['can_reports_mass_edit_delete']    =      (isset($_POST['can_reports_mass_edit_delete']) and $_POST['can_reports_mass_edit_delete']=='1') ? 1 : 0;
                
                if(!is_numeric($new['user_level']) or $new['user_level'] < 0 or $new['user_level'] > 10000)
                    $this->errors[] = 'Das Userlevel muss numerisch sein und muss zwischen 0 und 10000 liegen!';
                    
                if(count($this->errors) > 0)
                    return;
                    
                // save the changes
                $query = "UPDATE xdb_users SET
                                                admin = ".$new['admin'].",
                                                user_level=".$new['user_level'].",
                                                `can_users` = ".$new['can_users'].",
                                                `can_users_edit` = ".$new['can_users_edit'].",
                                                `can_users_rights` = ".$new['can_users_rights'].",
                                                `can_reports_mass_edit` = ".$new['can_reports_mass_edit'].",
                                                `can_reports_mass_edit_regroup` = ".$new['can_reports_mass_edit_regroup'].",
                                                `can_reports_mass_edit_setworld` = ".$new['can_reports_mass_edit_setworld'].",
                                                `can_reports_mass_edit_delete` = ".$new['can_reports_mass_edit_delete']."
                                                WHERE id = '$id'
                                                LIMIT 1";
                
                if(!$this->mysql->sql_query($query))
                {
                    $this->sqlError();
                    return;
                }
                else
                {
                    $user_to_edit = $this->getUser($id);
                    $this->smarty->assign('user', $user_to_edit);
                    $this->smarty->assign('saved', true);
                }
            }
            else
            {
                $this->smarty->assign('user', $user_to_edit);
            }
            
            $this->content='user_rights';
        }
        
        function action_user_delete()
        {
            $this->title="Benutzer löschen";
            
            global $obst;
            if($this->user->getId() == $obst['sysadmin'])
            {
                if(empty($_GET['userid']) or !is_numeric($_GET['userid']))
                {
                    $this->errors[] = 'Ungültige BenutzerID!';
                    return;
                }
                
                $id = addslashes($_GET['userid']);
                $model_user = new UserModel($this->mysql);
                $user = $model_user->getById($id);
                
                if($id == $obst['sysadmin'])
                {
                    $this->errors[] = 'Der Systemadministrator kann nicht gelöscht werden!';
                }
                if($user == false)
                {
                    $this->errors[] = 'Diesen Benutzer gibt es nicht.';
                }
                
                if(count($this->errors) > 0)
                    return;
                
                $model_user->delete("id='$id'", 1);
                $this->redirect('index.php?page=admin&action=users_list');
            }
            else
            {
                $this->errors[] = 'Nur der Systemadministrator kann andere Benutzer löschen!';
                return;
            }
        }
        
        function action_report_groups()
        {
            $this->title = "Berichtegruppen verwalten";
            
            $model_group = new ReportGroupsModel($this->mysql);
            
            $this->smarty->assign('report_groups', $model_group->get());
            $this->content = 'report_groups';
        }
        
        function action_report_groups_add()
        {
            $this->title = "Berichtegruppen verwalten";
            
            $model_group = new ReportGroupsModel($this->mysql);
            
            $group_name = isset($_POST['group_name']) ? trim($_POST['group_name']) : '';
            
            if(empty($group_name) or strlen($group_name > 50))
            {
                $this->errors[] = 'Keinen, oder einen zu langen Gruppennamen angegeben! (max. 50 Zeichen)';
            }
            
            if(count($this->errors) > 0)
                return;
            
            $success = $model_group->insert('name',array(addslashes($group_name)));
            if(!$success)
            {
                $this->sqlError();
            }
            else
            {
                $this->redirect('index.php?page=admin&action=report_groups');
            }
        }
        
        function action_report_groups_delete()
        {
            $this->title = "Berichtegruppen verwalten";
            
            $model_reports = new ReportsModel($this->mysql);
            $model_group = new ReportGroupsModel($this->mysql);
            
            if(empty($_GET['delete']) or !is_numeric($_GET['delete']))
            {
                $this->errors[] = 'Keine GruppenID angegeben!';
            }
            
            if(count($this->errors) > 0)
                return;
            
            $group_id = addslashes($_GET['delete']);
            $success = $model_group->deleteById($group_id) and $model_reports->update("group_id='-1'", "group_id='$group_id'");
            if(!$success)
            {
                $this->sqlError();
            }
            else
            {
                $this->redirect('index.php?page=admin&action=report_groups');
            }
        }
        
        function action_report_groups_rename()
        {
            $this->title="Bericht umbenennen";
            
            $model_group = new ReportGroupsModel($this->mysql);
            
            $new_name = isset($_POST['new_name']) ? trim($_POST['new_name']) : '';
            
            if(empty($_POST['group']) or !is_numeric($_POST['group']))
            {
                $this->errors[] = 'Keine gültige GruppenID angegeben!';
            }
            if(empty($new_name))
                $this->errors[] = 'Der neue Name ist ungültig!';
            
            $group = addslashes($_POST['group']);
            
            // überprüfen ob die Gruppe existiert
            if(!$model_group->exists($group))
                $this->errors[] = 'Diese Gruppe existiert nicht!';
                
            if(count($this->errors) > 0)
                return;
                
            $success = $model_group->update("name='$new_name'", "id='$group'");
            if($success)
                $this->redirect('index.php?page=admin&action=report_groups');
            else
                $this->sqlError();
        }
        
        
        function getAllUsers()
        {
            $model = new UserModel($this->mysql);
            $result = $model->get('id,name,email,activated,user_level', 'name');
            if(!$result)
            {
                $this->sqlError();
                return false;
            }
            
            return $result;
        }
        
        function getUser($id)
        {
            $model = new UserModel($this->mysql);
            $result = $model->getById($id);
            if(!$result)
            {
                $this->sqlError();
                return false;
            }
            
            return $result;
        }
    
    };

?>