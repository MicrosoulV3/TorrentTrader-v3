<?php
error_reporting(E_ALL ^ E_NOTICE);

if ($_GET["phpinfo"] == 1){
	echo "<br /><center><a href='check.php'>Back To Check</a></center><br /><br />";
	phpinfo();
	die();
}

function get_php_setting($val) {
	$r =(ini_get($val) == '1' ? 1 : 0);
	return $r ? 'ON' : 'OFF';
}

function writableCell( $folder, $relative=1, $text='' ) {
	$writeable = '<b><font color="green">Writeable</font></b>';
	$unwriteable = '<b><font color="red">Unwriteable</font></b>';

	echo '<tr>';
	echo '<td>' . $folder . '</td>';
	echo '<td align="right">';
	if ( $relative ) {
		echo is_writable( "./$folder" ) ? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" ) ? $writeable : $unwriteable;
	}
    echo '</td>';
	echo '</tr>';
}


view();


function view() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TorrentTrader Check</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
td { vertical-align: top; }
</style>
</head>
<body>
<center>
<br /><b>TorrentTrader v3.0 Config Check<br /> - TorrentialStorm/MicrosoulV3<br /><br /></b>

<input type="button" class="button" value="Check Again" onclick="window.location=window.location" /><br /><br />

<a href="check.php?phpinfo=1">PHPInfo</a><br /><br />
<a href='index.php'>Return to your homepage</a></center><br />
<b>Required Settings Check:</b><br />
<p>If any of these items are highlighted in red then please take actions to correct them. <br />
Failure to do so could lead to your installation not functioning correctly.</p>
<br />
This system check is designed for unix based servers, windows based servers may not give desired results<br />
<br />
<br />

<table cellpadding="3" cellspacing="1" style="border-collapse: collapse" border="1">
<tr>
	<td>PHP version >= 7.2.0</td>
	<td>
	<?php
		echo phpversion() < '7.2' ? '<b><font color="red">No</font> 7.2 or above required</b>' : '<b><font color="green">Yes</font></b>';
		echo " - Your PHP version is ".phpversion();
	?>
	</td>
</tr>

