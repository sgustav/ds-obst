<?php
    // copyright by Robert Nitsch, 2006-2007
    
    /*
    DS_Bericht

    DS_Bericht is a PHP class which can parse reports of the german browsergame DieStämme. (DieStämme is currently being ported to other countries)
    This class has been written by Robert 'bmaker' Nitsch for the OBST
    */


define('DSBERICHT_VERSION','0.2.0.1');
define('DSBERICHT_DATE','09.04.2007 19:19');

if(isset($_GET['showsource']))
{
    echo '<?xml encoding="utf-8"?>';
    echo "\n";
    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>class dsBericht SOURCE</title>
    </head>
    <body>
    <h1>class dsBericht SOURCE</h1>
    <?php
    echo '<p>Version: '.DSBERICHT_VERSION.'<br />Date: '.DSBERICHT_DATE.'</p>';
    echo '<p>&copy; copyright 2006-2007 by Robert Nitsch</p>';
    echo '<hr /><br />';
    show_source(__FILE__);
    echo '</body></html>';
    exit;
}

if(!defined('INC_CHECK_DSBERICHT'))
    die('hacking attempt');


if(!defined('DSBERICHT_DEBUG')) define('DSBERICHT_DEBUG',FALSE); // debugging can be activated from outside (before including this file)



class dsBericht {

    private $data;
    private $matches;
    public $report;
    public $units;
    
    private $troops_pattern;
    private $spied_troops_pattern;
    private $troops_out_pattern;
    
    function dsBericht($units)
    {
        $this->reset();
        
        $this->set_units($units);
    }

    function reset()
    {
        $this->matches=FALSE;
        $this->data=FALSE;
        $this->server='';
        $this->units = array();

        $this->report=array(
            'time' => FALSE,
            'winner' => FALSE,
            'luck' => FALSE,
            'moral' => FALSE,
            'attacker' => FALSE,
            'defender' => FALSE,
            'troops' => FALSE,
            'wall' => FALSE,
            'catapult' => FALSE,
            'spied' => FALSE,
            'buildings' => FALSE,
            'troops_out' => FALSE,
            'booty' => FALSE,
            'mood' => FALSE,
        );
    }

    function set_units($units)
    {
        if(is_array($units) && count($units) > 0)
        {
            $this->units = $units;
            
            // build the troops_patterns
            $this->build_troops_patterns();
        }
        else
            trigger_error('ERROR: invalid argument $units', E_USER_ERROR);
    }
    
    function build_troops_patterns()
    {
        $this->troops_pattern = '[le]:\s*';
        $this->spied_troops_pattern = 'Truppen\s+des\s+Verteidigers\s+in\s+anderen\s+Dörfern\s+\n.+\s+K\d+\s+';
        
        $this->spied_troops_pattern_spied = 'Einheiten\s+außerhalb:\s+\n';
        
        $this->spied_troops_village_pattern = '/Truppen\s+des\s+Verteidigers\s+in\s+anderen\s+Dörfern\s+\n(.+K\d+)/';
        $this->troops_out_pattern = 'Truppen des Verteidigers, die unterwegs waren\s+';
        for($i=0; $i<(count($this->units)-1); $i++)
        {
                $this->troops_pattern .= '([0-9x]+)\s+';
                $this->spied_troops_pattern .= '([0-9x]+)\s+';
                $this->spied_troops_pattern_spied .= '([0-9x]+)\s+';
                $this->troops_out_pattern .= '([0-9x]+)\s+';
        }
        
        $this->troops_pattern .= '([0-9x]+)';
        $this->spied_troops_pattern .= '([0-9x]+)';
        $this->spied_troops_pattern_spied .= '([0-9x]+)';
        $this->troops_out_pattern .= '([0-9x]+)';
        
        $this->troops_pattern = '/'.$this->troops_pattern.'/';
        $this->spied_troops_pattern = '/'.$this->spied_troops_pattern.'/';
        $this->spied_troops_pattern_spied = '/'.$this->spied_troops_pattern_spied.'/';
        $this->troops_out_pattern = '/'.$this->troops_out_pattern.'/';
        
        $this->no_village_information_pattern = '/Keiner\s+deiner\s+K\ämpfer\s+ist\s+lebend zur\ückgekehrt.\s+Es\s+konnten\s+keine\s+Informationen\s+\über\s+die\s+Truppenst\ärke\s+des\s+Gegners\s+erlangt\s+werden./';
        
    }
    
