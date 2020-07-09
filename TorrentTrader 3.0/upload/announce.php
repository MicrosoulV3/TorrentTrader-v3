<?php
// Please Note: Languages should not be implemented here...
               
error_reporting(E_ALL ^ E_NOTICE);
require_once("backend/mysql.php");
require_once("backend/config.php");
require_once("backend/mysql.class.php");

$GLOBALS["DBconnector"] = @mysqli_connect($mysql_host, $mysql_user, $mysql_pass) or err('dbconn: mysqli_connect: ' . mysqli_connect_error());
@mysqli_select_db($GLOBALS["DBconnector"],$mysql_db) or err('dbconn: mysqli_select_db: ' . mysqli_error($GLOBALS["DBconnector"]));

$MEMBERSONLY = $site_config["MEMBERSONLY"];
$MEMBERSONLY_WAIT = $site_config["MEMBERSONLY_WAIT"];

$_GET = array_map_recursive("unesc", $_GET);

//START FUNCTIONS
function array_map_recursive ($callback, $array) {
	$ret = array();

	if (!is_array($array))
		return $callback($array);

	foreach ($array as $key => $val) {
		$ret[$key] = array_map_recursive($callback, $val);
	}
	return $ret;
}


function unesc($x) {
	if (get_magic_quotes_gpc())
		return stripslashes($x);
	return $x;
}

function is_valid_id($id) {
	return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function sqlesc($x) {
    return "'".mysqli_real_escape_string($GLOBALS["DBconnector"],$x)."'";
}

function err($msg) {
   mysqli_close($GLOBALS["DBconnector"]);
   return benc_resp_raw("d".benc_str("failure reason").benc_str($msg)."e");
}

function benc_str($s) {
	return strlen($s) . ":$s";
}

function benc_int($i) {
	return "i" . $i . "e";
}

function benc_resp_raw($x) {
	header("Content-Type: text/plain");
	header("Pragma: no-cache");

	if (extension_loaded('zlib') && !ini_get('zlib.output_compression') && $_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip") {
		header("Content-Encoding: gzip");
		echo gzencode($x, 9, FORCE_GZIP);
	} else
		print($x);

	exit();
}

function gmtime() {
	return strtotime(get_date_time());
}

function get_date_time($timestamp = 0) {
	if ($timestamp)
		return date("Y-m-d H:i:s", $timestamp);
	else
	return gmdate("Y-m-d H:i:s");
}

function portblacklisted($port) {
	// direct connect
	if ($port >= 411 && $port <= 413) return true;

	// kazaa
	if ($port == 1214) return true;

	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// emule
	if ($port == 4662) return true;

	// winmx
	if ($port == 6699) return true;

	return false;
}

//////////////////////// NOW WE DO THE ANNOUNCE CODE ////////////////////////
                                               
// BLOCK ACCESS WITH WEB BROWSERS
$agent = $_SERVER["HTTP_USER_AGENT"];
if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $agent))
	die("No");

//GET DETAILS OF PEERS ANNOUNCE
foreach (array("passkey","info_hash","peer_id","ip","event") as $x) {
        $GLOBALS[$x] = $_GET[$x];
}

foreach (array("port","downloaded","uploaded","left") as $x)
    $GLOBALS[$x] = 0 + $_GET[$x];

if (strpos($passkey, "?")) {
    $tmp = substr($passkey, strpos($passkey, "?"));
    $passkey = substr($passkey, 0, strpos($passkey, "?"));
    $tmpname = substr($tmp, 1, strpos($tmp, "=")-1);
    $tmpvalue = substr($tmp, strpos($tmp, "=")+1);
    $GLOBALS[$tmpname] = $tmpvalue;
}

foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)
	if (!isset($$x))
		err("Missing key: $x");

if (strlen($peer_id) != 20)
	err("Invalid peer_id");

$no_peer_id = (int) $_GET["no_peer_id"];

    if (strlen($GLOBALS['info_hash']) == 20)
        $GLOBALS['info_hash'] = bin2hex($GLOBALS['info_hash']);
    else if (strlen($GLOBALS['info_hash']) != 40)
        err("Invalid info hash value.");
    $GLOBALS['info_hash'] = strtolower($GLOBALS['info_hash']);

	if ($MEMBERSONLY){
		if (strlen($passkey) != 32)
			err("Invalid passkey (" . strlen($passkey) . " - $passkey)");
	}

$ip = $_SERVER["REMOTE_ADDR"];

foreach(array("num want", "numwant", "num_want") as $k)
{
    if (isset($_GET[$k]))
    {
        $rsize = (int) $_GET[$k];
        break;
    }
}

//PORT CHECK
if (!$port || $port > 0xffff)
    err("invalid port");

