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
    
    class Validator {
    
        public static function email($str)
        {
            if(preg_match('/[a-zA-Z0-9\-_\.]{1,50}@[a-zA-Z0-9\-_]+\.[a-zA-Z]{1,3}/', $str))
                return TRUE;
            
            return FALSE;
        }
        
        public static function nick($str)
        {
            if(preg_match('/[a-zA-Z0-9\-_]{3,30}/', $str))
                return TRUE;
            
            return FALSE;
        }
        
        public static function pass($str)
        {
            if(strlen($str) >= 6)
                return true;
            
            return false;
        }
    };

?>