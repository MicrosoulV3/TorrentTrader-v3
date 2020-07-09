<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

stdhead("User CP");

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);

$r = @SQL_Query_exec("SELECT * FROM users WHERE id=$id");
$user = mysqli_fetch_array($r) or  show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);

//add invites check here

if ($CURUSER["view_users"] == "no" && $CURUSER["id"] != $id)
     show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
     
if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["edit_users"] == "no")
	show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);

//get all vars first

//$country
$res = SQL_Query_exec("SELECT name FROM countries WHERE id=$user[country] LIMIT 1");
if (mysqli_num_rows($res) == 1){
	$arr = mysqli_fetch_assoc($res);
	$country = "$arr[name]";
}

if (!$country) $country = "<b>Unknown</b>";

//$ratio
if ($user["downloaded"] > 0) {
    $ratio = $user["uploaded"] / $user["downloaded"];
}else{
	$ratio = "---";
}

$numtorrents = get_row_count("torrents", "WHERE owner = $id");
$numcomments = get_row_count("comments", "WHERE user = $id");
$numforumposts = get_row_count("forum_posts", "WHERE userid = $id");

$avatar = htmlspecialchars($user["avatar"]);
	if (!$avatar) {
		$avatar = $site_config["SITEURL"]."/images/default_avatar.png";
	}

function peerstable($res){
	$ret = "<table align='center' cellpadding=\"3\" cellspacing=\"0\" class=\"table_table\" width=\"100%\" border=\"1\"><tr><th class='table_head'>".T_("NAME")."</th><th class='table_head'>".T_("SIZE")."</th><th class='table_head'>" .T_("UPLOADED"). "</th>\n<th class='table_head'>" .T_("DOWNLOADED"). "</th><th class='table_head'>" .T_("RATIO"). "</th></tr>\n";

	while ($arr = mysqli_fetch_assoc($res)){
		$res2 = SQL_Query_exec("SELECT name,size FROM torrents WHERE id=$arr[torrent] ORDER BY name");
		$arr2 = mysqli_fetch_assoc($res2);
		if ($arr["downloaded"] > 0){
			$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
		}else{
			$ratio = "---";
		}
		$ret .= "<tr><td class='table_col1'><a href='torrents-details.php?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr2["name"]) . "</b></a></td><td align='center' class='table_col2'>" . mksize($arr2["size"]) . "</td><td align='center' class='table_col1'>" . mksize($arr["uploaded"]) . "</td><td align='center' class='table_col2'>" . mksize($arr["downloaded"]) . "</td><td align='center' class='table_col1'>$ratio</td></tr>\n";
  }
  $ret .= "</table>\n";
  return $ret;
}


//Layout
stdhead(sprintf(T_("USER_DETAILS_FOR"), $user["username"]));

begin_frame(sprintf(T_("USER_DETAILS_FOR"), $user["username"]));