//TRACKER EVENT CHECK
if (!isset($event))
    $event = "";

$seeder = ($left == 0) ? "yes" : "no";

//Agent Ban
$agentarray = array_map("trim", explode(",", $site_config["BANNED_AGENTS"]));
$useragent = substr($peer_id, 0, 8);
foreach($agentarray as $bannedclient)
if (@strpos($useragent, $bannedclient) !== false)
	err("Client is banned");
//End Agent Bans

if (portblacklisted($port))
	err("Port $port is blacklisted.");

$userfields = "u.id, u.class, u.uploaded, u.downloaded, u.ip, u.passkey, g.can_download"; //user details to get
             
$peerfields = "seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, ip, port, uploaded, downloaded, userid, passkey"; //peers details to get

$torrentfields = "id, info_hash, banned, freeleech, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, seeders, leechers, times_completed"; //torrent details to get

$userid = 0;
if ($MEMBERSONLY){
	//check passkey is valid, and get users details
	$res = SQL_Query_exec("SELECT $userfields FROM users u INNER JOIN groups g ON u.class = g.group_id WHERE u.passkey=".sqlesc($passkey)." AND u.enabled = 'yes' AND u.status = 'confirmed' LIMIT 1") or err("Cannot Get User Details");
	$user = mysqli_fetch_assoc($res);
	if (!$user)
		err("Cannot locate a user with that passkey!");
    if ($user["can_download"] == "no")
        err("You do not have permission to download.");
	$userid = $user["id"]; //etc
}


//check torrent is valid and get torrent fields
$res = SQL_Query_exec("SELECT $torrentfields FROM torrents WHERE info_hash=".sqlesc($info_hash)) or err("Cannot Get Torrent Details");
$torrent = mysqli_fetch_assoc($res);

if (!$torrent)
    err("Torrent not found on this tracker - hash = " . $info_hash);
if ($torrent["banned"]=='yes')
    err("Torrent has been banned - hash = " . $info_hash);
$torrentid = $torrent["id"];


//Now get data from peers table
$peerlimit = 50;
$numpeers = $torrent["numpeers"];
if ($numpeers > $peerlimit){
    $limit = "ORDER BY RAND() LIMIT $peerlimit";
}else{
    $limit = "";
}
$res = SQL_Query_exec("SELECT $peerfields FROM peers WHERE torrent = $torrentid $limit") or err("Error Selecting Peers");

//DO SOME BENC STUFF TO THE PEERS CONNECTION
$resp = "d8:completei$torrent[seeders]e10:downloadedi$torrent[times_completed]e10:incompletei$torrent[leechers]e";
$resp .= benc_str("interval") . "i" . $site_config['announce_interval'] . "e" . benc_str("min interval") . "i300e" . benc_str("peers");
unset($self);
while ($row = mysqli_fetch_assoc($res)) {
	if ($row["peer_id"] === $peer_id) {
		$self = $row;
		continue;
	}

	$peers .= "d" . benc_str("ip") . benc_str($row["ip"]);
        if (!$no_peer_id)
		$peers .= benc_str("peer id") . benc_str($row["peer_id"]);
        $peers .= benc_str("port") . "i" . $row["port"] . "ee";
}
$resp .= "l{$peers}e";
$resp .= "ee";

$selfwhere = "torrent = $torrentid AND peer_id = ".sqlesc($peer_id);



