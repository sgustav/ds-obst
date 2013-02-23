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
    class PageUser extends Page {
    
        public $title = 'Userpage';
        public $template = 'user';
        
        
        function init() {}
        
        
        function defaultAction()
        {
            $this->smarty->assign('nonavi',true);
            $this->smarty->assign('nosubnavi',true);
            
            if(!empty($_SERVER['REQUEST_URI']))
            {
                if(preg_match('/^\/[0-9a-zA-Z_\-\?\.&=]{1,150}$/', $_SERVER['REQUEST_URI']))
                {
                    $this->smarty->assign('redirect', htmlspecialchars(basename($_SERVER['REQUEST_URI'])));
                }
            }
            
            $this->title = 'Login';
            $this->content = "form";
        }
        
        function action_register()
        {
            $this->title = "Registrierung";
            if($this->user->loggedIn())
                $this->accessDenied('Du hast bereits einen Account. Du kannst keine weiteren Accounts anlegen!');
                
            $this->content = "register";
            $this->smarty->assign('nonavi', true);
            $this->smarty->assign('nosubnavi', true);
            
            if(isset($_POST['filled']))
            {
                $model = new UserModel($this->mysql);
                
                $reg = array();
                $reg['name'] = isset($_POST['name']) ? $_POST['name'] : '';
                $reg['email'] = isset($_POST['email']) ? $_POST['email'] : '';
                $reg['pass'] = isset($_POST['pass']) ? $_POST['pass'] : '';
                
                if(!Validator::nick($reg['name']))
                    $this->errors[] = "Ungültiger Nickname!";
                if(!Validator::email($reg['email']))
                    $this->errors[] = "Ungültige EMail-Addresse!";
                if(strlen($reg['pass']) < 6)
                    $this->errors[] = "Das Passwort muss mindestens 6 Zeichen lang sein!";
                $existing = $model->get('COUNT(*) AS anzahl', '', 'name = "'.$reg['name'].'"', 1);
                if(!$existing)
                    $this->sqlError();
                if($existing[0]['anzahl'] == 1)
                    $this->errors[] = "Dieser Benutzername ist bereits belegt.";
                    
                if(count($this->errors) > 0)
                    return;
                    
                $success = $model->insert('name, email, pass, activated', array($reg['name'], $reg['email'], md5($reg['pass']), 0));
                
                if($success)
                    $this->flash('index.php', 'Du hast dich erfolgreich registriert. Du wirst per EMail benachrichtigt sobald du freigeschaltet wirst.');
                else
                    $this->sqlError();
            }
        }
        
        function action_dologin()
        {
            $this->smarty->assign('nosubnavi',true);
            $this->title = "Login";
            $this->smarty->assign('nonavi',true);
            
                
            if($this->user->loggedIn())
                $this->errors[] = "Du bist bereits eingeloggt.";
                
            if(empty($_POST['user']) or empty($_POST['pass']))
            {
                $this->errors[] = "Kein Benutzername/Passwort angegeben.";
            }
            
            if(count($this->errors) > 0)
                return;
            
            // login
            $user = $_POST['user'];
            $pass = $_POST['pass'];
            $query = $this->mysql->sql_query("SELECT
                                                    id,
                                                    name,
                                                    pass,
                                                    activated,
                                                    admin,
                                                    lastlogin
                                                    FROM xdb_users
                                                    WHERE name = '".addslashes($user)."'
                                                    LIMIT 1");
            if($query and $this->mysql->sql_num_rows($query) == 1)
            {
                $data = $this->mysql->sql_fetch_assoc($query);
                    
                global $obst;
                if(!$obst['online'] and !$data['admin'])
                {
                    $this->errors[] = 'Der Login ist momentan nicht möglich, da diese OBST-Installation deaktiviert wurde.';
                    return;
                }
                
                if($data['activated'] == 1 or $data['admin'] == 1)
                {
                    if($data['pass'] == md5($pass))
                    {
                        $this->user->setVal('logged_in', 1);
                        $this->user->setVal('name', $data['name']);
                        $this->user->setVal('id', $data['id']);
                        $this->user->setVal('ip', $_SERVER['REMOTE_ADDR']);
                        $this->user->setVal('lastlogin', $data['lastlogin']);
                        $this->user->refreshPrivileges();
                        
                        $this->mysql->sql_query('UPDATE xdb_users SET lastlogin = "'.time().'" WHERE id = '.$data['id'].' LIMIT 1');
                        
                        if(empty($_POST['redirect']))
                            $this->flash('index.php', 'Du bist jetzt eingeloggt.');
                        else
                            $this->flash($_POST['redirect'], 'Du bist jetzt eingeloggt.');
                    }
                    else
                    {
                        $this->errors[] = "Benutzername oder Passwort falsch (oder SQL-Fehler).";
                    }
                }
                else
                {
                    $this->accessDenied("Dein Account ist deaktiviert.");
                }
            }
            else
            {
                $this->errors[] = "Benutzername oder Passwort falsch (oder SQL-Fehler).";
            }
            
            $this->debuginfo[] = "SQL-Fehlermeldung: ".$this->mysql->lasterror;
        }
        
        function action_logout()
        {
            $this->title = "Logout";
            $this->user->destroy();
            $this->flash("index.php", "Du bist jetzt ausgeloggt.");
        }
        
        function action_options()
        {
            $this->title="Benutzeroptionen";
            $this->content="options";
            
            $id = addslashes($this->user->getVal('id'));
            if(empty($id))
            { $this->errors[] = "Keine BenutzerID..."; return; }
            
            $model = new UserModel($this->mysql);
            $user = $model->getById($id);
            
            if(isset($_POST['filled']))
            {
                $new['email'] = addslashes($_POST['email']);
                
                if(empty($_POST['email']) or !Validator::email($_POST['email']))
                    $this->errors[] = "Keine gültige EMail-Addresse angegeben!";
                if(!empty($_POST['pass_new']))
                {
                    if($_POST['pass_new'] != $_POST['pass_new_confirm'])
                        $this->errors[] = "Die beiden Passwörter sind nicht gleich!";
                    if(md5($_POST['pass_old']) != $user['pass'])
                    {
                        $this->errors[] = "Das alte Passwort stimmt nicht!";
                        $this->log->log('Passwort ändern wurde verweigert, da das alte Passwort nicht stimmt', $this->user);
                    }
                }
                
                if(count($this->errors) > 0)
                    return;
                    
                    
                // save the changes
                $new_pass = '';
                if(!empty($_POST['pass_new']))
                {
                    $new_pass = "pass = '".md5($_POST['pass_new'])."',";
                    $this->smarty->assign('password_changed', true);
                }
                
                
                $query = "UPDATE xdb_users SET
                                                $new_pass
                                                email='".$new['email']."'
                                                
                                                WHERE id = '$id'
                                                LIMIT 1";
                
                if(!$this->mysql->sql_query($query))
                {
                    $this->sqlError();
                    return;
                }
                
                $this->smarty->assign('saved', true);
                
                // get updated user information
                $user = $model->getById($id);
            }
            
            $this->smarty->assign('user', $user);
        }
        
    };

?>