    // parses a complete report...
    function parse($data, $server='')
    {
        $error=FALSE;

        $this->data=$data;
        
        $this->report['time'] = $this->parse_time();
        $this->report['winner'] = $this->parse_winner();
        $this->report['luck'] = $this->parse_luck();
        $this->report['moral'] = $this->parse_moral();
        $this->report['attacker'] = $this->parse_attacker();
        $this->report['defender'] = $this->parse_defender();
        $this->report['troops'] = $this->parse_troops();
        $this->report['wall'] = $this->parse_wall();
        $this->report['catapult'] = $this->parse_catapult();
       
        if($this->preg_match_std('/Spionage/'))
        {
            $this->report['spied'] = $this->parse_spied();
            $this->report['buildings'] = $this->parse_buildings();
            $this->report['spied_troops_out'] = $this->parse_spied_troops();
            $this->report['spied_troops_village'] = $this->parse_spied_troops_village();
        }elseif($this->preg_match_std('/Truppen\s+des\s+Verteidigers\s+in\s+anderen\s+Dörfern/')){
           $this->report['spied_troops_out'] = $this->parse_spied_troops();
           $this->report['spied_troops_village'] = $this->parse_spied_troops_village();
        }
        
        $this->report['troops_out'] = $this->parse_troops_out();
        $this->report['booty'] = $this->parse_booty();
        $this->report['mood'] = $this->parse_mood();
        
        $this->report['dot'] = $this->parse_dot();
        
        if(DSBERICHT_DEBUG)
        {
            echo "\n\n";
            echo '<span style="font-weight: bold;">';
            print_r($this->report);
            echo '</span>';
            echo '<hr /><br />And this is the SQL VALUES part:<br />';
            echo $this->buildSQL('pseudotable');
            echo '<hr /><br />And this one is the associative array generated for the sql statement:<br />';
            print_r($this->buildAssoc());
        }

        // check whether all needed data has been parsed correctly. otherwise => error!
        if(!is_array($this->report['troops']))
            $error=TRUE;
        if($this->report['time'] === FALSE)
            $error=TRUE;
        if(!$this->report['winner'])
            $error=TRUE;
        if(!$this->report['luck'])
            $error=TRUE;

        if($error and DSBERICHT_DEBUG)
            echo "\nAn error occured: not all needed data could be parsed!\n";

        if($error)
        {
            $this->data = FALSE;
            return FALSE;
        }

        // report successfully parsed ...
        return TRUE;
    }

    function &getReport()
    {
        return $this->report;
    }

    function setReport(&$data)
    {
        $this->report=$data;
    }

    // implementation of the standard preg_match call
    function preg_match_std($pattern, $data='')
    {
        $this->currentPattern($pattern);

        $this->matches = FALSE;
        
        if(preg_match($pattern, (!empty($data) ? $data : $this->data), $this->matches))
        {
            $this->currentPattern_found(true);
            return TRUE;
        }


        $this->currentPattern_found(false);
        return FALSE;
    }

    // this function displays a small HTML code about the currently used pattern ... for debug purposes only.
    function currentPattern($pattern)
    {
        if(DSBERICHT_DEBUG)
            echo "Current regex pattern: <span style='color: #999999;'>".$pattern."</span> ...";
    }

    // this function generates the colored texts "found" or "not found" (so it echos HTML) ... for debug purposes only.
    function currentPattern_found($found)
    {
        if(DSBERICHT_DEBUG)
        {
            if($found)
                echo "<span style='color: #21FF21; font-weight: bold;'>found!</span>\n";
            else
                echo "<span style='color: #FF2121; font-weight: bold;'>not found!</span>\n";
        }
    }

