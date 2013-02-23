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

    if(!defined('XDB_INCCHECK')) die('access denied');
    
    /* CONFIGURATION of OBST */
    
        $obst = array();
        
        /* You can deactivate OBST completely by setting this variable to "false"
        Note: only admins can log in while obst is disabled */
        $obst['online'] = true;
        
        /* If you deactivate OBST you can set up a reason to be displayed to the users, explaining
        why OBST is currently deactivated for them. */
        $obst['offline_reason'] = 'Es wurde kein Grund angegeben.';
        
        /* The url to your OBST installation. This url is used in emails for instance... */
        $obst['url'] = 'http://www.yourdomain.com';
        
        /* The email of the OBST administrator. Is used in emails for instance */
        $obst['email'] = 'yourname@yourdomain.com';
        
        /* The Tag of your tribe */
        $obst['name'] = '-TAG-';
        
        /* the currently activated/used style */
        $obst['style'] = 'std';
        
        /* the userid of the system administrator, who has special privileges */
        $obst['sysadmin'] = 1;
        
        /* IMPORTANT: This array specifies the worlds for which this installation of OBST is enabled */
        $obst['worlds'] = array(60);
        
        /* This setting enables/disables OBST's debugmode. */
        define('OBST_DEBUG',TRUE); // debug mode? (SQL queries will be printed out for example)
        define('OBST_DEBUG_PARSING',FALSE); // specifies whether special data will be printed out when parsing in a report
        
        /* This setting specifies the path to smarty, OBST's template engine */
        define('OBST_SMARTYDIR',OBST_ROOT.'/../smarty'); // smarty directory
        
        // unit configuration
        require(OBST_ROOT.'/include/class.DSUnit.php');
        require(OBST_ROOT.'/include/config.units.php'); // IMPORTANT: LOOK IN THIS FILE, TOO!
        
        // mysql configuration
        $mysql_user='db_username';
        $mysql_pass='db_password';
        $mysql_host='db_host'; // the database host address
        $mysql_name='db_name'; // the database name
    
    /* END configuration of OBST */
?>