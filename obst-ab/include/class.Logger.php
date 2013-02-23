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
    class Logger {
    
        private $path;
        private $logfile;
        
        function __construct()
        {
            $this->path = OBST_ROOT.'/include/logger.log';
            
            if((file_exists($this->path) and !is_writable($this->path)) or (!file_exists($this->path) and !is_writable(OBST_ROOT.'/include')))
                throw new Exception('In das Verzeichnis /include bzw. in die Datei /include/logger.log kann nicht geschrieben werden! Die Schreibrechte müssen für diese Datei bzw. diesen Ordner aktiviert sein!');
                
            $this->logfile = fopen($this->path,'a');
        }
        
        function __destruct()
        {
            fclose($this->logfile);
        }
        
        function log($message, User $user)
        {
            $write = date('d.m.Y, H:i:s').' | IP: '.$_SERVER['REMOTE_ADDR'].' | User: '.$user->getVal('name').' | '.$message;
            $write .= "\n";
            fputs($this->logfile, $write);
        }
    
    };
    
?>