<?php


error_reporting(E_ALL ^ E_NOTICE);

// Prefer unescaped. Data will be escaped as needed.
if (ini_get("magic_quotes_gpc")) {
	$_POST = array_map_recursive("unesc", $_POST);
	$_GET = array_map_recursive("unesc", $_GET);
	$_REQUEST = array_map_recursive("unesc", $_REQUEST);
	$_COOKIE = array_map_recursive("unesc", $_COOKIE);
}

if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("Europe/London"); // Do NOT change this. All times are converted to user's chosen timezone.

/// each() replacement for php 7+. Change all instances of each() to thisEach() in all TT files. each() deprecated as of 7.2
function thisEach(&$arr) {
    $key = key($arr);
    $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
    next($arr);
    return $result;
}
///end each() replacement

///mysql_result replacement for php 7+. Change all mysql_result to mysqli_result in all TT files. mysql_result deprercated/removed, so emulate it. Should switch to data_seek 
function mysqli_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}
///end mysql_result replacement

define("BASEPATH", str_replace("backend", "", dirname(__FILE__)));
$BASEPATH = BASEPATH;
define("BACKEND", dirname(__FILE__));
$BACKEND = BACKEND;

require_once(BACKEND."/mysql.php"); //Get MYSQL Connection Info
require_once(BACKEND."/config.php");  //Get Site Settings and Vars ($site_config)