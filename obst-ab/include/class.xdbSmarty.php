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

    // stellt die Smartyklasse bereit
    // URL: http://smarty.php.net
    
    require(OBST_SMARTYDIR.'/Smarty.class.php');
    
    class xdbSmarty extends Smarty {
    
        function xdbSmarty()
        {
            $this->template_dir = OBST_DIR_TEMPLATE.'/';
            $this->compile_dir = OBST_ROOT.'/cache/compiled';
            $this->cache_dir = OBST_ROOT.'/cache';
            
            $this->assign('obst_root', OBST_ROOT);
        }
    
    }

?>