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
    
    define('XDB_INCCHECK',TRUE);
    define('OBST_ROOT', '..');
    require OBST_ROOT.'/include/config.inc.php';
    require(OBST_ROOT.'/include/common.php');
    
    $user = new User($mysql);
    $log = new Logger();
    
    
    // smarty instanzieren
    $output=new xdbSmarty();
    $output->assign('obst', $obst); // general config specified in config.inc.php
    $output->assign('obst_user', $user);
    
    // admin?
    $is_admin = false;
    if($user->getVal('admin'))
        $is_admin = true;
    $output->assign('is_admin', $is_admin);
    $output->assign('nonavi', true);
    
    // show debuginfos
    $output->assign('show_debuginfo', true);
    
    if($is_admin)
    {
        require OBST_ROOT."/include/pages/class.PageAdmin.php";
        $page = new PageAdmin($output, $mysql, $user, $log);
    }
    else
    {
        $log->log('Zugriff verweigert (Administration allgemein)', $user);
        die("Kein Adminzugriff!");
    }
?>