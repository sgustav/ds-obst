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
    class User {
    
        public $rights;
        public $mysql;
        public $worlds;
        
        function User($mysql)
        {
            $this->mysql = $mysql;
            
            @session_start();
            
            $this->rights = array(  'can_reports_comment',
                                    'can_reports_comments_delete',
                                    'can_reports_view',
                                    'can_reports_mass_edit',
                                    'can_reports_mass_edit_regroup',
                                    'can_reports_mass_edit_setworld',
                                    'can_reports_mass_edit_delete',
                                    'can_reports_parse',
                                    'can_reports_delete',
                                    'can_users',
                                    'can_users_edit',
                                    'can_users_rights'
                                 );
            
            if(!isset($_SESSION['logged_in']))
            {
                $this->initSession();
            }
            else if ($this->loggedIn())
            {
                // die Session überprüfen
                if($this->getVal('ip') != $_SERVER['REMOTE_ADDR'])
                    $this->destroy();
                    
                // die Benutzerrechte aktualisieren
                $this->refreshPrivileges();
            }
        }
        
        
        function getId()
        {
            return $this->getVal('id');
        }
        
        function loggedIn()
        {
            if($this->getVal('logged_in'))
                return true;
            else
                return false;
        }
        
        function isAdmin()
        {
            if($this->getVal('admin') == '1')
                return true;
            
            return false;
        }
        
        function initSession()
        {
            // Gast
            session_regenerate_id(true);
            $this->setVal('user_level', 0);
            $this->setVal('admin', 0);
            $this->setVal('logged_in', FALSE);
            $this->setVal('name', '');
            $this->setVal('worlds', '');
        }
        
        function getVal($name)
        {
            if(isset($_SESSION[$name]))
                return $_SESSION[$name];
            else
                return FALSE;
        }
        
        function setVal($name, $value)
        {
            $_SESSION[$name]=$value;
        }
        
        function has_access_to_world($world)
        {
            if($world == 0)
                return true;
                
            if($this->getVal('admin') == 1)
                return true;
            
            if(count($this->worlds) == 0)
                return true;
                
            if(array_search($world, $this->worlds) !== false)
                return true;
            
            return false;
        }
        
        function getWorlds()
        {
            return $this->worlds;
        }
        
        function refreshPrivileges()
        {
            if($this->loggedIn())
            {
                $rights = '';
                foreach($this->rights as $right)
                    $rights .= "$right,\n";
                    
                $query = "SELECT    admin,
                                    activated,
                                    worlds,
                                    $rights
                                    user_level
                          FROM xdb_users
                          WHERE id = '".$this->getVal('id')."' LIMIT 1";
                
                $result = $this->mysql->sql_query($query);
                
                if(!$result)
                    throw new Exception("Die Benutzerrechte konnten nicht aktualisiert werden!\n".
                                        "SQL-Fehler: ".$this->mysql->lasterror.
                                        "\nSQL-Abfrage: ".$query);
                
                $data = $this->mysql->sql_fetch_assoc($result);
                
                $this->setVal('admin', intval($data['admin']));
                $this->setVal('user_level', intval($data['user_level']));
                $this->setVal('activated', intval($data['activated']));
            
                $this->setVal('worlds', $data['worlds']);
                $this->worlds = array();
                if(strlen($data['worlds']) > 0)
                    $this->worlds = explode(',', $data['worlds']);
                else
                {
                    global $obst;
                    $this->setVal('worlds', implode(',', $obst['worlds']));
                    $this->worlds = $obst['worlds'];
                }
                
                foreach($this->rights as $right)
                {
                    if(is_numeric($data[$right]))
                        $data[$right] = intval($data[$right]);
                    $this->setVal($right, $data[$right]);
                }
                
                /*
                $this->setVal('can_reports_comments_delete', intval($data['can_reports_comments_delete']));
                $this->setVal('can_reports_delete', intval($data['can_reports_delete']));
                $this->setVal('can_reports_parse', intval($data['can_reports_parse']));
                $this->setVal('can_reports_view', intval($data['can_reports_view']));
                $this->setVal('can_users', intval($data['can_users']));
                $this->setVal('can_users_edit', intval($data['can_users_edit']));
                $this->setVal('can_users_rights', intval($data['can_users_rights']));
                */
            
            }
        }
        
        function level()
        {
            return $this->getVal('user_level');
        }
        
        function can($privilege)
        {
            global $obst;
            
            if($this->getVal("can_$privilege") == 1 or $obst['sysadmin'] == $this->getId())
                return true;
            else
                return false;
        }
        
        function destroy()
        {
			session_destroy();
            $this->initSession();
        }
    }

?>