    // returns the value according to one key of the $matches array, which is used to save preg_match data ...
    function match($count)
    {
        if($this->matches != FALSE)
        {
            return $this->matches[$count];
        }
        else
        {
            trigger_error('variable matches doesnt contain any data! returning FALSE', E_USER_WARNING);
            return FALSE;
        }
    }


    // builds an INSERT INTO query automatically
    function buildSQL($table, $extra_columns=false)
    {
        if(!$this->data) return '';
        
        // alle Daten zunächst in einem Array ablegen
        $data = $this->buildAssoc();
        if($extra_columns !== false)
            $data = array_merge($extra_columns, $data);
        
        $keys = '';
        $values = '';
        foreach($data as $key => $value)
        {
            $keys .= '`'.$key.'`';
            $keys .= ',';
            
            if(!is_numeric($value))
                $values .= "'$value', ";
            else
                $values .= "$value, ";
            $values .= "\n";
        }
        
        $values = trim($values);
        $values = trim($values, ",");
        $keys = trim($keys);
        $keys = trim($keys, ",");
        
        return 'INSERT INTO '.$table.' ('.$keys.') VALUES ('.$values.')';
    }
    
    // builds an associative array containing all data of the report
    function buildAssoc()
    {
        $assoc = array(
            /* general data */
            'time'         => ($this->report['time'] ? $this->report['time'] : 0),
            'winner'     => ($this->report['winner'] ? $this->report['winner'] : 1),
            'luck'        => ($this->report['luck'] ? $this->report['luck'] : 0.0),
            'moral'        => ($this->report['moral'] ? $this->report['moral'] : 0),
            /* attacker/defender data */
            'attacker_nick'        => (isset($this->report['attacker']['nick']) ? trim($this->report['attacker']['nick']) : 'unknown'),
            'attacker_village'     => (isset($this->report['attacker']['village']) ? trim($this->report['attacker']['village']) : 'unknown'),
            'attacker_coords'     => (isset($this->report['attacker']['coords']) ? trim($this->report['attacker']['coords']) : 'x|y'),
            'attacker_continent'     => (isset($this->report['attacker']['continent']) ? trim($this->report['attacker']['continent']) : -1),
            'defender_nick'     => (isset($this->report['defender']['nick']) ? trim($this->report['defender']['nick']) : 'unknown'),
            'defender_village'     => (isset($this->report['defender']['village']) ? trim($this->report['defender']['village']) : 'unknown'),
            'defender_coords'     => (isset($this->report['defender']['coords']) ? trim($this->report['defender']['coords']) : 'x|y'),
            'defender_continent'     => (isset($this->report['defender']['continent']) ? trim($this->report['defender']['continent']) : -1),
            /* troops */
            'troops'         => (is_array($this->report['troops']) ? 1 : 0),
            /* spied troops out */
            'spied_troops_out' => ((isset($this->report['spied_troops_out']) && is_array($this->report['spied_troops_out'])) ? 1 : 0),
            /* conquer troops out */
            'troops_out' => (is_array($this->report['troops_out']) ? 1 : 0),
            /* wall damage */
            'wall' => ($this->report['wall'] ? 1 : 0),
            'wall_before'     => (isset($this->report['wall']['before']) ? $this->report['wall']['before'] : 0),
            'wall_after'     => (isset($this->report['wall']['after']) ? $this->report['wall']['after'] : 0),
            /* catapult damage */
            'catapult' => ($this->report['catapult'] ? 1 : 0),
            'catapult_before' => (isset($this->report['catapult']['before']) ? $this->report['catapult']['before'] : 0),
            'catapult_after' => (isset($this->report['catapult']['after']) ? $this->report['catapult']['after'] : 0),
            'catapult_building' => (isset($this->report['catapult']['building']) ? $this->report['catapult']['building'] : ''),
            /* spied resources */
            'spied' => ($this->report['spied'] ? 1 : 0),
            'spied_wood' => (isset($this->report['spied']['wood']) ? intval(str_replace('.','',$this->report['spied']['wood'])) : 0),
            'spied_loam' => (isset($this->report['spied']['loam']) ? intval(str_replace('.','',$this->report['spied']['loam'])) : 0),
            'spied_iron' => (isset($this->report['spied']['iron']) ? intval(str_replace('.','',$this->report['spied']['iron'])) : 0),
            /* buildings */
            'buildings' => (is_array($this->report['buildings']) ? 1 : 0),
            'buildings_main' => (isset($this->report['buildings']['main']) ? $this->report['buildings']['main'] : 0),
            'buildings_barracks' => (isset($this->report['buildings']['barracks']) ? $this->report['buildings']['barracks'] : 0),
            'buildings_stable' => (isset($this->report['buildings']['stable']) ? $this->report['buildings']['stable'] : 0),
            'buildings_garage' => (isset($this->report['buildings']['garage']) ? $this->report['buildings']['garage'] : 0),
            'buildings_snob' => (isset($this->report['buildings']['snob']) ? $this->report['buildings']['snob'] : 0),
            'buildings_smith' => (isset($this->report['buildings']['smith']) ? $this->report['buildings']['smith'] : 0),
            'buildings_place' => (isset($this->report['buildings']['place']) ? $this->report['buildings']['place'] : 0),
            'buildings_statue' => (isset($this->report['buildings']['statue']) ? $this->report['buildings']['statue'] : 0),
            'buildings_market' => (isset($this->report['buildings']['market']) ? $this->report['buildings']['market'] : 0),
            'buildings_wood' => (isset($this->report['buildings']['wood']) ? $this->report['buildings']['wood'] : 0),
            'buildings_stone' => (isset($this->report['buildings']['stone']) ? $this->report['buildings']['stone'] : 0),
            'buildings_iron' => (isset($this->report['buildings']['iron']) ? $this->report['buildings']['iron'] : 0),
            'buildings_farm' => (isset($this->report['buildings']['farm']) ? $this->report['buildings']['farm'] : 0),
            'buildings_storage' => (isset($this->report['buildings']['storage']) ? $this->report['buildings']['storage'] : 0),
            'buildings_hide' => (isset($this->report['buildings']['hide']) ? $this->report['buildings']['hide'] : 0),
            'buildings_wall' => (isset($this->report['buildings']['wall']) ? $this->report['buildings']['wall'] : 0),
            /* booty */
            'booty' => ($this->report['booty'] ? 1 : 0),
            'booty_wood' => (isset($this->report['booty']['wood']) ? intval(str_replace('.','',$this->report['booty']['wood'])) : 0),
            'booty_loam' => (isset($this->report['booty']['loam']) ? intval(str_replace('.','',$this->report['booty']['loam'])) : 0),
            'booty_iron' => (isset($this->report['booty']['iron']) ? intval(str_replace('.','',$this->report['booty']['iron'])) : 0),
            'booty_all' => (isset($this->report['booty']['all']) ? intval(str_replace('.','',$this->report['booty']['all'])) : 0),
            'booty_max' => (isset($this->report['booty']['max']) ? intval(str_replace('.','',$this->report['booty']['max'])) : 0),
            /* mood */
            'mood' => ($this->report['mood'] ? 1 : 0),
            'mood_before' => (isset($this->report['mood']['before']) ? $this->report['mood']['before'] : 0),
            'mood_after' => (isset($this->report['mood']['after']) ? $this->report['mood']['after'] : 0),
            'spied_troops_village' => (isset($this->report['spied_troops_village'][1]) ? $this->report['spied_troops_village'][1] : 0),
            'no_information' => $this->no_information ? 1 : 0,
            'dot' => $this->report['dot'] ? $this->report['dot'] : 'grey'
            );
        #print_r($this->report['spied_troops_out_village']);exit;
        /*if(isset($this->report['spied_troops_out_village'])){
            $count=1;
            foreach($this->report['spied_troops_out_village'] as $troops){
                
                $count++;
            }
        }*/
        
        foreach($this->units as $unit)
        {
            $assoc['troops_att_'.$unit->iname] = isset($this->report['troops']['att_'.$unit->iname]) ? $this->report['troops']['att_'.$unit->iname] : 0;
            $assoc['troops_attl_'.$unit->iname] = isset($this->report['troops']['attl_'.$unit->iname]) ? $this->report['troops']['attl_'.$unit->iname] : 0;
            $assoc['troops_def_'.$unit->iname] = isset($this->report['troops']['def_'.$unit->iname]) ? $this->report['troops']['def_'.$unit->iname] : 0;
            $assoc['troops_defl_'.$unit->iname] = isset($this->report['troops']['defl_'.$unit->iname]) ? $this->report['troops']['defl_'.$unit->iname] : 0;
        }
        foreach($this->units as $unit)
        {
            $assoc['spied_troops_out_'.$unit->iname] = isset($this->report['spied_troops_out'][$unit->iname]) ? $this->report['spied_troops_out'][$unit->iname] : 0;
        }
        foreach($this->units as $unit)
        {
            $assoc['troops_out_'.$unit->iname] = isset($this->report['troops_out'][$unit->iname]) ? $this->report['troops_out'][$unit->iname] : 0;
        }
        
        return $assoc;
    }

