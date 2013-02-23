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
    
    error_reporting(E_ALL);

    define('XDB_INCCHECK',TRUE);
    define('OBST_ROOT', '.');
    
    if(!file_exists(OBST_ROOT.'/install/lock_setup'))
        die('Die Installation von OBST ist aktiviert. Bitte <a href="install/install.php">beende/deaktiviere die Installation</a>!');
        
    require OBST_ROOT.'/include/config.inc.php';
    require(OBST_ROOT.'/include/common.php');
    
   
    
    $user = new User($mysql);
    $log = new Logger();
    
    
    // smarty instanzieren
    $output=new xdbSmarty();
    $output->assign('obst', $obst); // general config specified in config.inc.php
    $output->assign('obst_user', $user);
    $output->assign('obst_user_worlds', $user->getWorlds());
    
    // admin?
    $is_admin = false;
    if($user->getVal('admin') == 1)
        $is_admin = true;
        
    $output->assign('is_admin', $is_admin);

    // show debuginfos?
    if($is_admin or OBST_DEBUG)
        $output->assign('show_debuginfo', true);
    

    if(!check_logged_in())
    {
        require OBST_ROOT."/include/pages/class.PageUser.php";
        $page = new PageUser($output, $mysql, $user, $log);
    }
    else
    {
        if($obst['online'] or $user->isAdmin())
        {
            $page = false;
            
            switch(isset($_GET['page']) ? $_GET['page'] : '')
            {
                case '':
                    require OBST_ROOT."/include/pages/class.PageOverview.php";
                    $page = new PageOverview($output, $mysql, $user, $log);
                    break;
                case 'user':
                    require OBST_ROOT."/include/pages/class.PageUser.php";
                    $page = new PageUser($output, $mysql, $user, $log);
                    break;
                case 'reports':
                    require OBST_ROOT."/include/pages/class.PageReports.php";
                    $page = new PageReports($output, $mysql, $user, $log);
                    break;
                case 'admin':
                    if($admin)
                    {
                        require OBST_ROOT."/include/pages/class.PageReports.php";
                        $page = new PageReports($output, $mysql, $user, $log);
                        break;
                    }
                default:
                    require OBST_ROOT."/include/pages/class.PageOverview.php";
                    $page = new PageOverview($output, $mysql, $user, $log);
            }
            
        }
        else
        {
            $user->destroy();
            
            $output->assign('offline_reason', $obst['offline_reason']);
            $output->display('offline.tpl');
        }
    }
?>