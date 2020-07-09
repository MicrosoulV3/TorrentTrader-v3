<?php
require_once("backend/functions.php");
dbconn();

if ($site_config['SHOUTBOX']){

//DELETE MESSAGES
if (isset($_GET['del'])){

	if (is_numeric($_GET['del'])){
		$query = "SELECT * FROM shoutbox WHERE msgid=".$_GET['del'] ;
		$result = SQL_Query_exec($query);
	}else{
		echo "invalid msg id STOP TRYING TO INJECT SQL";
		exit;
	}

	$row = mysqli_fetch_row($result);
		
	if ($row && ($CURUSER["edit_users"]=="yes" || $CURUSER['username'] == $row[1])) {
		$query = "DELETE FROM shoutbox WHERE msgid=".$_GET['del'] ;
		write_log("<b><font color='orange'>Shout Deleted: </font> Deleted by   ".$CURUSER['username']."</b>");
		SQL_Query_exec($query);	
	}
}

//INSERT MESSAGE
if (!empty($_POST['message']) && $CURUSER) {	
	$_POST['message'] = sqlesc($_POST['message']);
	$query = "SELECT COUNT(*) FROM shoutbox WHERE message=".$_POST['message']." AND user='".$CURUSER['username']."' AND UNIX_TIMESTAMP('".get_date_time()."')-UNIX_TIMESTAMP(date) < 30";
	$result = SQL_Query_exec($query);
	$row = mysqli_fetch_row($result);

	if ($row[0] == '0') {
		$query = "INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (NULL, '".$CURUSER['username']."', ".$_POST['message'].", '".get_date_time()."', '".$CURUSER['id']."')";
		SQL_Query_exec($query);
	}
}

//GET CURRENT USERS THEME AND LANGUAGE
if ($CURUSER){
	$ss_a = @mysqli_fetch_assoc(@SQL_Query_exec("select uri from stylesheets where id=" . $CURUSER["stylesheet"]));
	if ($ss_a)
		$THEME = $ss_a["uri"];
}else{//not logged in so get default theme/language
	$ss_a = mysqli_fetch_assoc(SQL_Query_exec("select uri from stylesheets where id='" . $site_config['default_theme'] . "'"));
	if ($ss_a)
		$THEME = $ss_a["uri"];
}

if(!isset($_GET['history'])){ 
?>
<html>
<head>
<title><?php echo $site_config['SITENAME'] . T_("SHOUTBOX"); ?></title>
<?php /* If you do change the refresh interval, you should also change index.php printf(T_("SHOUTBOX_REFRESH"), 5) the 5 is in minutes */ ?>
<meta http-equiv="refresh" content="300" />
<link rel="stylesheet" type="text/css" href="<?php echo $site_config['SITEURL']?>/themes/<?php echo $THEME; ?>/theme.css" />
<script type="text/javascript" src="<?php echo $site_config['SITEURL']; ?>/backend/java_klappe.js"></script>
</head>
<body class="shoutbox_body">
<?php
	echo '<div class="shoutbox_contain"><table border="0" style="width: 99%; table-layout:fixed">';
}else{
    
    if ($site_config["MEMBERSONLY"]) {
        loggedinonly();
    }
    
	stdhead();
	begin_frame(T_("SHOUTBOX_HISTORY"));
	echo '<div class="shoutbox_history">';

	$query = 'SELECT COUNT(*) FROM shoutbox';
	$result = SQL_Query_exec($query);
	$row = mysqli_fetch_row($result);
	echo '<div align="center">Pages: ';
	$pages = round($row[0] / 100) + 1;
	$i = 1;
	while ($pages > 0){
		echo "<a href='".$site_config['SITEURL']."/shoutbox.php?history=1&amp;page=".$i."'>[".$i."]</a>&nbsp;";
		$i++;
		$pages--;
	}

	echo '</div><br /><table border="0" style="width: 99%; table-layout:fixed">';
}

if (isset($_GET['history'])) {
	if (isset($_GET['page'])) {
		if($_GET['page'] > '1') {
			$lowerlimit = $_GET['page'] * 100 - 100;
			$upperlimit = $_GET['page'] * 100;
		}else{
			$lowerlimit = 0;
			$upperlimit = 100;
		}
	}else{
		$lowerlimit = 0;
		$upperlimit = 100;
	}	
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT '.$lowerlimit.','.$upperlimit;
}else{
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT 20';
}


$result = SQL_Query_exec($query);
$alt = false;

while ($row = mysqli_fetch_assoc($result)) {
	if ($alt){	
		echo '<tr class="shoutbox_noalt">';
		$alt = false;
	}else{
		echo '<tr class="shoutbox_alt">';
		$alt = true;
	}

	echo '<td style="font-size: 9px; width: 118px;">';
	echo "<div align='left' style='float: left'>";

	echo date('jS M, g:ia', utc_to_tz_time($row['date']));
	

	echo "</div>";

	if ( ($CURUSER["edit_users"]=="yes") || ($CURUSER['username'] == $row['user']) ){
		echo "<div align='right' style='float: right'><a href='".$site_config['SITEURL']."/shoutbox.php?del=".$row['msgid']."' style='font-size: 8px'>[D]</a></div>";
	}

	echo	'</td><td style="font-size: 12px; padding-left: 5px"><a href="'.$site_config['SITEURL'].'/account-details.php?id='.$row['userid'].'" target="_parent"><b>'.$row['user'].':</b></a>&nbsp;&nbsp;'.nl2br(format_comment($row['message']));
	echo	'</td></tr>';
}
?>

</table>
</div>
<br />

<?php

//if the user is logged in, show the shoutbox, if not, dont.
if(!isset($_GET['history'])) {
	if (isset($_COOKIE["pass"])){
		echo "<form name='shoutboxform' action='shoutbox.php' method='post'>";
		echo "<center><table width='100%' border='0' cellpadding='1' cellspacing='1'>";
		echo "<tr class='shoutbox_messageboxback'>";
		echo "<td width='75%' align='center'>";
		echo "<input type='text' name='message' class='shoutbox_msgbox' />";
		echo "</td>";
		echo "<td>";
		echo "<input type='submit' name='submit' value='".T_("SHOUT")."' class='shoutbox_shoutbtn' />";
		echo "</td>";
		echo "<td>";
        echo '<a href="javascript:PopMoreSmiles(\'shoutboxform\', \'message\');"><small>'.T_("MORE_SMILIES").'</small></a>';
        echo ' <small>-</small> <a href="javascript:PopMoreTags();"><small>'.T_("TAGS").'</small></a>';
		echo "<br />";
		echo "<a href='shoutbox.php'><small>".T_("REFRESH")."</small></a>";              
		echo " <small>-</small> <a href='".$site_config['SITEURL']."/shoutbox.php?history=1' target='_blank'><small>".T_("HISTORY")."</small></a>";
		echo "</td>";
		echo "</tr>";
		echo "</table></center>";
		echo "</form>";
	}else{
		echo "<br /><div class='shoutbox_error'>".T_("SHOUTBOX_MUST_LOGIN")."</div>";
	}
}

if(!isset($_GET['history'])){ 
	echo "</body></html>";
}else{
	end_frame();
	stdfoot();
}


}//END IF $SHOUTBOX
else{
	echo T_("SHOUTBOX_DISABLED");
}
?>