if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {
	?>
	<table align="center" border="0" cellpadding="6" cellspacing="1" width="100%">
	<tr>
		<td width="50%"><b><?php echo T_("PROFILE"); ?></b></td>
		<td width="50%"><b><?php echo T_("ADDITIONAL_INFO"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
		<?php echo T_("USERNAME"); ?>: <?php echo htmlspecialchars($user["username"])?><br />
		<?php echo T_("USERCLASS"); ?>: <?php echo get_user_class_name($user["class"])?><br />
		<?php echo T_("TITLE"); ?>: <i><?php echo format_comment($user["title"])?></i><br />
		<?php echo T_("JOINED"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["added"]))?><br />
		<?php echo T_("LAST_VISIT"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["last_access"]))?><br />
		<?php echo T_("LAST_SEEN"); ?>: <?php echo htmlspecialchars($user["page"]);?><br />
		</td>

		<td align="left">
		<?php echo T_("AGE"); ?>: <?php echo htmlspecialchars($user["age"])?><br />
		<?php echo T_("CLIENT"); ?>: <?php echo htmlspecialchars($user["client"])?><br />
		<?php echo T_("COUNTRY"); ?>: <?php echo $country?><br />
		<?php echo T_("DONATED"); ?>  <?php echo $site_config['currency_symbol']; ?><?php echo number_format($user["donated"], 2); ?><br /> 
		<?php echo T_("WARNINGS"); ?>: <?php echo htmlspecialchars($user["warned"])?><br />
		<?php if ($CURUSER["edit_users"] == "yes"){ echo T_("ACCOUNT_PRIVACY_LVL").": <b>".T_($user["privacy"])."</b><br />"; }?>
		</td>
	</tr>

	<tr>
		<td width="50%"><b><?php echo T_("STATISTICS"); ?></b></td>
		<td width="50%"><b><?php echo T_("OTHER"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
		<?php echo T_("UPLOADED"); ?>: <?php echo mksize($user["uploaded"]); ?><br />
		<?php echo T_("DOWNLOADED"); ?>: <?php echo mksize($user["downloaded"]); ?><br />
		<?php echo T_("RATIO"); ?>: <?php echo $ratio; ?><br />
		<?php echo T_("AVG_DAILY_DL"); ?>: <?php echo mksize($user["downloaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("AVG_DAILY_UL"); ?>: <?php echo mksize($user["uploaded"] / (DateDiff($user["added"], time()) / 86400)); ?><br />
		<?php echo T_("TORRENTS_POSTED"); ?>: <?php echo number_format($numtorrents); ?><br />
		<?php echo T_("COMMENTS_POSTED"); ?>: <?php echo number_format($numcomments); ?><br />
        Forum Posts: <?php echo number_format($numforumposts); ?><br />   
		</td>

		<td align="left">
		<img src="<?php echo $avatar; ?>" alt="" title="<?php echo $user["username"]; ?>" height="80" width="80" /><br />
		<a href="mailbox.php?compose&amp;id=<?php echo $user["id"]?>"><?php echo T_("SEND_PM"); ?></a><br />
		<!-- <a href=#>View Forum Posts</a><br />
		<a href=#>View Comments</a><br /> -->
		<a href="report.php?user=<?php echo $user["id"]?>"><?php echo T_("REPORT_MEMBER"); ?></a><br />
		</td>
	</tr>
	<?php if ($CURUSER["edit_users"] == "yes") { ?>
	<tr>
		<td width="50%"><b><?php echo T_("STAFF_ONLY_INFO"); ?></b></td>
	</tr>

	<tr valign="top">
		<td align="left">
			<?php
				if ($user["invited_by"]) {
					$res = SQL_Query_exec("SELECT username FROM users WHERE id=$user[invited_by]");
					$row = mysqli_fetch_array($res);
					echo "<b>".T_("INVITED_BY").":</b> <a href=\"account-details.php?id=$user[invited_by]\">$row[username]</a><br />";
				}
				echo "<b>".T_("INVITES").":</b> ".number_format($user["invites"])."<br />";
				$invitees = array_reverse(explode(" ", $user["invitees"]));
				$rows = array();
				foreach ($invitees as $invitee) {
					$res = SQL_Query_exec("SELECT id, username FROM users WHERE id='$invitee' and status='confirmed'");
					if ($row = mysqli_fetch_array($res)) {
						$rows[] = "<a href=\"account-details.php?id=$row[id]\">$row[username]</a>";
					}
				}
				if ($rows)
					echo "<b>".T_("INVITEES").":</b> ".implode(", ", $rows)."<br />";
			?>
		</td>
	</tr>
	<?php
	}
	//team
	$res = SQL_Query_exec("SELECT name,image FROM teams WHERE id=$user[team] LIMIT 1");
	if (mysqli_num_rows($res) == 1) {
		$arr = mysqli_fetch_assoc($res);
		echo "<tr><td colspan='2' align='left'><b>Team Member Of:</b><br />";
		echo"<img src='".htmlspecialchars($arr["image"])."' alt='' /><br />".sqlesc($arr["name"])."<br /><br /><a href='teams-view.php'>[View ".T_("TEAMS")."]</a></td></tr>"; 
	}
	?>

	</table>

	<?php
}else{
	echo sprintf(T_("REPORT_MEMBER_MSG"), $user["id"]);
}

end_frame();

if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes") || ($CURUSER["id"] == $user["id"])) {
	begin_frame(T_("LOCAL_ACTIVITY"));

	$res = SQL_Query_exec("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = '$id' AND seeder = 'yes'");
	if (mysqli_num_rows($res) > 0)
	  $seeding = peerstable($res);

	$res = SQL_Query_exec("SELECT torrent, uploaded, downloaded FROM peers WHERE userid = '$id' AND seeder = 'no'");
	if (mysqli_num_rows($res) > 0)
	  $leeching = peerstable($res);

	if ($seeding)
		print("<b>" .T_("CURRENTLY_SEEDING"). ":</b><br />$seeding<br /><br />");

	if ($leeching)
		print("<b>" .T_("CURRENTLY_LEECHING"). ":</b><br />$leeching<br /><br />");

	if (!$leeching && !$seeding)
		print("<b>".T_("NO_ACTIVE_TRANSFERS")."</b><br />");

	end_frame();


	begin_frame(T_("UPLOADED_TORRENTS"));
	//page numbers
	$page = (int) $_GET["page"];
	$perpage = 25;
	$where = "";
	if ($CURUSER['control_panel'] != "yes")
		$where = "AND anon='no'";
	$res = SQL_Query_exec("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where");
	$row = mysqli_fetch_array($res);
	$count = $row[0];
	unset($where);

	$orderby = "ORDER BY id DESC";

	//get sql info
	if ($count) {
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "account-details.php?id=$id&amp;");
		$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit";
		$res = SQL_Query_exec($query);
	}else{
		unset($res);
	}

	if ($count) {
		print($pagertop);
		torrenttable($res);
		print($pagerbottom);
	}else {
		print("<b>".T_("UPLOADED_TORRENTS_ERROR")."</b><br />");
	}

	end_frame();
}