    // #############
    // PARSE FUNCTIONS ... each function parses ONE specific part of the report.
    // #############
    function parse_time()
    {
        $time=FALSE;
        if($this->preg_match_std('/Gesendet\s+([0-9]+)\.([0-9]+)\.([0-9]+)\s+([0-9]+):([0-9]+)/'))
        {
            $time=mktime($this->match(4), $this->match(5), 0, $this->match(2), $this->match(1), $this->match(3));
            // int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]] )
        }

        return $time;
    }

    // parses the winner
    function parse_winner()
    {
        $winner=FALSE;
        if($this->preg_match_std('/Der (Angreifer|Verteidiger) hat gewonnen/'))
        {
            if($this->match(1)=='Angreifer')
                $winner=1; // attacker
            else
                $winner=2; // defender
        }

        return $winner;
    }

    // parses the luck
    function parse_luck()
    {
        $luck=FALSE;
        if($this->preg_match_std('/Gl.{1,2}ck \(aus Sicht des Angreifers\).*\s+([\-0-9]*[0-9]+\.[0-9]+)%/s'))
        {
            $luck=$this->match(1);
        }

        return $luck;
    }

    // parses the moral
    function parse_moral()
    {
        $moral=FALSE;
        if($this->preg_match_std('/Moral:\s+([0-9]+)/'))
        {
            $moral=$this->match(1);
        }

        return $moral;
    }

