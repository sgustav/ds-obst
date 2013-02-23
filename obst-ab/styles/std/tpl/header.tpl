<?xml encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="http://www.die-staemme.de/favicon.ico" />
	<title>OBST - {$title}</title>
	<meta name="author" content="Robert 'bmaker' Nitsch" />
	<link rel="stylesheet" type="text/css" href="{$obst_root}/styles/{$obst.style}/css.css" />
</head>

<body>
<script language="javascript" type="text/javascript" src="{$obst_root}/general.js"></script>

{if !isset($nonavi)}
<div align="center" id="ds_navi">
    <span>
        Hallo {$obst_user->getVal('name')}! |
    	<a href="index.php">Start</a> -
    	<a href="index.php?page=reports">Berichte</a> -
        <a href="index.php?page=user&amp;action=options">Profil</a> -
        <a href="index.php?page=overview&amp;action=credits">Credits</a> -
    	<a href="index.php?page=user&amp;action=logout">Logout</a>
    </span>
</div>
{/if}


<div class="dsstyle_body">

<h1><a href="index.php" target="_self">OBST [{$obst.name}]</a> - {$title}</h1>
<!--<h2 style="color: #FF2121; margin-top: 0px;">Diese Seite befindet sich noch im Test! Bitte beachtet das!</h2>-->
<hr />
<br />

<div>