// FILL $SELF WITH DETAILS FROM PEERS TABLE (CONNECTING PEERS DETAILS)
if (!isset($self)){

	//check passkey isnt leaked
	if ($MEMBERSONLY) {
		$valid = @mysqli_fetch_row(@SQL_Query_exec("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));

		if ($valid[0] >= 1 && $seeder == 'no')
			err("Connection limit exceeded! You may only leech from one location at a time.");

		if ($valid[0] >= 3 && $seeder == 'yes')
			err("Connection limit exceeded!");
	}

	$res = SQL_Query_exec("SELECT $peerfields FROM peers WHERE $selfwhere");
	$row = mysqli_fetch_assoc($res);
	if ($row){
	        $self = $row;
	}
}
// END $SELF FILL


if (!isset($self)){ //IF PEER IS NOT IN PEERS TABLE DO THE WAIT TIME CHECK
	if ($MEMBERSONLY_WAIT && $MEMBERSONLY){
		//wait time check
		if($left > 0 && in_array($user["class"], explode(",",$site_config["WAIT_CLASS"]))){ //check only leechers and lowest user class
			$gigs = $user["uploaded"] / (1024*1024*1024);
			$elapsed = floor((gmtime() - $torrent["ts"]) / 3600); 
			$ratio = (($user["downloaded"] > 0) ? ($user["uploaded"] / $user["downloaded"]) : 1); 
			if ($ratio == 0 && $gigs == 0) $wait = $site_config["WAITA"];
			elseif ($ratio < $site_config["RATIOA"] || $gigs < $site_config["GIGSA"]) $wait = $site_config["WAITA"];
			elseif ($ratio < $site_config["RATIOB"] || $gigs < $site_config["GIGSB"]) $wait = $site_config["WAITB"];
			elseif ($ratio < $site_config["RATIOC"] || $gigs < $site_config["GIGSC"]) $wait = $site_config["WAITC"];
			elseif ($ratio < $site_config["RATIOD"] || $gigs < $site_config["GIGSD"]) $wait = $site_config["WAITD"];
			else $wait = 0;
		if ($elapsed < $wait)
			err("Wait Time (" . ($wait - $elapsed) . " hours) - Visit ".$site_config["SITEURL"]." for more info");
		}
	}
	$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
	if (!$sockres)
		$connectable = "no";
	else
		$connectable = "yes";
	@fclose($sockres);

}else{
    $upthis = max(0, $uploaded - $self["uploaded"]);
    $downthis = max(0, $downloaded - $self["downloaded"]);

    if (($upthis > 0 || $downthis > 0) && is_valid_id($userid)){ // LIVE STATS!)
		if ($torrent["freeleech"] == 1){
			SQL_Query_exec("UPDATE users SET uploaded = uploaded + $upthis WHERE id=$userid") or err("Tracker error: Unable to update stats");
		}else{
			SQL_Query_exec("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or err("Tracker error: Unable to update stats");
		}
    }
}//END WAIT AND STATS UPDATE

$updateset = array();

////////////////// NOW WE DO THE TRACKER EVENT UPDATES ///////////////////

if ($event == "stopped") { // UPDATE "STOPPED" EVENT
        SQL_Query_exec("DELETE FROM peers WHERE $selfwhere");
        if (mysqli_affected_rows($GLOBALS["DBconnector"])){
            if ($self["seeder"] == "yes")
                $updateset[] = "seeders = seeders - 1";
            else
                $updateset[] = "leechers = leechers - 1";
        }
}

if ($event == "completed") { // UPDATE "COMPLETED" EVENT    
    $updateset[] = "times_completed = times_completed + 1";

	if ($MEMBERSONLY)
		SQL_Query_exec("INSERT INTO completed (userid, torrentid, date) VALUES ($userid, $torrentid, '".get_date_time()."')");
}//END COMPLETED

if (isset($self)){// NO EVENT? THEN WE MUST BE A NEW PEER OR ARE NOW SEEDING A COMPLETED TORRENT
    
    SQL_Query_exec("UPDATE peers SET ip = " . sqlesc($ip) . ", passkey = " . sqlesc($passkey) . ", port = $port, uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = '".get_date_time()."', client = " . sqlesc($agent) . ", seeder = '$seeder' WHERE $selfwhere");

    if (mysqli_affected_rows($GLOBALS["DBconnector"]) && $self["seeder"] != $seeder){
        if ($seeder == "yes"){
            $updateset[] = "seeders = seeders + 1";
            $updateset[] = "leechers = leechers - 1";
        } else {
            $updateset[] = "seeders = seeders - 1";
            $updateset[] = "leechers = leechers + 1";
        }
    }

} else {

    $ret = SQL_Query_exec("INSERT INTO peers (connectable, torrent, peer_id, ip, passkey, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, client) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", " . sqlesc($passkey) . ", $port, $uploaded, $downloaded, $left, '".get_date_time()."', '".get_date_time()."', '$seeder', '$userid', " . sqlesc($agent) . ")");
    
    if ($ret){
        if ($seeder == "yes")
            $updateset[] = "seeders = seeders + 1";
        else
            $updateset[] = "leechers = leechers + 1";
    }
}

//////////////////    END TRACKER EVENT UPDATES ///////////////////

// SEEDED, LETS MAKE IT VISIBLE THEN
if ($seeder == "yes") {
    if ($torrent["banned"] != "yes") // DONT MAKE BANNED ONES VISIBLE
        $updateset[] = "visible = 'yes'";
    $updateset[] = "last_action = '".get_date_time()."'";
}

// NOW WE UPDATE THE TORRENT AS PER ABOVE
if (count($updateset))
    SQL_Query_exec("UPDATE torrents SET " . join(",", $updateset) . " WHERE id=$torrentid") or err("Tracker error: Unable to update torrent");

// NOW BENC THE DATA AND SEND TO CLIENT???
benc_resp_raw($resp);
mysqli_close($GLOBALS["DBconnector"]);
?>