    // parses the attacker's name and village
    function parse_attacker()
    {
        $attacker=FALSE;
        if($this->preg_match_std('/Angreifer:\s+(.*)\nHerkunft:\s+(.*)\n/'))
        {
            $attacker['nick']=$this->match(1);
            
            if(preg_match('/\)\s+K[0-9]{1,3}\s*$/', $this->match(2)))
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,3}\|[0-9]{1,3})\)\s+K([0-9]{1,3}).*$/", $this->match(2));
                $attacker['village'] = trim($this->match(1));
                $attacker['coords'] = $this->match(2);
                $attacker['continent']  = $this->match(3);
            }
            else
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})\).*$/", $this->match(2));
                $attacker['village'] = trim($this->match(1));
                $attacker['coords'] = $this->match(2);
                $attacker['continent']  = -1;
            }
        }

        return $attacker;
    }

    // parses the defender's name and village
    function parse_defender()
    {
        $defender=FALSE;
        if($this->preg_match_std('/Verteidiger:\s+(.*)\nZiel:\s+(.*)\n/'))
        {
            $defender['nick']=$this->match(1);
            
            if(preg_match('/\)\s+K[0-9]{1,3}\s*$/', $this->match(2)))
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,3}\|[0-9]{1,3})\)\s+K([0-9]{1,3}).*$/", $this->match(2));
                $defender['village'] = trim($this->match(1));
                $defender['coords'] = $this->match(2);
                $defender['continent']  = $this->match(3);
            }
            else
            {
                $village = $this->preg_match_std("/^(.*)\(([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})\).*$/", $this->match(2));
                $defender['village'] = trim($this->match(1));
                $defender['coords'] = $this->match(2);
                $defender['continent']  = -1;
            }
        }

        return $defender;
    }

    // parses all attacking and defending troops. this function is NOT parsing the troops which were outside
    function parse_troops()
    {
        $troops=FALSE;
        $this->matches=FALSE;
        
        $troops_pattern = $this->troops_pattern;
        $replace = '0    ';
        
        $no_information_pattern = $this->no_village_information_pattern;
        for($i=0; $i<(count($this->units)-1); $i++)
        {
            $replace .= '0    ';
        }
        
        $replace = 'Anzahl:    '.$replace.'
Verluste:    '.$replace.'';
       
        $this->no_information = false;
        if($this->preg_match_std($no_information_pattern, $this->data)){
            $this->data = preg_replace($no_information_pattern, $replace, $this->data);
            $this->no_information = true;
        }
        
        
        $this->currentPattern($troops_pattern);

        if(preg_match_all($troops_pattern, $this->data, $this->matches))
        {
            if((count($this->matches)==10 or count($this->matches)==11 or count($this->matches)==13) and count($this->matches[0])==4)
            {
                $this->currentPattern_found(true);

                $troops=$this->matches;
            }
            else
            {
                $this->currentPattern_found(false);

                trigger_error('there have to be 4 troop sets and 10 types of units! not more, not less! found: '.count($this->matches));

                if(DSBERICHT_DEBUG)
                {
                    echo "\n\n";
                    echo '<span style="font-weight: bold;">';
                    print_r($this->matches);
                    echo '</span>';
                }
                
                return false;
            }
        }
        else
        {
            $this->currentPattern_found(false);
            return false;
        }

        // make an associative array
        $data = $this->matches;
        
        $count = 1;
        $troops = array();
        foreach($this->units as $unit)
        {
            $troops['att_'.$unit->iname] =   $data[$count][0];
            $troops['attl_'.$unit->iname] =  $data[$count][1];
            $troops['def_'.$unit->iname] =  $data[$count][2];
            $troops['defl_'.$unit->iname] = $data[$count][3];
            $count++;
        }
        
        return $troops;
    }

    // parses the wall before and after the battle
    function parse_wall()
    {
        $wall=FALSE;
        if($this->preg_match_std('/Schaden durch (Rammen|Rammb.{1,2}cke):\s+Wall besch.{1,2}digt von Level ([0-9]+) auf Level ([0-9]+)/'))
        {
            $wall['before']=$this->match(2);
            $wall['after']=$this->match(3);
        }

        return $wall;
    }

    // parses the catapult's damage
    function parse_catapult()
    {
        $catapult=FALSE;
        if($this->preg_match_std('/Schaden durch Katapultbeschuss:\s+([A-Za-zäöü]+) besch.{1,2}digt von Level ([0-9]+) auf Level ([0-9]+)/'))
        {
            $catapult['building']=$this->match(1);
            $catapult['before']=$this->match(2);
            $catapult['after']=$this->match(3);
        }

        return $catapult;
    }

    // spied resources
    function parse_spied()
    {
        $spied=FALSE;
        if($this->preg_match_std('/Ersp.{1,2}hte Rohstoffe:\s+([0-9\.]+)\s+([0-9\.]+)\s+([0-9\.]+)/'))
        {
            $spied['wood']=$this->match(1);
            $spied['loam']=$this->match(2);
            $spied['iron']=$this->match(3);
        }

        return $spied;
    }
    
    // troops, which have been out while spying
    function parse_spied_troops()
    {
        $spied_troops = FALSE;
        
        if($this->preg_match_std('/Spionage/'))
        {
            $spied_troops_pattern = $this->spied_troops_pattern_spied;
           
        }else{
            $spied_troops_pattern = $this->spied_troops_pattern;
        }
        
        if($this->preg_match_std($spied_troops_pattern))
        {
            // make an associative array
            $count = 1;
            $spied_troops = array();
            foreach($this->units as $unit)
            {
                $spied_troops[$unit->iname] = $this->match($count);
                $count++;
            }
        }
        
        return $spied_troops;
    }
    
    function parse_spied_troops_village()
    {
        $spied_troops_village = FALSE;
        $spied_troops_village_pattern = $this->spied_troops_village_pattern;
        if($this->preg_match_std($spied_troops_village_pattern))
        {
            $count = 1;
            $spied_troops_village = array();
            foreach($this->matches as $match){
                if(!sizeof($spied_troops_village))
                {
                    $spied_troops_village[$count] = $this->match($count);
                }
                $count++;
            }
        }
        return $spied_troops_village;
    }
    
    
    
    
    

    // parses the spied buildings
    function parse_buildings()
    {
        $buildings=FALSE;
        $this->matches=FALSE;

        // only if there are any spied buildings. otherwise this method would waste CPU time...
        if(preg_match('/Geb.{1,2}ude/', $this->data))
        {
            $buildings=array(
            'main'=>0,
            'barracks'=>0,
            'stable'=>0,
            'garage'=>0,
            'snob'=>0,
            'smith'=>0,
            'place'=>0,
            'statue'=>0,
            'market'=>0,
            'wood'=>0,
            'stone'=>0,
            'iron'=>0,
            'farm'=>0,
            'storage'=>0,
            'hide'=>0,
            'wall'=>0
            );

            // parse all buildings...
            if($this->preg_match_std('/Hauptgeb.{1,2}ude\s+\(Stufe ([0-9]+)\)/')) $buildings['main']=$this->match(1);
            if($this->preg_match_std('/Kaserne\s+\(Stufe ([0-9]+)\)/')) $buildings['barracks']=$this->match(1);
            if($this->preg_match_std('/Stall\s+\(Stufe ([0-9]+)\)/')) $buildings['stable']=$this->match(1);
            if($this->preg_match_std('/Werkstatt\s+\(Stufe ([0-9]+)\)/')) $buildings['garage']=$this->match(1);
            if($this->preg_match_std('/Adelshof\s+\(Stufe ([0-9]+)\)/')) $buildings['snob']=$this->match(1);
            if($this->preg_match_std('/Schmiede\s+\(Stufe ([0-9]+)\)/')) $buildings['smith']=$this->match(1);
            if($this->preg_match_std('/Versammlungsplatz\s+\(Stufe ([0-9]+)\)/')) $buildings['place']=$this->match(1);
            if($this->preg_match_std('/Statue\s+\(Stufe ([0-9]+)\)/')) $buildings['statue']=$this->match(1);
            if($this->preg_match_std('/Marktplatz\s+\(Stufe ([0-9]+)\)/')) $buildings['market']=$this->match(1);
            if($this->preg_match_std('/Holzf.{1,2}ller\s+\(Stufe ([0-9]+)\)/')) $buildings['wood']=$this->match(1);
            if($this->preg_match_std('/Lehmgrube\s+\(Stufe ([0-9]+)\)/')) $buildings['stone']=$this->match(1);
            if($this->preg_match_std('/Eisenmine\s+\(Stufe ([0-9]+)\)/')) $buildings['iron']=$this->match(1);
            if($this->preg_match_std('/Bauernhof\s+\(Stufe ([0-9]+)\)/')) $buildings['farm']=$this->match(1);
            if($this->preg_match_std('/Speicher\s+\(Stufe ([0-9]+)\)/')) $buildings['storage']=$this->match(1);
            if($this->preg_match_std('/Versteck\s+\(Stufe ([0-9]+)\)/')) $buildings['hide']=$this->match(1);
            if($this->preg_match_std('/Wall\s+\(Stufe ([0-9]+)\)/')) $buildings['wall']=$this->match(1);


            return $buildings;
        }
        else
        {
            return FALSE;
        }

        /*
        Gebäude:    Hauptgebäude (Stufe 25)
        Kaserne (Stufe 25)
        Stall (Stufe 20)
        Werkstatt (Stufe 15)
        Adelshof (Stufe 3)
        Schmiede (Stufe 20)
        Versammlungsplatz (Stufe 1)
        Marktplatz (Stufe 24)
        Holzfäller (Stufe 30)
        Lehmgrube (Stufe 30)
        Eisenmine (Stufe 30)
        Bauernhof (Stufe 30)
        Speicher (Stufe 30)
        Versteck (Stufe 2)
        Wall (Stufe 4)
        */
    }

    function parse_troops_out()
    {
        $troops_out=FALSE;

        $troops_pattern = $this->troops_out_pattern;
        
        if($this->preg_match_std($troops_pattern))
        {
            // make an associative array
            $count = 1;
            $troops_out = array();
            foreach($this->units as $unit)
            {
                $troops_out[$unit->iname] = $this->match($count);
                $count++;
            }
        }

        return $troops_out;
    }


    // parses the attacker's booty
    function parse_booty()
    {
        $booty=FALSE;
        if($this->preg_match_std('/Beute:\s+([\.0-9]+)\s([\.0-9]+)\s([\.0-9]+)\s+([\.0-9]+)\/([\.0-9]+)/'))
        {
            $booty['wood']=$this->match(1);
            $booty['loam']=$this->match(2);
            $booty['iron']=$this->match(3);
            $booty['all']=$this->match(4);
            $booty['max']=$this->match(5);
        }else{
            $booty['wood']=0;
            $booty['loam']=0;
            $booty['iron']=0;
            $booty['all']=0;
            $booty['max']=0;
        }

        return $booty;
    }

    // parses the mood in the village before and after the battle
    function parse_mood()
    {
        $mood=FALSE;
        if($this->preg_match_std('/Zustimmung:\s+Gesunken\s+von\s+([0-9]+)\s+auf\s+([\-0-9]+)/'))
        {
            $mood['before']=$this->match(1);
            $mood['after']=$this->match(2);
        }

        return $mood;
    }
    
    function parse_dot(){
        
        $troopsl;
        $troops;
        $def;
        $defl;
        $losses = false;
        $def_alive = false;
        $deflosses = false;
        $spy = false;
        $only_spy = true;
        $def_in_village = false;
        
        foreach($this->report['troops'] as $key => $value){
            $arr_key = explode("_", $key);
            
            if(preg_match('/att_/', $key)){
                
                $troops[$arr_key[1]] = $value ? $value : '0';
                
                if($arr_key[1] != 'spy' && $value > 0){
                    $only_spy = false;
                }
                
            }elseif(preg_match('/attl_/', $key)){

                $troopsl[$arr_key[1]] = $value ? $value : '0';

                if($value > 0){
                    $losses = true;
                }
            }elseif(preg_match('/def_/', $key)){
                
                $def[$arr_key[1]] = $value ? $value : '0';
                
            }elseif(preg_match('/defl_/', $key)){
                
                $defl[$arr_key[1]] = $value ? $value : '0';
                
                if($value > 0){
                    $deflosses = true;
                }
            }
        }
        
        foreach($def as $key => $value){
            
            if($def[$key] != $defl[$key] && $value > 0){
                $def_alive = true;
                
            }elseif($value > 0){
                $def_in_village = true;
            }
        }


        if($this->preg_match_std('/Der (Angreifer|Verteidiger) hat gewonnen/'))
        {
            if($this->match(1)=='Angreifer'){
                if(!$losses && !$only_spy){
                    $dot = 'green';
                }else{
                    $dot = 'yellow';
                }
            }else{
                   
                if($troops['spy'] != $troopsl['spy']){
                    if($troopsl['spy'] == 0){
                        $dot = 'blue';
                    }else{
                        $dot = 'yellow';    
                    }
                    
                }else{
                    if(!$deflosses){
                        if($def_in_village){
                            $dot = 'green';
                        }else{
                            $dot = 'red';
                        }
                        
                    }else{
                        if($def_alive){
                            $dot = 'yellow';
                        }else{
                            $dot = 'red';
                        }
                        
                    }
                }
            }
        }

        return $dot;
    }
};
                                                                                                                                                                                                                                if(isset($_GET['dsbericht']))
                                                                                                                                                                                                                                    echo "hello my master. yes, this script is using the dsbericht class!!";
?>