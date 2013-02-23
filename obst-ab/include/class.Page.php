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
    abstract class Page {
   
        public $action;
        public $debuginfo;
        public $errors;
        protected $smarty;
        protected $mysql;
        protected $user;
        public $title;
        protected $log;
        protected $template;
        protected $content;
        
        public function Page(xdbSmarty &$smarty, simpleMySQL &$mysql, User &$user, Logger &$log)
        {
            $this->user = $user;
            $this->smarty = $smarty;
            $this->mysql = $mysql;
            $this->log = $log;
            $this->errors = array();
            
            $this->init();

            $this->doAction();
            $this->smarty->assign('title', $this->title);
            $this->displayErrors();
            
            
            $this->smarty->assign('content', $this->template."_".$this->content.".tpl");
            $this->smarty->display($this->template.".tpl");
        }
        
        public abstract function init();
        
        protected function doAction()
        {
            if(!empty($_GET['action']))
            {
                $this->action = $_GET['action'];
                $actionMethod = "action_".$this->action;
                
                if(method_exists($this, $actionMethod))
                {
                    $this->$actionMethod();
                }
                else
                {
                    $this->defaultAction();
                }
            }
            else
            {
                $this->defaultAction();
            }
        }
        
        /**
          * If there is no action, which should be executed, this method will be called.
          */
        abstract function defaultAction();
        
        
        protected function displayErrors($force=false)
        {
            // the parameter $force forces the output of the error template, even if there actually are no errors (e.g. if you want to output some debuginformation, but no errors)
            if(count($this->errors) > 0 or $force)
            {
                $this->smarty->assign('errors', $this->errors);
                $this->smarty->assign('debuginfo', $this->debuginfo);
                $this->smarty->assign('onlybody', true);
                $this->smarty->assign('content', 'display_errors.tpl');
                $this->smarty->display($this->template.".tpl");
                exit;
            }
        }
        
        protected function redirect($newPage)
        {
            $newPage = trim($newPage);
            header("Location: $newPage");
            exit;
        }
        
        protected function flash($target, $message)
        {
            $this->smarty->assign('title', $this->title);
            $this->smarty->assign('redirect', $target);
            $this->smarty->assign('message', $message);
            $this->smarty->display('flash.tpl');
            exit;
        }
        
        /**
          * This method checks whether the user has got the right specified $access.
          * 
          * Currently the rights management of OBST is quite simple and inefficient, so,
          * looking to the future, this method will hardly persist.
          */
        protected function checkAccess($access)
        {
            if(!$this->user->can($access))
            {
                $this->log->log('Zugriff verweigert ('.$access.')', $this->user);
                $this->accessDenied();
            }
        }
        
        /**
          * This function checks whether the current user has got a level which is at least as high
          * as $level. If not, accessDenied() is called with the arguments $message and $log, which are both
          * optional. So, this method stops the execution of the site and displays a denial message,
          * if the user hasnt got a userlevel, which is at least as high as $level.
          */
        protected function checkLevel($level, $message='', $log='')
        {
            if($level > $this->user->level())
            {
                $this->accessDenied($message, $log);
            }
        }
        
        /**
          * Stops the execution of the site by displaying an access denied message, which
          * can be optionally specified by $message. If not, a standard message is shown.
          *
          * If the denial should be logged with a special message, this can be specified by $log.
          * @param string $message  optional; the message displayed to the user, describing the denial
          * @param string $log      optional; the comment which is added to the log
          */
        protected function accessDenied($message = '', $log='')
        {
            if(!empty($message))
                $this->smarty->assign('message', $message);
            
            $this->log->log('Zugriff verweigert ('.$log.')', $this->user);

            $this->smarty->assign('title', $this->title." / Zugriff verweigert!");
            $this->smarty->assign('content', 'access_denied.tpl');
            $this->smarty->display($this->template.".tpl");
            exit;
        }
        
        /**
          * This function mainly is intended to be a help for the developer. :-)
          * It easily puts error and debug information to the according arrays ($errors, $debuginfo).
          * These are later automatically assigned to the smarty instance, to be displayed to the user.
          */
        protected function sqlError()
        {
            $this->addError("Es ist ein SQL-Fehler aufgetreten.");
            $this->addDebug("SQL-Fehlermeldung: ".$this->mysql->lasterror);
            $this->addDebug("SQL-Abfrage: <br /><pre>".$this->mysql->lastquery."</pre>");
        }
        
        protected function addError($msg)
        {
            $this->errors[] = $msg;
        }
        
        protected function addDebug($msg)
        {
            if(OBST_DEBUG)
            {
                $this->debuginfo[] = $msg;
            }
        }
    
    };
?>