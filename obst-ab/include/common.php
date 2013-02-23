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

    
    // UTF-8-Kodierung auch über den Content-Type-Header erzwingen
    // (bei manchen HTTP-Servern kommt es vor, dass dieser per Content-Type-Header ein anderes Charset vorgibt)
    header('Content-Type:text/html; charset=utf-8');
    
    // PHP-errors should be shown only in debugmode!
    if(OBST_DEBUG)
    {
        error_reporting(E_ALL);
    }
    else
        error_reporting(E_ALL ^ E_NOTICE);

    // some other constants
    define('OBST_DIR_TEMPLATE', OBST_ROOT.'/styles/'.$obst['style'].'/tpl');
    
    // wichtige Dateien (Funktionsbibliotheken/Klassen) inkludieren
    define('INC_CHECK_DSBERICHT',TRUE);     // class.dsBericht.php
    define('SSQL_INC_CHECK',TRUE);            // class.simpleMySQL.php
    define('OC_INC_CHECK',TRUE);            // class.outputControl.php
    //require(OBST_DIR_INCLUDE.'/class.outputControl.php');
    if(OBST_DEBUG_PARSING) define('DSBERICHT_DEBUG', TRUE);
    require(OBST_ROOT.'/include/version.inc.php');
    require(OBST_ROOT.'/include/class.dsBericht.php');
    require(OBST_ROOT.'/include/class.simpleMySQL.php');
    require(OBST_ROOT.'/include/class.xdbSmarty.php');
    require(OBST_ROOT.'/include/class.user.php');
    require(OBST_ROOT.'/include/class.Page.php');
    require(OBST_ROOT.'/include/class.Logger.php');
    require(OBST_ROOT.'/include/class.Validator.php');
    require(OBST_ROOT.'/include/models/class.BaseModel.php');
    require(OBST_ROOT.'/include/models/class.UserModel.php');
    require(OBST_ROOT.'/include/models/class.ReportsModel.php');
    require(OBST_ROOT.'/include/models/class.ReportCommentsModel.php');
    require(OBST_ROOT.'/include/models/class.ReportGroupsModel.php');

    // MySQL-Verbindung aufbauen
    if(!function_exists('mysql_connect'))
    {
        die("MySQL ist auf diesem Server nicht installiert!");
    }
    $mysql=new simpleMySQL($mysql_user, $mysql_pass, $mysql_name, $mysql_host);
    if(!$mysql->connected())
    {
        die("Konnte keine MySQL-Verbindung aufbauen. Bitte überprüfe die Konfiguration!<br />\nFehlermeldung: ".$mysql->lasterror);
    }
    
    // sicherheitsrelevante Variablen vernichten...
    $mysql_pass='---------------';
    unset($mysql_user);
    unset($mysql_pass);

    // alle Einstellungen in ein Array laden, damit sie überall verfügbar sind
    $xdb_sett = loadCfg();

    // einige praktische Funktionen

    /**
     * diese Funktion läd alle Einstellungen in ein Array (jeweils in $cfg['nameDerEinstellung'])
     */ 
    function loadCfg()
    {
        $cfg=array();

        global $mysql;
        if(isset($mysql))
        {
            $erg = $mysql->sql_query('SELECT * FROM xdb_config');
            $i=0;
            $count=$mysql->sql_num_rows($erg);

            for($i; $i < $count; $i++)
            {
                $cfg[$mysql->sql_result($erg, $i, 'cfg_name')] = $mysql->sql_result($erg, $i, 'cfg_value');
            }

            return $cfg;
        }

        return FALSE;
    }

    // diese Funktion liefert eine aktuelle Einstellung aus der Datenbank
    function getCfg($name, $std=FALSE)
    {
        global $mysql;
        if(isset($mysql))
        {
            $erg = $mysql->sql_query('SELECT cfg_value FROM xdb_config WHERE cfg_name = "'.$name.'" LIMIT 1');
            if($erg and $mysql->sql_num_rows($erg)==1)
            {
                return $mysql->sql_result($erg, 0, 'cfg_value');
            }
            else
            {
                trigger_error('in getCfg("'.$name.'"): config value not found or sql error! returned standard value.', E_USER_WARNING);
                return $std;
            }
        }
        else
        {
            trigger_error('in getCfg("'.$name.'"): SQL connection not available.', E_USER_WARNING);
        }


        return $std;
    }


    // leitet den User zum Login weiter wenn er nicht eingeloggt ist
    function check_logged_in()
    {
        global $user;

        // wenn nicht eingeloggt weiterleiten
        if(!$user->getVal('logged_in'))
            return false;
        
        if(!$user->getVal('activated') and !$user->getVal('admin'))
        {
            $user->destroy();
            return false;
        }
        
        return true;
    }


    // bestimmt ob der User eingeloggt ist oder nicht
    function logged_in()
    {
        global $user;

        if($user->getVal('logged_in'))
            return TRUE;
        else
            return FALSE;
    }

    // bestimmt ob ein User die nötigen Berechtigungen hat
    function requireAccess($level)
    {
        global $user;

        if(intval($user->getVal('access_level', 0)) >= $level)
            return TRUE;
        else
            return FALSE;
    }



    // diese Funktion wird immer aufgerufen, wenn die Seite nicht mehr online ist und gibt eine Offlinemessage aus...
    function notOnline()
    {
        global $output;

        if($output)
            $output->addP('<img src="'.picURL('ico_warning.png').'" border="0" alt="[BILD] Warnung" /> OBST ist momentan deaktiviert. Bitte probiere es später noch einmal.');
    }

    function picURL($pic)
    {
        return XDB_DIR_PICS.'/'.$pic;
    }

    // extrahiert DS-Koordinaten aus einem String (nur das erste Vorkommen!)
    function getCoordinates($string)
    {
        // $string darf nicht leer sein
        if(empty($string)) return FALSE;

        // array vorbereiten, das zurückgegeben wird
        $coords=array('x' => 0, 'y' => 0);

        // Koordinaten aus dem String extrahieren
        $matches=array();
        if(preg_match('/\(([0-9]+)\|([0-9]+)\)/',$string,$matches))
        {
            $coords['x']=$matches[1];
            $coords['y']=$matches[2];

            return $coords;
        }
        else
        {
            // wenn keine Koordinaten gefunden wurden, FALSE zurückgeben
            return FALSE;
        }
    }
?>