if($CURUSER["edit_users"]=="yes"){
	begin_frame(T_("STAFF_ONLY_INFO"));

	$avatar = htmlspecialchars($user["avatar"]);
	$signature = htmlspecialchars($user["signature"]);
	$uploaded = $user["uploaded"];
	$downloaded = $user["downloaded"];
	$enabled = $user["enabled"] == 'yes';
	$warned = $user["warned"] == 'yes';
	$forumbanned = $user["forumbanned"] == 'yes';
	$modcomment = htmlspecialchars($user["modcomment"]);

	print("<form method='post' action='admin-modtasks.php'>\n");
	print("<input type='hidden' name='action' value='edituser' />\n");
	print("<input type='hidden' name='userid' value='$id' />\n");
	print("<table border='0' cellspacing='0' cellpadding='3'>\n");
	print("<tr><td>".T_("TITLE").": </td><td align='left'><input type='text' size='67' name='title' value=\"$user[title]\" /></td></tr>\n");
	print("<tr><td>".T_("EMAIL")."</td><td align='left'><input type='text' size='67' name='email' value=\"$user[email]\" /></td></tr>\n");
	print("<tr><td>".T_("SIGNATURE").": </td><td align='left'><textarea cols='50' rows='10' name='signature'>".htmlspecialchars($user["signature"])."</textarea></td></tr>\n");
	print("<tr><td>".T_("UPLOADED").": </td><td align='left'><input type='text' size='30' name='uploaded' value=\"".mksize($user["uploaded"], 9)."\" /></td></tr>\n");
	print("<tr><td>".T_("DOWNLOADED").": </td><td align='left'><input type='text' size='30' name='downloaded' value=\"".mksize($user["downloaded"], 9)."\" /></td></tr>\n");
	print("<tr><td>".T_("AVATAR_URL")."</td><td align='left'><input type='text' size='67' name='avatar' value=\"$avatar\" /></td></tr>\n");
	print("<tr><td>".T_("IP_ADDRESS").": </td><td align='left'><input type='text' size='20' name='ip' value=\"$user[ip]\" /></td></tr>\n");
	print("<tr><td>".T_("INVITES").": </td><td align='left'><input type='text' size='4' name='invites' value='".$user["invites"]."' /></td></tr>\n");

	if ($CURUSER["class"] > $user["class"]){
		print("<tr><td>".T_("CLASS").": </td><td align='left'><select name='class'>\n");
		$maxclass = $CURUSER["class"];
		for ($i = 1; $i < $maxclass; ++$i)
		print("<option value='$i' " . ($user["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "\n");
		print("</select></td></tr>\n");
	}


	print("<tr><td>".T_("DONATED_US").": </td><td align='left'><input type='text' size='4' name='donated' value='$user[donated]' /></td></tr>\n");
	print("<tr><td>".T_("PASSWORD").": </td><td align='left'><input type='password' size='67' name='password' value=\"$user[password]\" /></td></tr>\n");
	print("<tr><td>".T_("CHANGE_PASS").": </td><td align='left'><input type='checkbox' name='chgpasswd' value='yes'/></td></tr>");
	print("<tr><td>".T_("MOD_COMMENT").": </td><td align='left'><textarea cols='50' rows='10' name='modcomment'>$modcomment</textarea></td></tr>\n");
	print("<tr><td>".T_("ACCOUNT_STATUS").": </td><td align='left'><input name='enabled' value='yes' type='radio' " . ($enabled ? " checked='checked'" : "") . " />Enabled <input name='enabled' value='no' type='radio' " . (!$enabled ? " checked='checked' " : "") . " />Disabled</td></tr>\n");
	print("<tr><td>".T_("WARNED").": </td><td align='left'><input name='warned' value='yes' type='radio' " . ($warned ? " checked='checked'" : "") . " />Yes <input name='warned' value='no' type='radio' " . (!$warned ? " checked='checked'" : "") . " />No</td></tr>\n");
	print("<tr><td>".T_("FORUM_BANNED").": </td><td align='left'><input name='forumbanned' value='yes' type='radio' " . ($forumbanned ? " checked='checked'" : "") . " />Yes <input name='forumbanned' value='no' type='radio' " . (!$forumbanned ? " checked='checked'" : "") . " />No</td></tr>\n");
	print("<tr><td>".T_("PASSKEY").": </td><td align='left'>$user[passkey]<br /><input name='resetpasskey' value='yes' type='checkbox' />".T_("RESET_PASSKEY")." (".T_("RESET_PASSKEY_MSG").")</td></tr>\n");
	print("<tr><td colspan='2' align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
	print("</table>\n");
	print("</form>\n");

	end_frame();
}

if($CURUSER["edit_users"]=="yes"){
	begin_frame(T_("BANS_WARNINGS"));

    print '<a name="warnings"></a>';
    
	$rqq = "SELECT * FROM warnings WHERE userid=$id ORDER BY id DESC";
	$res = SQL_Query_exec($rqq);

	if (mysqli_num_rows($res) > 0){

		?>
		<b>Warnings:</b><br />
		<table border="1" cellpadding="3" cellspacing="0" width="80%" align="center" class="table_table">
		<tr>
            <th class="table_head">Added</th>
		    <th class="table_head"><?php echo T_("EXPIRE"); ?></th>
		    <th class="table_head"><?php echo T_("REASON"); ?></th>
		    <th class="table_head"><?php echo T_("WARNED_BY"); ?></th>
		    <th class="table_head"><?php echo T_("TYPE"); ?></th>      
		</tr>
		<?php

		while ($arr = mysqli_fetch_assoc($res)){
			if ($arr["warnedby"] == 0) {
				$wusername = T_("SYSTEM");
			} else {
				$res2 = SQL_Query_exec("SELECT id,username FROM users WHERE id = ".$arr['warnedby']."");
				$arr2 = mysqli_fetch_assoc($res2);

				$wusername = htmlspecialchars($arr2["username"]);
			}
			$arr['added'] = utc_to_tz($arr['added']);
			$arr['expiry'] = utc_to_tz($arr['expiry']);

			$addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
			$expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
			print("<tr><td class='table_col1' align='center'>$addeddate</td><td class='table_col2' align='center'>$expirydate</td><td class='table_col1'>".format_comment($arr['reason'])."</td><td class='table_col2' align='center'><a href='account-details.php?id=".$arr2['id']."'>".$wusername."</a></td><td class='table_col1' align='center'>".$arr['type']."</td></tr>\n");
		 }

		echo "</table>\n";
	}else{
		echo T_("NO_WARNINGS");
	}


	print("<form method='post' action='admin-modtasks.php'>\n");
	print("<input type='hidden' name='action' value='addwarning' />\n");
	print("<input type='hidden' name='userid' value='$id' />\n");
	echo "<br /><br /><center><table border='0'><tr><td align='right'><b>".T_("REASON").":</b> </td><td align='left'><textarea cols='40' rows='5' name='reason'></textarea></td></tr>";
	echo "<tr><td align='right'><b>".T_("EXPIRE").":</b> </td><td align='left'><input type='text' size='4' name='expiry' />(days)</td></tr>";
	echo "<tr><td align='right'><b>".T_("TYPE").":</b> </td><td align='left'><input type='text' size='10' name='type' /></td></tr>";
	echo "<tr><td colspan='2' align='center'><input type='submit' value='".T_("ADD_WARNING")."' /></td></tr></table></center></form>";

	if($CURUSER["delete_users"] == "yes"){
		print("<hr /><center><form method='post' action='admin-modtasks.php'>\n");
		print("<input type='hidden' name='action' value='deleteaccount' />\n");
		print("<input type='hidden' name='userid' value='$id' />\n");
		print("<input type='hidden' name='username' value='".$user["username"]."' />\n");
		echo "<b>".T_("REASON").":</b><input type='text' size='30' name='delreason' />";
		echo "&nbsp;<input type='submit' value='".T_("DELETE_ACCOUNT")."' /></form></center>";
	}

	end_frame();
}

stdfoot();

?>