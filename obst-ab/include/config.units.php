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
    
    /*
    Einheitenkonfiguration
    
    die entsprechenden Abschnitte sind einzeln kommentiert und daher selbsterklärend.
    An dieser Stelle möchte ich etwas zur Tabellenstruktur sagen:
        > Die Vereinigungsmenge aller Einheitenkonfigurationen muss auch
        > den Spalten für die Einheiten in der Tabelle entsprechen.
        >
        > D.h. konkret, dass in der Tabelle der Berichte für jeden Einheitentyp, egal in welcher
        > Einheitenkonfiguration er vorkommt, eine passende Spalte in der Tabelle xdb_reports
        > vorhanden sein muss.
        > In der Standardkonfiguration sind glücklicherweise ;) alle bisher bekannten
        > DS-Einheiten bereits abgedeckt!
    */
    
    $obst_units = array();
    
    foreach($obst['worlds'] as $world)
    {
        $obst_units[$world] = array();
    }
    
    function setUnits($world, $units)
    {
        global $obst_units;
        $obst_units[$world] = $units;
    }
    
    /* standardeinheitenkonfiguration */
    $units_std = array(
                    new DSUnit('spear', 'Speerträger'),
                    new DSUnit('sword', 'Schwertkämpfer'),
                    new DSUnit('axe', 'Axtkämpfer'),
                    new DSUnit('archer', 'Bogenschütze'),
                    new DSUnit('spy', 'Späher'),
                    new DSUnit('light', 'Leichte Kavallerie'),
                    new DSUnit('marcher', 'Berittener Bogenschütze'),
                    new DSUnit('heavy', 'Schwere Kavallerie'),
                    new DSUnit('ram', 'Rammbock'),
                    new DSUnit('catapult', 'Katapult'),
                    new DSUnit('knight', 'Paladin'),
                    new DSUnit('snob', 'Adelsgeschlecht')
                   );

    /*
    an dieser Stelle können besondere Einheitenkonfigurationen für extravagante Welten wie Welt 4
    vorgenommen werden
    Beispiel:
            setUnits(4, array(
                            new DSUnit('spear', 'Speerträger'),
                            new DSUnit('sword', 'Schwertkämpfer'),
                            new DSUnit('axe', 'Axtkämpfer'),
                            new DSUnit('spy', 'Späher'),
                            new DSUnit('light', 'Leichte Kavallerie'),
                            new DSUnit('heavy', 'Schwere Kavallerie'),
                            new DSUnit('ram', 'Rammbock'),
                            new DSUnit('catapult', 'Katapult'),
                            new DSUnit('knight', 'Priester'),
                            new DSUnit('snob', 'Adelsgeschlecht')
                        );
    */
    // ...
    
    /*
    alle Welten, denen bis hierher noch nicht explizit eine Einheitenkonfiguration zugewiesen wurde,
    erhalten hier die Standardkonfiguration
    */
    foreach($obst_units as $world => $config)
    {
        if(count($config) == 0)
        {
            $obst_units[$world] = $units_std;
        }
    }
?>