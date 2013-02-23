<?php
// a class for simple use of MySQL
// copyright by Robert Nitsch, 2006
// www.robertnitsch.de

// version 1.1.0.0

if(!defined('SSQL_INC_CHECK')) die('access denied!');

class simpleMySQL {

    private $connection;
    private $querycount;
    private $affectedrows;
    public $lasterror;
    public $lastquery;
    
    // constructor
    public function simpleMySQL($db_user, $db_pass, $db_name, $db_host='localhost')
    {
        $this->querycount=0;
        $this->affectedrows=0;
        $this->lasterror='';
        $this->lastquery='';
        
        // connect to mysql database
        if($this->connection = @mysql_connect($db_host, $db_user, $db_pass))
        {
            @mysql_select_db($db_name, $this->connection);
            return TRUE;
        }
        else
        {
            $this->saveError('Verbindung zur MySQL-Datenbank fehlgeschlagen: '.mysql_error());
            return FALSE;
        }
    }
    
    public function connected()
    {
        if($this->connection != false)
            return true;
        else
            return false;
    }
    
    
    public function sql_query($query)
    {
        $result=FALSE;
        
        $this->lastquery = $query;
        
        if($result=@mysql_query($query,$this->connection))
        {
            $this->querycount++;
            $this->affectedrows += mysql_affected_rows($this->connection);
        
            return $result;
        }
        else
        {
            // save the error message
            $this->saveError();
            return FALSE;
        }
    }
    
    public function sql_result($queryid, $row, $column)
    {
        $return=FALSE;
        if($return=mysql_result($queryid, $row, $column))
        {
            return $return;
        }
        
        $this->saveError();
        return FALSE;
    }
    
    public function sql_num_rows($queryid)
    {
        return mysql_num_rows($queryid);
    }
    
    public function sql_fetch_assoc($queryid)
    {
        return mysql_fetch_assoc($queryid);
    }
    
    private function saveError($msg='')
    {
        if(empty($msg))
        {
            $this->lasterror=mysql_error($this->connection);
        }
        else
        {
            $this->lasterror=$msg;
        }
    }

};
?>