<tr>
	<td>&nbsp; - zlib compression support</td>
	<td><?php echo extension_loaded('zlib') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - XML support</td>
	<td><?php echo extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - MySQLi support</td>
	<td><?php echo function_exists( 'mysqli_connect' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - curl support (Not required but external torrents may scrape faster)</td>
	<td><?php echo function_exists( 'curl_init' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>
<tr>
	<td>&nbsp; - openSSL (for the torrent encryption mod)</td>
	<td><?php echo extension_loaded( 'openssl' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>
<tr>
	<td>&nbsp; - gmp support (Required for IPv6)</td>
	<td><?php echo extension_loaded( 'gmp' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - bcmath support (Required for IPv6)</td>
	<td><?php echo extension_loaded( 'bcmath' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - hash_hmac support (Recommended - For better password encryption)</td>
	<td><?php echo function_exists( 'hash_hmac' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>&nbsp; - suhosin extension (Optional)</td>
	<td><?php echo extension_loaded( 'suhosin' ) ? '<b><font color="green">Available</font></b><br /><br />Add to your php.ini (otherwise you may have issues):<br />suhosin.get.disallow_nul = Off<br />suhosin.request.disallow_nul = Off' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr>

<tr>
	<td>backend/config.php</td>
	<td>
	<?php
	if (@file_exists('backend/config.php') && @is_writable( 'backend/config.php' )){
		echo '<b><font color="red">Writeable</font></b><br />Warning: leaving backend/config.php writeable is a security risk';
	} else {
		echo '<b><font color="green">Unwriteable</font></b>';
	}
	?>
	</td>
</tr>

<tr>
	<td>Document Root<br /><i><font size="1">(Use this for your PATHS in config.php)</font></i></td>
	<td><?php echo str_replace('\\', '/', getcwd()) ?></td>
</tr>

</table>


<br />
<p>These settings are recommended for PHP in order to ensure full compatibility with TorrentTrader!.
However, TorrentTrader! will still operate if your settings do not quite match the recommended.</p>

<table cellpadding="3" cellspacing="1" style="border-collapse: collapse" border="1">
<tr><td width="500px">Directive</td><td>Recommended</td><td>Actual</td></tr>

<?php
$php_recommended_settings = array(array ('Safe Mode','safe_mode','OFF'),
array ('Display Errors (Can be off, but does make debugging difficult.)','display_errors','ON'),
array ('File Uploads','file_uploads','ON'),
array ('Magic Quotes Runtime','magic_quotes_runtime','OFF'),
array ('Register Globals','register_globals','OFF'),
array ('Output Buffering','output_buffering','OFF'),
array ('Session auto start','session.auto_start','OFF'),
array ('allow_url_fopen (Required for external torrents)', 'allow_url_fopen', 'ON')
);

foreach ($php_recommended_settings as $phprec) {
	?>
	<tr>
	<td><?php echo $phprec[0]; ?>:</td>
	<td><?php echo $phprec[2]; ?>:</td>
	<td><b>
	<?php
	if ( get_php_setting($phprec[1]) == $phprec[2] ) {
	?>
		<font color="green">
	<?php
	} else {
	?>
		<font color="red">
	<?php
	}
	echo get_php_setting($phprec[1]);
?>
</font></b>
</td></tr>
<?php
}
?>
</table>

<br /><b>Directory and File Permissions Check:</b><br />
<p>In order for TorrentTrader! to function correctly it needs to be able to access or write to certain files or directories.<br />
If you see "Unwriteable" you need to change the permissions on the file or directory to 777 (directories) or 666 (files) so that TorrentTrader to write to it.
<br />The censor.txt should be chmodded to <b>600</b>.
</p>
<br />

<table cellpadding="3" cellspacing="1" style='border-collapse: collapse' border="1" >
<?php
writableCell( 'backups' );
writableCell( 'uploads' );
writableCell( 'uploads/images' );
writableCell( 'cache' );
writableCell( 'cache/get_row_count' );
writableCell( 'cache/queries' );
writableCell( 'cache/diskcache' );
writableCell( 'import' );
writableCell( 'censor.txt', 1 );  
?>
</table>
<br />
<?php
require_once("backend/mysql.php");
echo "<b>Table Status Check:</b><br /><br />";
	$link = mysqli_connect($mysql_host, $mysql_user, $mysql_pass);
	if (!$link)
	printf("<font color='#ff0000'><b>Failed to connect to database:</b></font> (%d) %s<br />", mysqli_errno($link), mysqli_error($link));
else {
	if (!mysqli_select_db($link, $mysql_db))
		printf("<font color='#ff0000'><b>Failed to select database:</b></font> (%d) %s<br />", mysqli_errno($link), mysqli_error($link));
	else {
		$r = mysqli_query($link, "SHOW TABLES");
		if (!$r)
			printf("<font color='#ff0000'><b>Failed to list tables:</b></font> (%d) %s<br />", mysqli_errno($link), mysqli_error($link));
		else {
			$tables = array();
			while($rr=mysqli_fetch_row($r))
			$tables[] = $rr[0];
			$arr[] = "announce";
			$arr[] = "bans";
			$arr[] = "blocks";
			$arr[] = "categories";
			$arr[] = "censor";
			$arr[] = "comments";
			$arr[] = "completed";
			$arr[] = "countries";
			$arr[] = "email_bans";
			$arr[] = "faq";
			$arr[] = "groups";
			$arr[] = "guests";
			$arr[] = "languages";
			$arr[] = "log";
			$arr[] = "messages";
			$arr[] = "news";
			$arr[] = "peers";
			$arr[] = "pollanswers";
			$arr[] = "polls";
			$arr[] = "ratings";
			$arr[] = "reports";
			$arr[] = "rules";
			$arr[] = "shoutbox";
			$arr[] = "stylesheets";
			$arr[] = "tasks";
			$arr[] = "teams";
			$arr[] = "torrentlang";
			$arr[] = "torrents";
			$arr[] = "users";
			$arr[] = "warnings";
            $arr[] = "forumcats";
            $arr[] = "forum_topics";
            $arr[] = "forum_posts";
            $arr[] = "forum_forums";
            $arr[] = "forum_readposts";
            $arr[] = "sqlerr";  

			echo "<table cellpadding='3' cellspacing='1' style='border-collapse: collapse' border='1'>";
			echo "<tr><th>Table</th><th>Status</th></tr>";
			foreach ($arr as $t)
				if (!in_array($t, $tables))
					echo "<tr><td>$t</td><td align='right'><font color='#ff0000'><b>MISSING</b></font></td></tr>";
				else
					echo "<tr><td>$t</td><td align='right'><font color='green'><b>OK</b></font></td></tr>";
				echo "</table>";

			require("backend/config.php");
			echo "<br /><br /><b>Default Theme:</b> ";
			if (!is_numeric($site_config["default_theme"]))
				echo "<font color='#ff0000'><b>Invalid.</b></font> (Not a number)";
			else {
				$res = mysqli_query($link,"SELECT uri FROM stylesheets WHERE id=$site_config[default_theme]");
				if ($row = mysqli_fetch_row($res)) {
					if (file_exists("themes/$row[0]/header.php"))
						echo "<font color='green'><b>Valid.</b></font> (ID: $site_config[default_theme], Path: themes/$row[0]/)";
					else
						echo "<font color='#ff0000'><b>Invalid.</b></font> (No header.php found)";
				} else
					echo "<font color='#ff0000'><b>Invalid.</b></font> (No theme found with ID $site_config[default_theme])";
		}

		echo "<br /><b>Default Language:</b> ";
		if (!is_numeric($site_config["default_language"]))
			echo "<font color='#ff0000'><b>Invalid.</b></font> (Not a number)";
		else {
			$res = mysqli_query($link,"SELECT uri FROM languages WHERE id=$site_config[default_language]");
			if ($row = mysqli_fetch_row($res)) {
				if (file_exists("languages/$row[0]"))
					echo "<font color='green'><b>Valid.</b></font> (ID: $site_config[default_language], Path: languages/$row[0])";
				else
					echo "<font color='#ff0000'><b>Invalid.</b></font> (File languages/$row[0] missing)";
			} else
				echo "<font color='#ff0000'><b>Invalid.</b></font> (No language found with ID $site_config[default_language])";
			}
		}
	}
}
mysqli_free_result($res); ///not sure if this is really necessary, but whatever. Here it is.
mysqli_close($link);
?>
</body>
</html>
<?php
}//end func

?>