<?php
    /* AJAX-EXTENSION FOR DS OBST
     * 
     *
     * Adds an AJAX interface to OBST. It currently only supports parsing reports.
     * Author: Robert Nitsch
     * Last update: 13th June 2009
     */
 
    /*
     IF YOU WANT TO PARSE REPORTS USING AJAX REQUESTS, DO IT LIKE THIS:
 
         URL = ajax.php?action=parse_report
         POSTDATA:
            - report:   the report as it would have been copied by the user
            - user:     the username to use
            - pass:     the user's password given as MD5 hash
            - group:    the reportgroup's name or -1 if you want to select "no group"
            - world:    the world the report is associated with
 
     The response is given as XML like the following:
 
        <?xml version="1.0" encoding="utf-8"?>
        <response>
            <message>SOME RESPONSE MESSAGE HERE</message>
            <data>
                [<reportid>4</reportid>]
                [<error>1</error>]
            </data>
        </response>
 
     Explanation:
     - message:  	the actual response message
     - reportid: 	optional, given if and only if the parsing has been successful.
     - error:    	optional, indicates that something went wrong. The message then
       				is the error message.
 
     Normally reportid OR error is given, so you know if parsing has been a success or not.
    */
 
 
 
    // IMPORTANT:
    // This extension bypasses some user rights.
    // It only checks whether a user with the given login data exists and if he
    // is allowed to parse any reports at all. E.g. it does NOT check if the user
    // is allowed to parse reports associated with a specific world...
 
    error_reporting(E_ALL);
 
    define('XDB_INCCHECK',TRUE);
    define('INC_CHECK_DSBERICHT', TRUE);
    define('SSQL_INC_CHECK', TRUE);
    define('OBST_ROOT', '.');
    define('DEBUG_PARSER', FALSE);
 
    require OBST_ROOT.'/include/config.inc.php';
    require OBST_ROOT.'/include/class.simpleMySQL.php';
    require OBST_ROOT.'/include/class.dsBericht.php';
    require OBST_ROOT.'/include/models/class.BaseModel.php';
    require OBST_ROOT.'/include/models/class.ReportGroupsModel.php';
 
    // check for OBST being available
    if(!$obst['online'])
        error("This OBST installation is currently disabled. Reason: ".htmlspecialchars($obst['online']));
 
    // validate action
    if(empty($_GET['action']))
        error("GET[action] empty");
 
    // perform the given action
    if($_GET['action'] == 'parse_report') {
        requirePostValue('report');
        requirePostValue('user');
        requirePostValue('pass');
        requirePostValue('group');
        requirePostValue('world');
 
        $mysql=new simpleMySQL($mysql_user, $mysql_pass, $mysql_name, $mysql_host);
        if(!$mysql->connected())
        {
            error("MySQL connection could not be established. Error message: ".htmlspecialchars($mysql->lasterror));
        }
 
        // #############
        // VALIDATIONS
 
        // user
        $username = addslashes($_GET['user']);
        $res = $mysql->sql_query("SELECT
                                            id,
                                            name,
                                            pass,
                                            activated,
                                            admin,
                                            can_reports_parse,
                                            lastlogin
                                            FROM xdb_users
                                            WHERE name = '$username'
                                            LIMIT 1");
 
        if(!$res)
            _sqlError();
 
        if($mysql->sql_num_rows($res) == 0)
            error("The given username / password is wrong.");
 
        $userdata = $mysql->sql_fetch_assoc($res);
 
        // password check
        if($userdata['pass'] != $_GET['pass'])
            error("The given username / password is wrong.");
 
        // does the user have the right to parse reports anyway?
        if(intval($userdata['can_reports_parse']) != 1 && $userdata['admin'] != 1)
            error("You do not have the right to parse reports!");
 
        // does the group exist?
        if($_GET['group'] != "-1") {
            $model_group = new ReportGroupsModel($mysql);
            $group = $model_group->get("id", "", "name='".addslashes($_GET['group'])."'", "1");
 
            if($group === false)
            	_sqlError();
 
            if(count($group) == 0)
                error("The given group does not exist.");
 
            $group = $group[0];
            $groupid = $group['id'];
        }
        else {
            $groupid = -1;
        }
 
        // does the world exist?
        $world = intval($_GET['world']);
        if(array_search($world, $obst['worlds']) === false)
            error("The given world does not exist / is not supported by this OBST installation.");
 
        // ################
        // parse and save the report
 
        $parser = new dsBericht($obst_units[intval($world)]);
        if(!$parser->parse($_GET['report']))
        {
            error("The report could not be parsed.");
            return;
        }
 
 
 		if (DEBUG_PARSER)
 		{
 			error($parser->formatFields());
 			return;
 		}
 
 
        $max_id = $mysql->sql_result($mysql->sql_query("SELECT MAX(id) AS max_id FROM xdb_reports"), 0, 'max_id');
        if(empty($max_id))
            $max_id = 0;
 
        $data = serialize($parser->getReport());
        $data_hash = md5($data);
 
        $extra_columns = array(
                            'id' => ($max_id+1),
                            'user_id' => $userdata['id'],
                            'group_id' => $groupid,
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            'realtime' => time(),
                            'lastcomment' => 0,
                            'hash' => $data_hash,
                            'world' => $world
                            );
        $sql = $parser->buildSQL('xdb_reports', $extra_columns);
 
        if($mysql->sql_query($sql))
            sendAjaxResponse("The report has been parsed successfully.", "<reportid>".($max_id+1)."</reportid>");
        else
            _sqlError();
    }
    // here new actions may be implemented
    else {
        error("GET[action] invalid");
    }
 
    function requirePostValue($name, $emptyAllowed = false) {
        if(!isset($_GET[$name]))
            error("POST[$name] is not set");
        if(!$emptyAllowed && empty($_GET[$name]))
            error("POST[$name] empty");
    }
 
    function _sqlError() {
        global $mysql;
        error("SQL-Error!".(OBST_DEBUG ? " Error text: ".$mysql->lasterror : ""));
    }
 
    function error($msg) {
        sendAjaxResponse($msg, $data="<error>1</error>");
    }
 
    /**
     * Only use THIS function for sending data to the client!
     * 
     * @param msg     Message.
     * @param data     Optional response data.
     */
    function sendAjaxResponse($message, $data="") {
        header("Content-Type: text/xml; charset=utf-8");
 
        echo $_GET['callback'];
        echo '("';
        echo '<response>';
        echo "<message>$message</message>";
        echo "<data>$data</data>";
        echo '</response>';
        echo '")';
        exit();
    }
?>
