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
    
    header('Content-Type: text/html; charset=utf-8');
    error_reporting(E_ALL);
    
    define('OBST_ROOT', '..');
    
    $action = !empty($_GET['action']) ? $_GET['action'] : '';
    
    require(OBST_ROOT.'/include/version.inc.php');
    
    echo '<h1>DS OBST <span style="font-size: 75%;">v'.$obst['version_all'].'</span> - Installation</h1>';
    
    if(file_exists('lock_setup'))
        die('Die Installation ist zur Zeit gesperrt. Entferne die Datei "lock_setup", um die Installation zu reaktivieren.');
    
    function stepurl($no, $text='') { return "<a href=\"install.php?action=step$no\">".(!empty($text) ? $text : 'Weiter')."</a>"; }
    
    
    switch($action)
    {
        case '':
            echo 'Bitte benenne als erstes die Datei /config/localconfig.sample.php um zu localconfig.php und konfiguriere OBST darin (mit einem Text-Editor). '.stepurl(2);
            break;
        case 'step2':
            $requirements_fulfilled = true;
            echo 'Es wird nun geprüft, ob alle Vorraussetzungen für OBST erfüllt sind. Dies ist '.
                 'jedoch keine Garantie dafür, dass OBST tatsächlich funktionieren wird.<br />';
            echo 'PHP-Version (benötigt: PHP 5+): '.checkPHP();
            
            if($requirements_fulfilled)
                echo '<br />'.stepurl(3);
            else
                echo '<br />Sorry, aber nicht alle Anforderungen sind erfüllt!';
            break;
        case 'step3':
            echo 'Es wird nun versucht, das Cache-Verzeichnis beschreibbar zu machen... ';
            if(!is_writable(OBST_ROOT.'/cache/compiled'))
            {
                if(chmod(OBST_ROOT.'/cache/compiled', 0777) and is_writable(OBST_ROOT.'/cache/compiled'))
                {
                    echo 'das Cache-Verzeichnis ist jetzt beschreibbar...'.stepurl(4);
                }
                else
                {
                    echo 'das Cache-Verzeichnis ist nicht beschreibbar! Bitte aktiviere Schreibrechte für dieses Verzeichnis! '.stepurl(3,'nochmal probieren');
                }
            }
            else
            {
                echo 'das Cache-Verzeichnis ist bereits beschreibbar...'.stepurl(4);
            }
            break;
        case 'step4':
            echo 'Bitte führe die SQL-Kommandos in der Datei /install/my.sql aus (z.B. per phpMyAdmin).'.
                 'Dies wird die Tabellenstruktur von OBST anlegen. '.stepurl(5);
            break;
        case 'step5':
            ?>
            
            <form action="install.php?action=step6" method="post">
                <h3>Einrichten des Administratoraccounts</h3>
                Benutzername: <input type="text" name="admin_nick" /> (mind. 3, max. 30 Zeichen)<br />
                Passwort: <input type="password" name="admin_pass" /> (mind. 6 Zeichen)<br />
                EMail: <input type="text" name="admin_email" /><br />
                <input type="submit" value="Anlegen" />
            </form>
            
            <?php
            break;
        case 'step6':
            require(OBST_ROOT.'/include/class.Validator.php');
            if(empty($_POST['admin_nick']) or !Validator::nick($_POST['admin_nick']))
                die('Keinen gültigen Adminnamen angegeben!');
            if(empty($_POST['admin_pass']) or !Validator::pass($_POST['admin_pass']))
                die('Kein gültiges Passwort angegeben!');
            if(empty($_POST['admin_email']) or !Validator::email($_POST['admin_email']))
                die('Keine gültige EMail-Addresse angegeben!');
                
            define('XDB_INCCHECK', true);
            define('SSQL_INC_CHECK',TRUE);
            require(OBST_ROOT.'/include/config.inc.php');
            require(OBST_ROOT.'/include/class.simpleMySQL.php');
            
            $mysql = new simpleMySQL($mysql_user, $mysql_pass, $mysql_name, $mysql_host);
            if($mysql->connected())
            {
                // gibt es bereits einen account?
                if($mysql->sql_result($mysql->sql_query('SELECT COUNT(id) AS anzahl FROM xdb_users WHERE admin=1'),0,'anzahl') > 0)
                    die('Es gibt bereits einen Administratoraccount. '.stepurl(7));
                
                // anlegen des administrator accounts
                $nick = addslashes($_POST['admin_nick']);
                $pass = md5($_POST['admin_pass']);
                $email = addslashes($_POST['admin_email']);
                
                $success = $mysql->sql_query("INSERT INTO xdb_users (id, name, pass, email, admin, activated, worlds, can_reports_comment, can_reports_comments_delete, can_reports_delete, can_reports_mass_edit, can_reports_mass_edit_regroup, can_reports_mass_edit_setworld, can_reports_mass_edit_delete, can_reports_parse, can_reports_view, can_users, can_users_edit, can_users_rights) VALUES ".
                                                                    "(1, '$nick', '$pass', '$email', 1, 1, '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)");
                if(!$success)
                    die('SQL-Fehler: '.$mysql->lasterror);
                else
                    echo 'Account angelegt! '.stepurl(7);
            }
            else
            {
                echo 'FEHLER: '.$mysql->lasterror;
                die('<br />Bitte überprüfe die Konfiguration!');
            }
                
            break;
        case 'step7':
            // das lock erstellen
            $fh = fopen('lock_setup', 'w');
            if(!$fh or !file_exists(OBST_ROOT.'/install/lock_setup'))
                echo '<b>WARNUNG: </b>Die Datei "/install/lock_setup" konnte nicht erstellt werden.'.
                     'Du musst sie manuell erstellen um die Installation zu beenden/deaktivieren.<hr />';
            fclose($fh);
            
            echo 'Die Installation von OBST ist abgeschlossen und wurde zur Sicherheit gesperrt.';
            echo '<br /><a href="../index.php">Los gehts</a>.';
            break;
    }
    
    
    function checkPHP()
    {
        global $requirements_fulfilled;
        
        $local = phpversion();
        $required = '5.0.0';
        
        if(version_compare($local, $required, '<'))
        {
            $requirements_fulfilled = false;
            return 'nicht ausreichend';
        }
        else
            return 'OK';
    }
?>