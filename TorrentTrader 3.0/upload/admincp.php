<?php
require_once("backend/functions.php");
require_once("backend/bbcode.php");
dbconn();
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
     show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

 $action = $_REQUEST["action"];
 $do = $_REQUEST["do"];
 
function navmenu(){
global $site_config;

//Get Last Cleanup
$res = SQL_Query_exec("SELECT last_time FROM tasks WHERE task = 'cleanup'");
$row = mysqli_fetch_row($res);
if (!$row){
		$lastclean="never done...";
}else{
	$row[0]=gmtime()-$row[0]; $days=intval($row[0] / 86400);$row[0]-=$days*86400;
	$hours=intval($row[0] / 3600); $row[0]-=$hours*3600; $mins=intval($row[0] / 60);
	$secs=$row[0]-($mins*60);
	$lastclean = "$days days, $hours hrs, $mins minutes, $secs seconds ago.";
}

	begin_frame(T_("MENU"));
	echo "<center>Last cleanup performed: ".$lastclean." [<a href='admincp.php?action=forceclean'>".T_("FORCE_CLEAN")."</a>]</center><br /><br />";                                      
//	if ($site_config["ttversion"] != "2-svn") {  
//        $file = @file_get_contents('https://www.torrenttrader.org/tt2version.php');
//        if ($site_config['ttversion'] >= $file){
//			echo "<br /><center><b>".T_("YOU_HAVE_LATEST_VER_INSTALLED")." v$site_config[ttversion]</b></center>";
//		}else{
//			echo "<br /><center><b><font class='error'>".T_("NEW_VERSION_OF_TT_NOW_AVAIL").": v".$file." you have v".$site_config['ttversion']."<br /> Please visit <a href=http://www.torrenttrader.org>TorrentTrader.org</a> to upgrade.</font></b></center>";
//		}
//	}

	$res = SQL_Query_exec("SELECT VERSION() AS mysqli_version");
    $row = mysqli_fetch_assoc($res);
    $mysqlver = $row['mysqli_version']; 
	function apache_version()
    {
$ver = explode(" ",$_SERVER["SERVER_SOFTWARE"],3);
return ($ver[0] . " " . $ver[1]);
}
	$pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
	echo "<center><b>".T_("USERS_AWAITING_VALIDATION").":</b> <a href='admincp.php?action=confirmreg'>($pending)</a></center><br />";
	echo "<center>".T_("VERSION_MYSQL").": <b>" . $mysqlver . "</b><br />".T_("VERSION_PHP").": <b>" . phpversion() . "</b><br />".T_("VERSION_APACHE").": <b>" . apache_version() . "</b></center><hr/>";
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
    <td align="center"><a href="admincp.php?action=usersearch"><img src="images/admin/user_search.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("ADVANCED_USER_SEARCH"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=avatars"><img src="images/admin/avatar_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("AVATAR_LOG"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=backups"><img src="images/admin/db_backup.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BACKUPS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=ipbans"><img src="images/admin/ip_block.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_IPS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=bannedtorrents"><img src="images/admin/banned_torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BANNED_TORRENTS"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=blocks&amp;do=view"><img src="images/admin/blocks.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("BLOCKS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=cheats"><img src="images/admin/cheats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("DETECT_POSS_CHEATS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=emailbans"><img src="images/admin/mail_bans.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("EMAIL_BANS"); ?></a><br /></td>
    <td align="center"><a href="faq-manage.php"><img src="images/admin/faq.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FAQ"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=freetorrents"><img src="images/admin/free_leech.png" border="0" width="32" height="32" alt="" /><br />Freeleech Torrents<?php /*echo T_("TORRENTS_FREE_LEECH");*/ ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=lastcomm"><img src="images/admin/comments.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("LATEST_COMMENTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=masspm"><img src="images/admin/mass_pm.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MASS_PM"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=messagespy"><img src="images/admin/message_spy.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("MESSAGE_SPY"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=news&amp;do=view"><img src="images/admin/news.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("NEWS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=peers"><img src="images/admin/peer_list.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("PEERS_LIST"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=polls&amp;do=view"><img src="images/admin/polls.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("POLLS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=reports&amp;do=view"><img src="images/admin/report_system.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("REPORTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=rules&amp;do=view"><img src="images/admin/rules.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("RULES"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=sitelog"><img src="images/admin/site_log.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("SITELOG"); ?></a><br /></td>
    <td align="center"><a href="teams-create.php"><img src="images/admin/teams.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TEAMS"); ?></a><br /></td>
</tr>
<tr> 
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=style"><img src="images/admin/themes.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("THEME_MANAGEMENT"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=categories&amp;do=view"><img src="images/admin/torrent_cats.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_CAT_VIEW"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=torrentlangs&amp;do=view"><img src="images/admin/torrent_lang.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENT_LANG"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=torrentmanage"><img src="images/admin/torrents.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("TORRENTS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=groups&amp;do=view"><img src="images/admin/user_groups.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("USER_GROUPS_VIEW"); ?></a><br /></td>
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=warned"><img src="images/admin/warned_user.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WARNED_USERS"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=whoswhere"><img src="images/admin/whos_where.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WHOS_WHERE"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=censor"><img src="images/admin/word_censor.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WORD_CENSOR"); ?></a><br /></td>
    <td align="center"><a href="admincp.php?action=forum"><img src="images/admin/forums.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("FORUM_MANAGEMENT"); ?><br /></a></td>
    <td align="center"><a href="admincp.php?action=users"><img src="images/admin/simple_user_search.png" border="0" width="32" height="32" alt="" /><br />Simple User Search<br /></a></td>  
</tr>
<tr>
    <td colspan="5">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="admincp.php?action=privacylevel"><img src="images/admin/privacy_level.png" border="0" width="32" height="32" alt="" /><br />Privacy Level<br /></a></td>     
    <td align="center"><a href="admincp.php?action=pendinginvite"><img src="images/admin/pending_invited_user.png" border="0" width="32" height="32" alt="" /><br />Pending Invited Users<br /></a></td>    
    <td align="center"><a href="admincp.php?action=invited"><img src="images/admin/invited_user.png" border="0" width="32" height="32" alt="" /><br />Invited Users<br /></a></td>    
    <td align="center"><a href="admincp.php?action=sqlerr"><img src="images/admin/sql_error.png" border="0" width="32" height="32" alt="" /><br />SQL Error<br /></a></td>  
   </a></td>  
</tr>
</table>

<?php
	end_frame();
}


if (!$action){
	stdhead(T_("ADMIN_CP"));
	navmenu();
	stdfoot();
}

/////////////////////// GROUPS MANAGEMENT ///////////////////////
if ($action=="groups" && $do=="view"){
	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("USER_GROUPS"));
	
    print("<center><a href='admincp.php?action=groups&amp;do=add'>".T_("GROUPS_ADD_NEW")."</a></center>\n");

	print("<br /><br />\n<table width=\"100%\" align=\"center\" border=\"0\" class=\"table_table\">\n");
	print("<tr>\n");
	print("<th class='table_head'>".T_("NAME")."</th>\n");
	print("<th class='table_head'>".T_("TORRENTS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("MEMBERS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("NEWS")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("FORUM")."<br />".T_("GROUPS_VIEW_EDIT_DEL")."</th>\n");
	print("<th class='table_head'>".T_("UPLOAD")."</th>\n");
	print("<th class='table_head'>".T_("DOWNLOAD")."</th>\n");
	print("<th class='table_head'>".T_("CP_VIEW")."</th>\n");
    print("<th class='table_head'>".T_("CP_STAFF_PAGE")."</th>");
    print("<th class='table_head'>".T_("CP_STAFF_PUBLIC")."</th>");
    print("<th class='table_head'>".T_("CP_STAFF_SORT")."</th>");
	print("<th class='table_head'>".T_("DEL")."</th>\n");
	print("</tr>\n");

	$getlevel=SQL_Query_exec("SELECT * from groups ORDER BY group_id");
	while ($level=mysqli_fetch_assoc($getlevel)) {
		 print("<tr>\n");
		 print("<td class='table_col1'><a href='admincp.php?action=groups&amp;do=edit&amp;group_id=".$level["group_id"]."'>".htmlspecialchars($level["level"])."</a></td>\n");
		 print("<td class='table_col2'>".$level["view_torrents"]."/".$level["edit_torrents"]."/".$level["delete_torrents"]."</td>\n");
		 print("<td class='table_col1'>".$level["view_users"]."/".$level["edit_users"]."/".$level["delete_users"]."</td>\n");
		 print("<td class='table_col2'>".$level["view_news"]."/".$level["edit_news"]."/".$level["delete_news"]."</td>\n");
		 print("<td class='table_col1'>".$level["view_forum"]."/".$level["edit_forum"]."/".$level["delete_forum"]."</td>\n");
		 print("<td class='table_col2'>".$level["can_upload"]."</td>\n");
		 print("<td class='table_col1'>".$level["can_download"]."</td>\n");
		 print("<td class='table_col2'>".$level["control_panel"]."</td>\n");
         print("<td class='table_col1'>".$level["staff_page"]."</td>\n");
         print("<td class='table_col2'>".$level["staff_public"]."</td>\n");  
         print("<td class='table_col1'>".$level["staff_sort"]."</td>\n");  
		 print("<td class='table_col1'><a href='admincp.php?action=groups&amp;do=delete&amp;group_id=".$level["group_id"]."'>Del</a></td>\n");

		 print("</tr>\n");
	}

	print("</table><br /><br />");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="edit"){
	$group_id=intval($_GET["group_id"]);
	$rlevel=SQL_Query_exec("SELECT * FROM groups WHERE group_id=$group_id");
	if (!$rlevel)
		show_error_msg(T_("ERROR"),T_("CP_NO_GROUP_ID_FOUND"),1);

	$level=mysqli_fetch_assoc($rlevel);

	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();


	begin_frame(T_("CP_EDIT_GROUP"));
	?>
	<form action="admincp.php?action=groups&amp;do=update&amp;group_id=<?php echo $level["group_id"]; ?>" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Name:</td><td><input type="text" name="gname" value="<?php echo $level["level"];?>" size="40" /></td></tr>
	<tr><td>View Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vtorrent" value="yes" <?php if ($level["view_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vtorrent" value="no" <?php if ($level["view_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="etorrent" value="yes" <?php if ($level["edit_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="etorrent" value="no" <?php if ($level["edit_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dtorrent" value="yes" <?php if ($level["delete_torrents"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dtorrent" value="no" <?php if ($level["delete_torrents"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vuser" value="yes" <?php if ($level["view_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vuser" value="no" <?php if ($level["view_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="euser" value="yes" <?php if ($level["edit_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="euser" value="no" <?php if ($level["edit_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="duser" value="yes" <?php if ($level["delete_users"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="duser" value="no" <?php if ($level["delete_users"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vnews" value="yes" <?php if ($level["view_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vnews" value="no" <?php if ($level["view_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="enews" value="yes" <?php if ($level["edit_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="enews" value="no" <?php if ($level["edit_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Delete News:</td><td> <?php echo T_("YES");?> <input type="radio" name="dnews" value="yes" <?php if ($level["delete_news"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dnews" value="no" <?php if ($level["delete_news"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>View Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vforum" value="yes" <?php if ($level["view_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vforum" value="no" <?php if ($level["view_forum"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Edit In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="eforum" value="yes" <?php if ($level["edit_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="eforum" value="no" <?php if ($level["edit_forum"]=="no") echo "checked = 'checked'" ?> /></td></tr>
	<tr><td>Delete In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dforum" value="yes" <?php if ($level["delete_forum"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dforum" value="no" <?php if ($level["delete_forum"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can Upload:</td><td>  <?php echo T_("YES");?> <input type="radio" name="upload" value="yes" <?php if ($level["can_upload"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="upload" value="no" <?php if ($level["can_upload"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can Download:</td><td>  <?php echo T_("YES");?> <input type="radio" name="down" value="yes" <?php if ($level["can_download"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="down" value="no" <?php if ($level["can_download"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Can View CP:</td><td>  <?php echo T_("YES");?> <input type="radio" name="admincp" value="yes" <?php if ($level["control_panel"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="admincp" value="no" <?php if ($level["control_panel"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
	<tr><td>Staff Page:</td><td>  <?php echo T_("YES");?> <input type="radio" name="staffpage" value="yes" <?php if ($level["staff_page"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="staffpage" value="no" <?php if ($level["staff_page"]=="no") echo "checked = 'checked'"; ?> /></td></tr> 
    <tr><td>Staff Public:</td><td>  <?php echo T_("YES");?> <input type="radio" name="staffpublic" value="yes" <?php if ($level["staff_public"]=="yes") echo "checked = 'checked'" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="staffpublic" value="no" <?php if ($level["staff_public"]=="no") echo "checked = 'checked'"; ?> /></td></tr>
    <tr><td>Staff Sort:</td><td><input type='text' name='sort' size='3' value='<?php echo $level["staff_sort"]; ?>' /></td></tr>
    <?php
	print("\n<tr><td align=\"center\" ><input type=\"submit\" name=\"write\" value=\"Confirm\" /></td></tr>");
	print("</table></form><br /><br />");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="update"){
		stdhead(T_("GROUPS_MANAGEMENT"));
		navmenu();

		begin_frame(T_("_BTN_UPDT_"));

     $update = array();
     $update[] = "level = " . sqlesc($_POST["gname"]);
     $update[] = "view_torrents = " . sqlesc($_POST["vtorrent"]);
     $update[] = "edit_torrents = " . sqlesc($_POST["etorrent"]);
     $update[] = "delete_torrents = " . sqlesc($_POST["dtorrent"]);
     $update[] = "view_users = " . sqlesc($_POST["vuser"]);
     $update[] = "edit_users = " . sqlesc($_POST["euser"]);
     $update[] = "delete_users = " . sqlesc($_POST["duser"]);
     $update[] = "view_news = " . sqlesc($_POST["vnews"]);
     $update[] = "edit_news = " . sqlesc($_POST["enews"]);
     $update[] = "delete_news = " . sqlesc($_POST["dnews"]);
     $update[] = "view_forum = " . sqlesc($_POST["vforum"]);
     $update[] = "edit_forum = " . sqlesc($_POST["eforum"]);
     $update[] = "delete_forum = " . sqlesc($_POST["dforum"]);
     $update[] = "can_upload = " . sqlesc($_POST["upload"]);
     $update[] = "can_download = " . sqlesc($_POST["down"]);
     $update[] = "control_panel = " . sqlesc($_POST["admincp"]);
     $update[] = "staff_page = " . sqlesc($_POST["staffpage"]);
     $update[] = "staff_public = " . sqlesc($_POST["staffpublic"]);
     $update[] = "staff_sort = " . intval($_POST['sort']);
     $strupdate = implode(",", $update);

     $group_id=intval($_GET["group_id"]);
     SQL_Query_exec("UPDATE groups SET $strupdate WHERE group_id=$group_id");
                 
		echo "<br /><center><b>".T_("CP_UPDATED_OK")."</b></center><br />";
		end_frame();
		stdfoot();	
}

if ($action=="groups" && $do=="delete"){
		//Needs to be secured!!!!
		$group_id=intval($_GET["group_id"]);
		if (($group_id=="1") || ($group_id=="7"))
			show_error_msg(T_("ERROR"),T_("CP_YOU_CANT_DEL_THIS_GRP"),1);
 
		SQL_Query_exec("DELETE FROM groups WHERE group_id=$group_id");
        show_error_msg(T_("_DEL_"), T_("CP_DEL_OK"), 1);
}


if ($action=="groups" && $do=="add") {
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));
	?>
	<form action="admincp.php?action=groups&amp;do=addnew" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Group Name:</td><td><input type="text" name="gname" value="" size="40" /></td></tr>
	<tr><td>Copy Settings From: </td><td><select name="getlevel" size="1">
	<?php
	$rlevel=SQL_Query_exec("SELECT DISTINCT group_id, level FROM groups ORDER BY group_id");

	while($level=mysqli_fetch_assoc($rlevel)) {
		print("\n<option value='".$level["group_id"]."'>".htmlspecialchars($level["level"])."</option>");
	}
	print("\n</select></td></tr>");
	print("\n<tr><td align=\"center\" ><input type=\"submit\" name=\"confirm\" value=\"Confirm\" /></td></tr>");
	print("</table></form><br /><br />");
	end_frame();
	stdfoot();	
}

if ($action=="groups" && $do=="addnew") {
	
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));

	$group_id=intval($_POST["getlevel"]);

	$rlevel=SQL_Query_exec("SELECT * FROM groups WHERE group_id=$group_id");
	$level=mysqli_fetch_assoc($rlevel);
	if (!$level)
	   show_error_msg(T_("ERROR"),T_("CP_INVALID_ID"),1);

	$update = array();
	$update[] = "level = " . sqlesc($level["level"]);
	$update[] = "view_torrents = " . sqlesc($level["view_torrents"]);
	$update[] = "edit_torrents = " . sqlesc($level["edit_torrents"]);
	$update[] = "delete_torrents = " . sqlesc($level["delete_torrents"]);
	$update[] = "view_users = " . sqlesc($level["view_users"]);
	$update[] = "edit_users = " . sqlesc($level["edit_users"]);
	$update[] = "delete_users = " . sqlesc($level["delete_users"]);
	$update[] = "view_news = " . sqlesc($level["view_news"]);
	$update[] = "edit_news = " . sqlesc($level["edit_news"]);
	$update[] = "delete_news = " . sqlesc($level["delete_news"]);
	$update[] = "view_forum = " . sqlesc($level["view_forum"]);
	$update[] = "edit_forum = " . sqlesc($level["edit_forum"]);
	$update[] = "delete_forum = " . sqlesc($level["delete_forum"]);
	$update[] = "can_upload = " . sqlesc($level["can_upload"]);
	$update[] = "can_download = " . sqlesc($level["can_download"]);
	$update[] = "control_panel = " . sqlesc($level["control_panel"]);
    $update[] = "staff_page = " . sqlesc($level["staff_page"]);
    $update[] = "staff_public = " . sqlesc($level["staff_public"]);
    $update[] = "staff_sort = " . intval($level["staff_sort"]);
	$strupdate = implode(",", $update);
	SQL_Query_exec("INSERT INTO groups SET $strupdate");

	echo "<br /><center><b>Added OK</b></center><br />";
	end_frame();
	stdfoot();	
}

#====================================#
#		Theme Management		#
#====================================#

if ($action == "style") {
	if ($do == "add") {
		stdhead();
		navmenu();
		if ($_POST) {
			if (empty($_POST['name']))
				$error .= T_("THEME_NAME_WAS_EMPTY")."<br />";
			if (empty($_POST['uri']))
				$error .= T_("THEME_FOLDER_NAME_WAS_EMPTY");
			if ($error)
				show_error_msg(T_("ERROR"), T_("THEME_NOT_ADDED_REASON")." $error", 1);
			if (SQL_Query_exec("INSERT INTO stylesheets (name, uri) VALUES (".sqlesc($_POST["name"]).", ".sqlesc($_POST["uri"]).")"))
				show_error_msg(T_("SUCCESS"), "Theme '".htmlspecialchars($_POST["name"])."' added.", 0);
			elseif (mysqli_errno($GLOBALS["DBconnector"]) == 1062)
				show_error_msg(T_("FAILED"), T_("THEME_ALREADY_EXISTS"), 0);
			else
				show_error_msg(T_("FAILED"), T_("THEME_NOT_ADDED_DB_ERROR")." ".mysqli_error($GLOBALS["DBconnector"]), 0);
		}
		begin_frame(T_("THEME_ADD"));
		?>
        <form action='admincp.php' method='post'>
		<input type='hidden' name='action' value='style' />
        <input type='hidden' name='do' value='add' />
        <table align='center' width='400' cellspacing='0' class='table_table'>
		<tr>
		<td class='table_col1'><?php echo T_("THEME_NAME_OF_NEW")?>:</td>
		<td class='table_col2' align='right'><input type='text' name='name' size='30' maxlength='30' value='<?php echo $name; ?>' /></td>
		</tr>
		<tr>
		<td class='table_col1'><?php echo T_("THEME_FOLDER_NAME_CASE_SENSITIVE")?>:</td>
		<td class='table_col2' align='right'><input type='text' name='uri' size='30' maxlength='30' value='<?php echo $uri; ?>' /></td>
		</tr>
		<tr>
		<td colspan='2' align='center' class='table_head'>
		<input type='submit' value='Add new theme' />
		<input type='reset' value='<?php echo T_("RESET")?>' />
		</td>
		</tr>
		</table>
        </form>
		<br /><?php echo T_("THEME_PLEASE_NOTE_ALL_THEMES_MUST"); ?>
		<?php
		end_frame();
		stdfoot();
   } elseif ($do == "del") {

        if (!@count($_POST["ids"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
        $ids = array_map("intval", $_POST["ids"]);
        $ids = implode(', ', $ids);
        SQL_Query_exec("DELETE FROM `stylesheets` WHERE `id` IN ($ids)");
        SQL_Query_exec("UPDATE `users` SET `stylesheet` = ".$site_config["default_theme"]." WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
        autolink("admincp.php?action=style", T_("THEME_SUCCESS_THEME_DELETED"));
                                 
	}elseif ($do == "add2") {

		$add = $_POST["add"];
		$a = 0;
		foreach ($add as $theme) {
			if ($theme['add'] != 1) { $a++; continue; }
			if (!SQL_Query_exec("INSERT INTO stylesheets (name, uri) VALUES(".sqlesc($theme['name']).", ".sqlesc($theme['uri']).")")) {
				if (mysqli_errno($GLOBALS["DBconnector"]) == 1062)
					$error .= htmlspecialchars($theme['name'])." - ".T_("THEME_ALREADY_EXISTS").".<br />";
				else
					$error .= htmlspecialchars($theme['name']).": ".T_("THEME_DATEBASE_ERROR")." ".mysqli_error($GLOBALS["DBconnector"])." (".mysqli_errno($GLOBALS["DBconnector"]).")<br />";
			}else
				$added .= htmlspecialchars($theme['name'])."<br />";
		}
        
        if ($a == count($add))
            autolink("admincp.php?action=style", T_("THEME_NOTHING_SELECTED"));

		if ($added)
            autolink("admincp.php?action=style", sprintf(T_("THEME_THE_FOLLOWING_THEMES_WAS_ADDED"), $added));   
		if ($error)
			show_error_msg(T_("FAILED"), sprintf(T_("THEME_THE_FOLLOWING_THEMES_WAS_NOT_ADDED"), $error), 1);
        
	}else{
		stdhead(T_("THEME_MANAGEMENT"));
		navmenu();
		begin_frame(T_("THEME_MANAGEMENT"));
		$res = SQL_Query_exec("SELECT * FROM stylesheets");
		echo "<center><a href='admincp.php?action=style&amp;do=add'>".T_("THEME_ADD")."</a><!-- - <b>".T_("THEME_CLICK_A_THEME_TO_EDIT")."</b>--></center><br />";
		echo T_("THEME_CURRENT").":<form id='deltheme' method='post' action='admincp.php?action=style&amp;do=del'><table width='60%' class='table_table' align='center'>".
			"<tr><th class='table_head'>ID</th><th class='table_head'>".T_("NAME")."</th><th class='table_head'>".T_("THEME_FOLDER_NAME")."</th><th width='5%' class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr>";
		while ($row=mysqli_fetch_assoc($res)) {
			if (!is_dir("themes/$row[uri]"))
				$row['uri'] .= " <b>- ".T_("THEME_DIR_DONT_EXIST")."</b>";
			echo "<tr><td class='table_col1' align='center'>$row[id]</td><td class='table_col2' align='center'>$row[name]</td><td class='table_col1' align='center'>$row[uri]</td><td class='table_col2' align='center'><input name='ids[]' type='checkbox' value='$row[id]' /></td></tr>";
		}
		mysqli_free_result($res);
		echo "<tr><td colspan='4' align='right'><input type='submit' value='".T_("SELECTED_DELETE")."' /></td></tr></table></form>";
		
		echo "<p>".T_("THEME_IN_THEMES_BUT_NOT_IN_DB")."</p><form id='addtheme' action='admincp.php?action=style&amp;do=add2' method='post'><table width='60%' class='table_table' align='center'>".
			"<tr><th class='table_head'>".T_("NAME")."</th><th class='table_head'>".T_("THEME_FOLDER_NAME")."</th><th width='5%' class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th></tr>";
		$dh = opendir("themes/");
		$i=0;
		while (($file = readdir($dh)) !== false) {
			if ($file == "." || $file == ".." || !is_dir("themes/$file"))
				continue;
			if (is_file("themes/$file/header.php")) {
					$res = SQL_Query_exec("SELECT id FROM stylesheets WHERE uri = '$file' ");
					if (mysqli_num_rows($res) == 0) {
						echo "<tr><td class='table_col1' align='center'><input type='text' name='add[$i][name]' value='$file' /></td><td class='table_col2' align='center'>$file<input type='hidden' name='add[$i][uri]' value='$file' /></td><td class='table_col1' align='center'><input type='checkbox' name='add[$i][add]' value='1' /></td></tr>";
						$i++;
					}
				}
		}
		if (!$i) echo "<tr><td class='table_col1' align='center' colspan='3'>".T_("THEME_NOTHING_TO_SHOW")."</td></tr>";
		echo "</table><p align='center'>".($i?"<input type='submit' value='".T_("SELECTED_ADD")."' />":"")."</p></form>";
		end_frame();
		stdfoot();
	}
}

/////////////////////// NEWS ///////////////////////
if ($action=="news" && $do=="view"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("NEWS"));
	echo "<center><a href='admincp.php?action=news&amp;do=add'><b>".T_("CP_NEWS_ADD_ITEM")."</b></a></center><br />";

	$res = SQL_Query_exec("SELECT * FROM news ORDER BY added DESC");
	if (mysqli_num_rows($res) > 0){
		
		while ($arr = mysqli_fetch_assoc($res)) {
			$newsid = $arr["id"];
			$body = format_comment($arr["body"]);
			$title = $arr["title"];
			$userid = $arr["userid"];
			$added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

			$res2 = SQL_Query_exec("SELECT username FROM users WHERE id = $userid");
			$arr2 = mysqli_fetch_assoc($res2);
			
			$postername = $arr2["username"];
			
			if ($postername == "")
				$by = "Unknown";
			else
				$by = "<a href='account-details.php?id=$userid'><b>$postername</b></a>";
			
			print("<table border='0' cellspacing='0' cellpadding='0'><tr><td>");
			print("$added&nbsp;---&nbsp;by&nbsp;$by");
			print(" - [<a href='?action=news&amp;do=edit&amp;newsid=$newsid'><b>".T_("EDIT")."</b></a>]");
			print(" - [<a href='?action=news&amp;do=delete&amp;newsid=$newsid'><b>".T_("DEL")."</b></a>]");
			print("</td></tr>\n");

			print("<tr valign='top'><td><b>$title</b><br />$body</td></tr></table><br />\n");
		}

	}else{
	 echo "No News Posted";
	}

	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="takeadd"){
	$body = $_POST["body"];
	
	if (!$body)
		show_error_msg(T_("ERROR"),T_("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"),1); 

	$title = $_POST['title'];

	if (!$title)
		show_error_msg(T_("ERROR"),T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"),1);
	
	$added = $_POST["added"];

	if (!$added)
		$added = sqlesc(get_date_time());

	SQL_Query_exec("INSERT INTO news (userid, added, body, title) VALUES (".

	$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($title) . ")");

	if (mysqli_affected_rows($GLOBALS["DBconnector"]) == 1)
		show_error_msg(T_("COMPLETED"),T_("CP_NEWS_ITEM_ADDED_SUCCESS"),1);
	else
		show_error_msg(T_("ERROR"),T_("CP_NEWS_UNABLE_TO_ADD"),1);
}

if ($action=="news" && $do=="add"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame(T_("CP_NEWS_ADD"));
	print("<center><form method='post' action='admincp.php' name='news'>\n");
	print("<input type='hidden' name='action' value='news' />\n");
	print("<input type='hidden' name='do' value='takeadd' />\n");

	print("<b>".T_("CP_NEWS_TITLE").":</b> <input type='text' name='title' /><br />\n");

	echo "<br />".textbbcode("news","body")."<br />";

	print("<br /><br /><input type='submit' value='".T_("SUBMIT")."' />\n");

	print("</form><br /><br /></center>\n");
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="edit"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);
                                                                                            
	$res = SQL_Query_exec("SELECT * FROM news WHERE id=$newsid");

	if (mysqli_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), sprintf(T_("CP_NEWS_NO_ITEM_WITH_ID"), $newsid),1);

	$arr = mysqli_fetch_assoc($res);

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  		$body = $_POST['body'];

		if ($body == "")
    		show_error_msg(T_("ERROR"), T_("FORUMS_BODY_CANNOT_BE_EMPTY"),1);

		$title = $_POST['title'];

		if ($title == "")
			show_error_msg(T_("ERROR"), T_("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"),1);

		$body = sqlesc($body);

		$editedat = sqlesc(get_date_time());

		SQL_Query_exec("UPDATE news SET body=$body, title='$title' WHERE id=$newsid");

		$returnto = $_POST['returnto'];

		if ($returnto != "")
			header("Location: $returnto");
		else
			autolink("admincp.php?action=news&do=view", T_("CP_NEWS_ITEM_WAS_EDITED_SUCCESS")); 
	} else {
		$returnto = htmlspecialchars($_GET['returnto']);
		begin_frame(T_("CP_NEWS_EDIT"));
		print("<form method='post' action='?action=news&amp;do=edit&amp;newsid=$newsid' name='news'>\n");
		print("<center>");
		print("<input type='hidden' name='returnto' value='$returnto' />\n");
		print("<b>".T_("CP_NEWS_TITLE").": </b><input type='text' name='title' value=\"".$arr['title']."\" /><br /><br />\n");
		echo "<br />".textbbcode("news","body",$arr["body"])."<br />";
		print("<br /><input type='submit' value='Okay' />\n");
		print("</center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="delete"){

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

	SQL_Query_exec("DELETE FROM news WHERE id=$newsid");
    SQL_Query_exec("DELETE FROM comments WHERE news = $newsid");
	
	show_error_msg(T_("COMPLETED"),T_("CP_NEWS_ITEM_DEL_SUCCESS"),1);
}

///////////////// BLOCKS MANAGEMENT /////////////
if ($action=="blocks" && $do=="view") {
    stdhead(T_("_BLC_MAN_"));

    navmenu();

    begin_frame(T_("BLC_VIEW"));

    $enabled = SQL_Query_exec("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort");
    $disabled = SQL_Query_exec("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort");
    
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("_BLC_ENABLED_")."</th>".
            "</tr>".
        "</table><br />".
        "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr>");
        while($blocks = mysqli_fetch_assoc($enabled)){
        if(!$setclass){
            $class="table_col2";$setclass=true;}
        else{
            $class="table_col1";$setclass=false;}
    
            print("<tr>".
                        "<td class=\"$class\" valign=\"top\">".$blocks["named"]."</td>".
                        "<td class=\"$class\">".$blocks["description"]."</td>".
                        "<td class=\"$class\" align=\"center\">".$blocks["position"]."</td>".
                        "<td class=\"$class\" align=\"center\">".$blocks["sort"]."</td>".
                        "<td class=\"$class\" align=\"center\">[<a href=\"blocks-edit.php?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" class=\"table_head\"><form action='blocks-edit.php'><input type='submit' value='Edit' /></form></td></tr>");
    print("</table>");
    print("</td></tr></table>");    
    
    print("<hr />");
    $setclass=false;
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">Disabled Blocks</th>".
            "</tr>".
        "</table><br />".
        "<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<th class=\"table_head\">".T_("NAME")."</th>".
                "<th class=\"table_head\">Description</th>".
                "<th class=\"table_head\">Position</th>".
                "<th class=\"table_head\">Sort<br />Order</th>".
                "<th class=\"table_head\">Preview</th>".
            "</tr>");
        while($blocks = mysqli_fetch_assoc($disabled)){
        if(!$setclass){
            $class="table_col2";$setclass=true;}
        else{
            $class="table_col1";$setclass=false;}
    
            print("<tr>".
                        "<td class='$class' valign=\"top\">".$blocks["named"]."</td>".
                        "<td class='$class'>".$blocks["description"]."</td>".
                        "<td class='$class' align=\"center\">".$blocks["position"]."</td>".
                        "<td class='$class' align=\"center\">".$blocks["sort"]."</td>".
                        "<td class='$class' align=\"center\">[<a href=\"blocks-edit.php?preview=true&amp;name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" align=\"center\" valign=\"bottom\" class=\"table_head\"><form action='blocks-edit.php'><input type='submit' value='Edit' /></form></td></tr>");
    print("</table>");
    print("</td></tr></table>");    
    end_frame();
    stdfoot();    
}


////////// categories /////////////////////
if ($action=="categories" && $do=="view"){
	stdhead(T_("Categories Management"));
	navmenu();

	begin_frame(T_("TORRENT_CATEGORIES"));
	echo "<center><a href='admincp.php?action=categories&amp;do=add'><b>Add New Category</b></a></center><br />";

	print("<i>Please note that if no image is specified, the category name will be displayed</i><br /><br />");

	echo("<center><table width='95%' class='table_table'>");
	echo("<tr><th width='10' class='table_head'>Sort</th><th class='table_head'>Parent Cat</th><th class='table_head'>Sub Cat</th><th class='table_head'>Image</th><th width='30' class='table_head'></th></tr>");
	$query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
	$sql = SQL_Query_exec($query);
	while ($row = mysqli_fetch_assoc($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];
		$parent = $row['parent_cat'];

		print("<tr><td class='table_col1'>$priority</td><td class='table_col2'>$parent</td><td class='table_col1'>$name</td><td class='table_col2' align='center'>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/categories/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class='table_col1'><a href='admincp.php?action=categories&amp;do=edit&amp;id=$id'>[EDIT]</a> <a href='admincp.php?action=categories&amp;do=delete&amp;id=$id'>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
}


if ($action=="categories" && $do=="edit"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

	$res = SQL_Query_exec("SELECT * FROM categories WHERE id=$id");

	if (mysqli_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No category with ID $id.",1);

	$arr = mysqli_fetch_assoc($res);

	if ($_GET["save"] == '1'){
  		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
    		show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$name = $_POST['name'];
		if ($name == "")
			show_error_msg(T_("ERROR"), "Sub cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

		SQL_Query_exec("UPDATE categories SET parent_cat=$parent_cat, name=$name, sort_index=$sort_index, image=$image WHERE id=$id");

		show_error_msg(T_("COMPLETED"),"category was edited successfully.",0);

	} else {
		begin_frame(T_("CP_CATEGORY_EDIT"));
		print("<form method='post' action='?action=categories&amp;do=edit&amp;id=$id&amp;save=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Parent Category: </b><input type='text' name='parent_cat' value=\"".$arr['parent_cat']."\" /> All Subcats with EXACTLY the same parent cat are grouped</td></tr>\n");
		print("<tr><td align='left'><b>Sub Category: </b><input type='text' name='name' value=\"".$arr['name']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value=\"".$arr['sort_index']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Image: </b><input type='text' name='image' value=\"".$arr['image']."\" /> single filename</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="delete"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];

	if ($_GET["sure"] == '1'){

		if (!is_valid_id($id))
			show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

		$newcatid = (int) $_POST["newcat"];

		SQL_Query_exec("UPDATE torrents SET category=$newcatid WHERE category=$id"); //move torrents to a new cat

		SQL_Query_exec("DELETE FROM categories WHERE id=$id"); //delete old cat
		
		show_error_msg(T_("COMPLETED"),"Category Deleted OK",1);

	}else{
		begin_frame(T_("CATEGORY_DEL"));
		print("<form method='post' action='?action=categories&amp;do=delete&amp;id=$id&amp;sure=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Category ID to move all Torrents To: </b><input type='text' name='newcat' /> (Cat ID)</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="takeadd"){
  		$name = $_POST['name'];
		if ($name == "")
    		show_error_msg(T_("ERROR"), "Sub Cat cannot be empty!",1);

		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
			show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

	SQL_Query_exec("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES ($name, $parent_cat, $sort_index, $image)");

	if (mysqli_affected_rows($GLOBALS["DBconnector"]) == 1)
		show_error_msg(T_("COMPLETED"),"Category was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add category",1);
}

if ($action=="categories" && $do=="add"){
	stdhead(T_("CATEGORY_MANAGEMENT"));
	navmenu();

	begin_frame(T_("CATEGORY_ADD"));
	print("<center><form method='post' action='admincp.php'>\n");
	print("<input type='hidden' name='action' value='categories' />\n");
	print("<input type='hidden' name='do' value='takeadd' />\n");
                       
	print("<table border='0' cellspacing='0' cellpadding='5'>\n");

	print("<tr><td align='left'><b>Parent Category:</b> <input type='text' name='parent_cat' /></td></tr>\n");
	print("<tr><td align='left'><b>Sub Category:</b> <input type='text' name='name' /></td></tr>\n");
	print("<tr><td align='left'><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>\n");
	print("<tr><td align='left'><b>Image:</b> <input type='text' name='image' /></td></tr>\n");

	print("<tr><td colspan='2'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");

	print("</table></form></center>\n");
	end_frame();
	stdfoot();
}

if ($action == "whoswhere")
{
    stdhead("Where are members");
    navmenu();
    
    $res = SQL_Query_exec("SELECT `id`, `username`, `page`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != '' ORDER BY `last_access` DESC LIMIT 100");
    
    begin_frame("Last 100 Page Views");
    ?>
    
    <table border="0" cellpadding="4" cellspacing="3" width="80%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">Page</th>
        <th class="table_head">Accessed</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><b><?php echo $row["username"]; ?></b></a></td>
        <td class="table_col2" align="center"><?php echo htmlspecialchars($row["page"]); ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>
    </tr>
    <?php endwhile; ?>
    </table>
    
    <?php 
    end_frame();
    stdfoot(); 
}

if ($action=="peers"){
	stdhead("Peers List");
	navmenu();

	begin_frame("Peers List");

	$count1 = number_format(get_row_count("peers"));

	print("<center>We have $count1 peers</center><br />");

	$res4 = SQL_Query_exec("SELECT COUNT(*) FROM peers $limit");
	$row4 = mysqli_fetch_array($res4);

	$count = $row4[0];
	$peersperpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count, "admincp.php?action=peers&amp;");

	print("$pagertop");

	$sql = "SELECT * FROM peers ORDER BY started DESC $limit";
	$result = SQL_Query_exec($sql);

	if( mysqli_num_rows($result) != 0 ) {
		print'<center><table width="100%" border="0" cellspacing="0" cellpadding="3" class="table_table">';
		print'<tr>';
		print'<th class="table_head">User</th>';
		print'<th class="table_head">Torrent</th>';
		print'<th class="table_head">IP</th>';
		print'<th class="table_head">Port</th>';
		print'<th class="table_head">Upl.</th>';
		print'<th class="table_head">Downl.</th>';
		print'<th class="table_head">Peer-ID</th>';
		print'<th class="table_head">Conn.</th>';
		print'<th class="table_head">Seeding</th>';
		print'<th class="table_head">Started</th>';
		print'<th class="table_head">Last<br />Action</th>';
		print'</tr>';

		while($row = mysqli_fetch_assoc($result)) {
			if ($site_config['MEMBERSONLY']) {
				$sql1 = "SELECT id, username FROM users WHERE id = $row[userid]";
				$result1 = SQL_Query_exec($sql1);
				$row1 = mysqli_fetch_assoc($result1);
			}

			if ($row1['username'])
				print'<tr><td class="table_col1"><a href="account-details.php?id=' . $row['userid'] . '">' . $row1['username'] . '</a></td>';
			else
				print'<tr><td class="table_col1">'.$row["ip"].'</td>';

			$sql2 = "SELECT id, name FROM torrents WHERE id = $row[torrent]";
			$result2 = SQL_Query_exec($sql2);

			while ($row2 = mysqli_fetch_assoc($result2)) {

                $smallname = CutName(htmlspecialchars($row2["name"]), 40);
                
				print'<td class="table_col2"><a href="torrents-details.php?id=' . $row['torrent'] . '">' . $smallname . '</a></td>';
				print'<td align="center" class="table_col1">' . $row['ip'] . '</td>';
				print'<td align="center" class="table_col2">' . $row['port'] . '</td>';

				if ($row['uploaded'] < $row['downloaded'])
					print'<td align="center" class="table_col1"><font class="error">' . mksize($row['uploaded']) . '</font></td>';
				else
					if ($row['uploaded'] == '0')
						print'<td align="center" class="table_col1">' . mksize($row['uploaded']) . '</td>';
					else
						print'<td align="center" class="table_col1"><font color="green">' . mksize($row['uploaded']) . '</font></td>';
				print'<td align="center" class="table_col2">' . mksize($row['downloaded']) . '</td>';
				print'<td align="center" class="table_col1">' . substr($row["peer_id"], 0, 8) . '</td>';
				if ($row['connectable'] == 'yes')
					print'<td align="center" class="table_col2"><font color="green">' . $row['connectable'] . '</font></td>';
				else
					print'<td align="center" class="table_col2"><font class="error">' . $row['connectable'] . '</font></td>';
				if ($row['seeder'] == 'yes')
					print'<td align="center" class="table_col1"><font color="green">' . $row['seeder'] . '</font></td>';
				else
					print'<td align="center" class="table_col1"><font class="error">' . $row['seeder'] . '</font></td>';
				print'<td align="center" class="table_col2">' . utc_to_tz($row['started']) . '</td>';
				print'<td align="center" class="table_col1">' . utc_to_tz($row['last_action']) . '</td>';
				print'</tr>';
			}
		}
		print'</table>';
		print("$pagerbottom</center>");
	}else{
		print'<center><b>No Peers</b></center><br />';
	}
	end_frame();

	stdfoot();
}
                           

if ($action=="lastcomm"){
    
    $count = get_row_count("comments");
    
    list($pagertop, $pagerbottom, $limit) = pager(10, $count, "admincp.php?action=lastcomm&amp;");
                 
	stdhead("Latest Comments");
	navmenu();

	begin_frame("Last Comments");

	$res = SQL_Query_exec("SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added FROM comments c LEFT JOIN torrents t ON c.torrent = t.id LEFT JOIN news n ON c.news = n.id LEFT JOIN users u ON c.user = u.id ORDER BY c.added DESC $limit");
    
	while ($arr = mysqli_fetch_assoc($res)) {
		$userid = $arr["user"];
		$username = $arr["username"];
		$data = $arr["added"];
		$tid = $arr["torrent"];
        $nid = $arr["news"];
		$title = ( $arr['title'] ) ? $arr['title'] : $arr['name'];
		$comentario = stripslashes(format_comment($arr["text"]));
		$cid = $arr["id"];    
        
        $type = 'Torrent: <a href="torrents-details.php?id='.$tid.'">'.$title.'</a>';
        
        if ( $nid > 0 )
        {
             $type = 'News: <a href="comments.php?id='.$nid.'&amp;type=news">'.$title.'</a>';
        }
                       
		echo "<table class='table_table' align='center' cellspacing='0' width='100%'><tr><th class='table_head' align='center'>".$type."</td></tr><tr><td class='table_col2'>".$comentario."</th></tr><tr><td class='table_col1' align='center'>Posted in <b>".$data."</b> by <a href=\"account-details.php?id=".$userid."\">".$username."</a><!--  [ <a href=\"edit-comments.php?cid=".$cid."\">edit</a> | <a href=\"edit-comments.php?action=delete&amp;cid=".$cid."\">delete</a> ] --></td></tr></table><br />";
        #$rows[] = $arr;
	}
    
    if ($count > 10) echo $pagerbottom;
    
	end_frame();
	stdfoot();
}


if ($action=="messagespy"){                                    
	if ($do == "del") {
		if ($_POST["delall"])
			SQL_Query_exec("DELETE FROM `messages`");
		else {
			if (!@count($_POST["del"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
			$ids = array_map("intval", $_POST["del"]);
			$ids = implode(", ", $ids);
			SQL_Query_exec("DELETE FROM `messages` WHERE `id` IN ($ids)");
		}
		autolink("admincp.php?action=messagespy", T_("CP_DELETED_ENTRIES")); 
		stdhead();
		show_error_msg(T_("SUCCESS"), T_("CP_DELETED_ENTRIES"), 0);
		stdfoot();
		die;
	}


	stdhead("Message Spy");
	navmenu();

	$res2 = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')");
	$row = mysqli_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=messagespy&amp;");

	begin_frame("Message Spy");

	echo $pagertop;

	$res = SQL_Query_exec("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");

	print("<form id='messagespy' method='post' action='?action=messagespy&amp;do=del'><table border='0' cellspacing='0' cellpadding='3' align='center' class='table_table'>\n");

	print("<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th><th class='table_head' align='left'>Sender</th><th class='table_head' align='left'>Receiver</th><th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th></tr>\n");

	while ($arr = mysqli_fetch_assoc($res)){
		$res2 = SQL_Query_exec("SELECT username FROM users WHERE id=" . $arr["receiver"]);

		if ($arr2 = mysqli_fetch_assoc($res2))
			$receiver = "<a href='account-details.php?id=" . $arr["receiver"] . "'><b>" . $arr2["username"] . "</b></a>";
		else
			$receiver = "<i>Deleted</i>";

		$res3 = SQL_Query_exec("SELECT username FROM users WHERE id=" . $arr["sender"]);
		$arr3 = mysqli_fetch_assoc($res3);

		$sender = "<a href='account-details.php?id=" . $arr["sender"] . "'><b>" . $arr3["username"] . "</b></a>";
		if( $arr["sender"] == 0 )
			$sender = "<font class='error'><b>System</b></font>";
		$msg = format_comment($arr["msg"]);

		$added = utc_to_tz($arr["added"]);

		print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>$sender</td><td align='left' class='table_col2'>$receiver</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
	}

	print("</table><br />");
	echo "<input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></form>";


	print($pagerbottom);

	end_frame();
	stdfoot();
}


 if ($action == "torrentmanage") {
        
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"]))
                  show_error_msg(T_("ERROR"), "Nothing selected click <a href='admincp.php?action=torrentmanage'>here</a> to go back.", 1);
            foreach ($_POST["torrentids"] as $id) {
                deletetorrent(intval($id));
                write_log("Torrent ID $id was deleted by $CURUSER[username]");
            }
            show_error_msg("Torrents Deleted", "Go <a href='admincp.php?action=torrentmanage'>back</a>?", 1);
        }
        
        $search = (!empty($_GET["search"])) ? htmlspecialchars(trim($_GET["search"])) : "";
        
        $where = ($search == "") ? "" : "WHERE name LIKE " . sqlesc("%$search%") . "";
        
        $count = get_row_count("torrents", $where);
        
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, "admincp.php?action=torrentmanage&amp;");
        
        $res = SQL_Query_exec("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents $where ORDER BY name $limit");
        
        stdhead("Torrent Management");
        navmenu();
        
        begin_frame("Torrent Management");

        ?>

        <center>
        <form method='get' action='admincp.php'>
        <input type='hidden' name='action' value='torrentmanage' />
        Search: <input type='text' name='search' value='<?php echo $search; ?>' size='30' />
        <input type='submit' value='Search' />
        </form>

        <form id="myform" method='post' action='admincp.php?action=torrentmanage'>
        <input type='hidden' name='do' value='delete' />
        <table cellpadding='5' cellspacing='3' width='100%' align='center' class='table_table'>
        <tr>
            <th class='table_head'><?php echo T_("NAME"); ?></th>
            <th class='table_head'>Visible</th>
            <th class='table_head'>Banned</th>
            <th class='table_head'>Seeders</th>
            <th class='table_head'>Leechers</th>
            <th class='table_head'>External</th>
            <th class='table_head'>Edit</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>
        
        <?php while ($row = mysqli_fetch_array($res)) { ?>
        
        <tr>
            <td class='table_col1'><a href='torrents-details.php?id=<?php echo $row["id"]; ?>'><?php echo CutName(htmlspecialchars($row["name"]), 40); ?></a></td>
            <td class='table_col2'><?php echo $row["visible"]; ?></td>
            <td class='table_col1'><?php echo $row["banned"]; ?></td>
            <td class='table_col2'><?php echo number_format($row["seeders"]); ?></td>
            <td class='table_col1'><?php echo number_format($row["leechers"]); ?></td>
            <td class='table_col2'><?php echo $row["external"]; ?></td>
            <td class='table_col1'><a href='torrents-edit.php?id=<?php echo $row["id"]; ?>&amp;returnto=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'>Edit</a></td>
            <td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>    
        </tr>
        
        <?php } ?>
        
        </table>
        <br />
        <input type='submit' value='Delete checked' />
        </form>
        <br />
        <?php echo $pagerbottom; ?>
        </center>
        
        <?php
        
        end_frame();
        stdfoot();
        
    }

if ($action == "sitelog") {
	if ($do == "del") {
		if ($_POST["delall"])
			SQL_Query_exec("DELETE FROM `log`");
		else {
			if (!@count($_POST["del"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
			$ids = array_map("intval", $_POST["del"]);
			$ids = implode(", ", $ids);
			SQL_Query_exec("DELETE FROM `log` WHERE `id` IN ($ids)");
		}
		autolink("admincp.php?action=sitelog", T_("CP_DELETED_ENTRIES"));
		stdhead();
		show_error_msg(T_("SUCCESS"), T_("CP_DELETED_ENTRIES"), 0);
		stdfoot();
		die;
	}

	stdhead("Site Log");
	navmenu();

    $search = trim($_GET['search']);
	
	if ($search != '' ){
		$where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
	}

	$res2 = SQL_Query_exec("SELECT COUNT(*) FROM log $where");
	$row = mysqli_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=sitelog&amp;");

	begin_frame("Site Log");

	print("<form method='get' action='?'><center>");
	print("<input type='hidden' name='action' value='sitelog' />\n");
	print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
	print("<input type='submit' value='Search' />\n");
	print("</center></form><br />\n");

	echo $pagertop;
	?>
                           
    <form id='sitelog' action='admincp.php?action=sitelog&amp;do=del' method='post'>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
        <th class="table_head">Date</th>
        <th class="table_head">Time</th>
        <th class="table_head">Event</th>
    </tr>

	<?php
	
	$rqq = "SELECT id, added, txt FROM log $where ORDER BY id DESC $limit";
	$res = SQL_Query_exec($rqq);

	 while ($arr = mysqli_fetch_array($res)){
		$arr['added'] = utc_to_tz($arr['added']);
		$date = substr($arr['added'], 0, strpos($arr['added'], " "));
		$time = substr($arr['added'], strpos($arr['added'], " ") + 1);
		print("<tr><td class='table_col2' align='center'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td class='table_col1' align='center'>$date</td><td class='table_col2' align='center'>$time</td><td class='table_col1' align='left'>".stripslashes($arr["txt"])."</td><!--<td class='table_col2'><a href='staffcp.php?act=view_log&amp;do=del_log&amp;lid=$arr[id]' title='delete this entry'>delete</a></td>--></tr>\n");
	 }
	echo "<tr><td class='table_head' align='center' colspan='4'>\n";
	echo "<input type='submit' value='Delete Checked' /> <input type='submit' value='Delete All' name='delall' /></td></tr></table></form>";

	print($pagerbottom);

	end_frame();
	stdfoot();
}

if ($action == "cheats") {
	stdhead("Possible Cheater Detection");
	navmenu();

    $megabts = (int) $_POST['megabts'];
    $daysago = (int) $_POST['daysago'];

	if ($daysago && $megabts){

		$timeago = 84600 * $daysago; //last 7 days
		$bytesover = 1048576 * $megabts; //over 500MB Upped

		$result = SQL_Query_exec("select * FROM users WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC "); 
		$num = mysqli_num_rows($result); // how many uploaders

		begin_frame("Possible Cheater Detection");
		echo "<p>" . $num . " Users with found over last ".$daysago." days with more than ".$megabts." MB (".$bytesover.") Bytes Uploaded.</p>";

		$zerofix = $num - 1; // remove one row because mysql starts at zero

		if ($num > 0){
		echo "<table align='center' class='table_table'>";
		echo "<tr>";
		 echo "<th class='table_head'>No.</th>";
		 echo "<th class='table_head'>" .T_("USERNAME"). "</th>";
		 echo "<th class='table_head'>" .T_("UPLOADED"). "</th>";
		 echo "<th class='table_head'>" .T_("DOWNLOADED"). "</th>";
		 echo "<th class='table_head'>" .T_("RATIO"). "</th>";
		 echo "<th class='table_head'>" .T_("TORRENTS_POSTED"). "</th>";
		 echo "<th class='table_head'>AVG Daily Upload</th>";
		 echo "<th class='table_head'>" .T_("ACCOUNT_SEND_MSG"). "</th>";
		 echo "<th class='table_head'>Joined</th>";
		echo "</tr>";

		for ($i = 0; $i <= $zerofix; $i++) {
			 $id = mysqli_result($result, $i, "id");
			 $username = mysqli_result($result, $i, "username");
			 $added = mysqli_result($result, $i, "added");
			 $uploaded = mysqli_result($result, $i, "uploaded");
			 $downloaded = mysqli_result($result, $i, "downloaded");
			 $donated = mysqli_result($result, $i, "donated");
			 $warned = mysqli_result($result, $i, "warned");
			 $joindate = "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($added)) . " ago";
			 $upperquery = "SELECT added FROM torrents WHERE owner = $id";
			 $upperresult = SQL_Query_exec($upperquery);
			 $seconds = mkprettytime(utc_to_tz_time() - utc_to_tz_time($added));
			 $days = explode("d ", $seconds);

			 if(sizeof($days) > 1) {
				 $dayUpload  = $uploaded / $days[0];
				 $dayDownload = $downloaded / $days[0];
			}
		 
		  $torrentinfo = mysqli_fetch_array($upperresult);
		 
		  $numtorrents = mysqli_num_rows($upperresult);
		   
		  if ($downloaded > 0){
		   $ratio = $uploaded / $downloaded;
		   $ratio = number_format($ratio, 3);
		   $color = get_ratio_color($ratio);
		   if ($color)
		   $ratio = "<font color='$color'>$ratio</font>";
		   }
		  else
		   if ($uploaded > 0)
			$ratio = "Inf.";
		   else
			$ratio = "---";
		  
		 
		 $counter = $i + 1;
		 
		 echo "<tr>";
		  echo "<td align='center class='table_col1'>$counter.</td>";
		  echo "<td class='table_col2'><a href='account-details.php?id=$id'>$username</a></td>";
		  echo "<td class='table_col1'>" . mksize($uploaded). "</td>";
		  echo "<td class='table_col2'>" . mksize($downloaded) . "</td>";
		  echo "<td class='table_col1'>$ratio</td>";
		  if ($numtorrents == 0) echo "<td class='table_col2'><font color='red'>$numtorrents torrents</font></td>";
		  else echo "<td class=table_col2>$numtorrents torrents</td>";

		  echo "<td class='table_col1'>" . mksize($dayUpload) . "</td>";

		  echo "<td align='center' class='table_col2'><a href='mailbox.php?compose&amp;id=$id'>PM</a></td>";
		  echo "<td class='table_col1'>" . $joindate . "</td>";
		 echo "</tr>";

		 
		 }
		echo "</table><br /><br />";
		end_frame();
		}

		if ($num == 0)
		{
		end_frame();
		}

	}else{
	begin_frame("Possible Cheater Detection");?>
	<center><form action='admincp.php?action=cheats' method='post'>
		Number of days joined: <input type='text' size='4' maxlength='4' name='daysago' /> Days<br /><br />
		MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts' /> MB<br />
		<input type='submit' value='<?php echo T_("SUBMIT"); ?>' />
		</form></center><?php
	end_frame();
	}
	stdfoot();
}


if ($action=="emailbans"){
	stdhead(T_("EMAIL_BANS"));
	navmenu();

	$remove = (int) $_GET['remove'];

	if (is_valid_id($remove)){
		SQL_Query_exec("DELETE FROM email_bans WHERE id=$remove");
		write_log(sprintf(T_("EMAIL_BANS_REM"), $remove, $CURUSER["username"]));
	}

	if ($_GET["add"] == '1'){
		$mail_domain = trim($_POST["mail_domain"]);
		$comment = trim($_POST["comment"]);

		if (!$mail_domain || !$comment){
			show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").".",0);
			stdfoot();
			die;
		}
		$mail_domain= sqlesc($mail_domain);
		$comment = sqlesc($comment);
		$added = sqlesc(get_date_time());

		SQL_Query_exec("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES($added, $CURUSER[id], $mail_domain, $comment)");

		write_log(sprintf(T_("EMAIL_BANS_ADD"), $mail_domain, $CURUSER["username"]));
		show_error_msg(T_("COMPLETE"), T_("EMAIL_BAN_ADDED"), 0);
		stdfoot();
		die;
	}

	begin_frame(T_("EMAILS_OR_DOMAINS_BANS"));
	print(T_("EMAIL_BANS_INFO") . "<br /><br /><br /><b>".T_("ADD_EMAIL_BANS")."</b>\n");
	print("<form method='post' action='admincp.php?action=emailbans&amp;add=1'>\n"); 
    print("<table border='0' cellspacing='0' cellpadding='5' align='center'>\n");
	print("<tr><td align='right'>".T_("EMAIL_ADDRESS") . T_("DOMAIN_BANS")."</td><td><input type='text' name='mail_domain' size='40' /></td></tr>\n");
	print("<tr><td align='right'>".T_("ADDCOMMENT")."</td><td><input type='text' name='comment' size='40' /></td></tr>\n");
	print("<tr><td colspan='2' align='center'><input type='submit' value='".T_("ADD_BAN")."' /></td></tr>\n");
	print("\n</table></form>\n<br />");
	//}

	$res2 = SQL_Query_exec("SELECT count(id) FROM email_bans");
	$row = mysqli_fetch_array($res2);
	$count = $row[0];
	$perpage = 40;list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, basename(__FILE__)."?action=emailbans&amp;");
	print("<br /><b>".T_("EMAIL_BANS")." ($count)</b>\n");

	if ($count == 0){
		print("<p align='center'><b>".T_("NOTHING_FOUND")."</b></p><br />\n");
	}else{
		echo $pagertop;
		print("<table border='0' cellspacing='0' cellpadding='5' width='90%' align='center' class='table_table'>\n");
		print("<tr><th class='table_head'>Added</th><th class='table_head'>Mail Address Or Domain</th><th class='table_head'>Banned By</th><th class='table_head'>Comment</th><th class='table_head'>Remove</th></tr>\n");
		$res = SQL_Query_exec("SELECT * FROM email_bans ORDER BY added DESC $limit");

		while ($arr = mysqli_fetch_assoc($res)){
			$r2 = SQL_Query_exec("SELECT username FROM users WHERE id=$arr[userid]");
			$a2 = mysqli_fetch_assoc($r2);

			$r4 = SQL_Query_exec("SELECT username,id FROM users WHERE id=$arr[addedby]");
			$a4 = mysqli_fetch_assoc($r4);
			print("<tr><td class='table_col1'>".utc_to_tz($arr['added'])."</td><td align='left' class='table_col2'>$arr[mail_domain]</td><td align='left' class='table_col1'><a href='account-details.php?id=$a4[id]'>$a4[username]"."</a></td><td align='left' class='table_col2'>$arr[comment]</td><td class='table_col1'><a href='admincp.php?action=emailbans&amp;remove=$arr[id]'>Remove</a></td></tr>\n");
		}

		print("</table>\n");

		echo $pagerbottom;
		echo "<br />";
	}
	end_frame();
	stdfoot();
}

if ($action=="polls" && $do=="view"){
	stdhead(T_("POLLS_MANAGEMENT"));
	navmenu();
	begin_frame(T_("POLLS_MANAGEMENT"));

	echo "<center><a href='admincp.php?action=polls&amp;do=add'>Add New Poll</a>";
	echo "<a href='admincp.php?action=polls&amp;do=results'>View Poll Results</a></center>";

	echo "<br /><br /><b>Polls</b> (Top poll is current)<br />";

	$query = SQL_Query_exec("SELECT id,question,added FROM polls ORDER BY added DESC");

	while($row = mysqli_fetch_assoc($query)){
		echo "<a href='admincp.php?action=polls&amp;do=add&amp;subact=edit&amp;pollid=$row[id]'>".stripslashes($row["question"])."</a> - ".utc_to_tz($row['added'])." - <a href='admincp.php?action=polls&amp;do=delete&amp;id=$row[id]'>Delete</a><br />\n\n";
	}

	end_frame();

	stdfoot();
}


/////////////
if ($action=="polls" && $do=="results"){
	stdhead("Polls");
	navmenu();
	begin_frame("Results");
	echo "<table class=\"table_table\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"95%\">";
	echo '<tr>';
	echo '<th class="table_head">Username</th>';
	echo '<th class="table_head">Question</th>';
	echo '<th class="table_head">Voted</th>';
	echo '</tr>';

	$poll = SQL_Query_exec("SELECT * FROM pollanswers ORDER BY pollid DESC");

	while ($res = mysqli_fetch_assoc($poll)) {
		$user = mysqli_fetch_assoc(SQL_Query_exec("SELECT username,id FROM users WHERE id = '".$res['userid']."'"));
		$option = "option".$res["selection"];
		if ($res["selection"] < 255) {
			$vote = mysqli_fetch_assoc(SQL_Query_exec("SELECT ".$option." FROM polls WHERE id = '".$res['pollid']."'"));
		} else {
			$vote["option255"] = "Blank vote";
		}
		$sond = mysqli_fetch_assoc(SQL_Query_exec("SELECT question FROM polls WHERE id = '".$res['pollid']."'"));
		
		echo '<tr>';
		echo '<td class="table_col1" align="left"><b>';
		echo '<a href="account-details.php?id='.$user["id"].'">';
		echo '&nbsp;&nbsp;'.$user['username'];
		echo '</a>';
		echo '</b></td>';
		echo '<td class="table_col2" align="center">';
		echo '&nbsp;&nbsp;'.$sond['question'];
		echo '</td>';
		echo '<td class="table_col1" align="center">';
		echo $vote["$option"];
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
	end_frame();
	stdfoot();
}


if ($action=="polls" && $do=="delete"){
	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),sprintf(T_("CP_NEWS_INVAILD_ITEM_ID"), $newsid),1);

	SQL_Query_exec("DELETE FROM polls WHERE id=$id");
	SQL_Query_exec("DELETE FROM pollanswers WHERE  pollid=$id");
	
	show_error_msg(T_("COMPLETED"),"Poll and answers deleted",1);
}

if ($action=="polls" && $do=="add"){
	stdhead("Polls");
	navmenu();

	$pollid = (int)$_GET["pollid"];

	if ($_GET["subact"] == "edit"){
		$res = SQL_Query_exec("SELECT * FROM polls WHERE id = $pollid");
		$poll = mysqli_fetch_array($res);
	}
                                
	begin_frame("Polls");
	?>                                                
    <form method="post" action="admincp.php?action=polls&amp;do=save"> 
	<table border="0" cellspacing="0" class="table_table" align="center">
    <tr><td class="table_col1">Question <font class="error">*</font></td><td class="table_col2" align="left"><input name="question" size="60" maxlength="255" value="<?php echo $poll['question']; ?>" /></td></tr>
    <tr><td class="table_col1">Option 1 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option0" size="60" maxlength="40" value="<?php echo $poll['option0']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 2 <font class="error">*</font></td><td class="table_col2" align="left"><input name="option1" size="60" maxlength="40" value="<?php echo $poll['option1']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 3</td><td class="table_col2" align="left"><input name="option2" size="60" maxlength="40" value="<?php echo $poll['option2']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 4</td><td class="table_col2" align="left"><input name="option3" size="60" maxlength="40" value="<?php echo $poll['option3']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 5</td><td class="table_col2" align="left"><input name="option4" size="60" maxlength="40" value="<?php echo $poll['option4']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 6</td><td class="table_col2" align="left"><input name="option5" size="60" maxlength="40" value="<?php echo $poll['option5']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 7</td><td class="table_col2" align="left"><input name="option6" size="60" maxlength="40" value="<?php echo $poll['option6']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 8</td><td class="table_col2" align="left"><input name="option7" size="60" maxlength="40" value="<?php echo $poll['option7']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 9</td><td class="table_col2" align="left"><input name="option8" size="60" maxlength="40" value="<?php echo $poll['option8']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 10</td><td class="table_col2" align="left"><input name="option9" size="60" maxlength="40" value="<?php echo $poll['option9']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 11</td><td class="table_col2" align="left"><input name="option10" size="60" maxlength="40" value="<?php echo $poll['option10']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 12</td><td class="table_col2" align="left"><input name="option11" size="60" maxlength="40" value="<?php echo $poll['option11']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 13</td><td class="table_col2" align="left"><input name="option12" size="60" maxlength="40" value="<?php echo $poll['option12']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 14</td><td class="table_col2" align="left"><input name="option13" size="60" maxlength="40" value="<?php echo $poll['option13']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 15</td><td class="table_col2" align="left"><input name="option14" size="60" maxlength="40" value="<?php echo $poll['option14']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 16</td><td class="table_col2" align="left"><input name="option15" size="60" maxlength="40" value="<?php echo $poll['option15']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 17</td><td class="table_col2" align="left"><input name="option16" size="60" maxlength="40" value="<?php echo $poll['option16']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 18</td><td class="table_col2" align="left"><input name="option17" size="60" maxlength="40" value="<?php echo $poll['option17']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 19</td><td class="table_col2" align="left"><input name="option18" size="60" maxlength="40" value="<?php echo $poll['option18']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Option 20</td class="table_col2"><td class="table_col2" align="left"><input name="option19" size="60" maxlength="40" value="<?php echo $poll['option19']; ?>" /><br /></td></tr>
    <tr><td class="table_col1">Sort</td><td class="table_col2">
    <input type="radio" name="sort" value="yes" <?php echo $poll["sort"] != "no" ? " checked='checked'" : "" ?> />Yes
    <input type="radio" name="sort" value="no" <?php echo $poll["sort"] == "no" ? " checked='checked'" : "" ?> /> No
    </td></tr>
    <tr><td class="table_head" colspan="2" align="center"><input type="submit" value="<?php echo $pollid ? "Edit poll": "Create poll"; ?>" /></td></tr>
    </table>
    <p><font class="error">*</font> required</p>
    <input type="hidden" name="pollid" value="<?php echo $poll["id"]?>" />
    <input type="hidden" name="subact" value="<?php echo $pollid?'edit':'create'?>" />
    </form>
	<?php
	end_frame();
	stdfoot();
}
if ($action == "polls" && $do == "save") {
    $subact = filter_input(INPUT_POST, 'subact', FILTER_SANITIZE_STRING);
    $pollid = (int)$_POST["pollid"];
    $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_STRING);
    $option0 = filter_input(INPUT_POST, 'option0', FILTER_SANITIZE_STRING);
    $option1 = filter_input(INPUT_POST, 'option1', FILTER_SANITIZE_STRING);
    $option2 = filter_input(INPUT_POST, 'option2', FILTER_SANITIZE_STRING);
    $option3 = filter_input(INPUT_POST, 'option3', FILTER_SANITIZE_STRING);
    $option4 = filter_input(INPUT_POST, 'option4', FILTER_SANITIZE_STRING);
    $option5 = filter_input(INPUT_POST, 'option5', FILTER_SANITIZE_STRING);
    $option6 = filter_input(INPUT_POST, 'option6', FILTER_SANITIZE_STRING);
    $option7 = filter_input(INPUT_POST, 'option7', FILTER_SANITIZE_STRING);
    $option8 = filter_input(INPUT_POST, 'option8', FILTER_SANITIZE_STRING);
    $option9 = filter_input(INPUT_POST, 'option9', FILTER_SANITIZE_STRING);
    $option10 = filter_input(INPUT_POST, 'option10', FILTER_SANITIZE_STRING);
    $option11 = filter_input(INPUT_POST, 'option11', FILTER_SANITIZE_STRING);
    $option12 = filter_input(INPUT_POST, 'option12', FILTER_SANITIZE_STRING);
    $option13 = filter_input(INPUT_POST, 'option13', FILTER_SANITIZE_STRING);
    $option14 = filter_input(INPUT_POST, 'option14', FILTER_SANITIZE_STRING);
    $option15 = filter_input(INPUT_POST, 'option15', FILTER_SANITIZE_STRING);
    $option16 = filter_input(INPUT_POST, 'option16', FILTER_SANITIZE_STRING);
    $option17 = filter_input(INPUT_POST, 'option17', FILTER_SANITIZE_STRING);
    $option18 = filter_input(INPUT_POST, 'option18', FILTER_SANITIZE_STRING);
    $option19 = filter_input(INPUT_POST, 'option19', FILTER_SANITIZE_STRING);
    $sort = $_POST["sort"];
// if ($action=="polls" && $do=="save"){

// 	$subact = $_POST["subact"];
// 	$pollid = (int)$_POST["pollid"];

// 	$question = $_POST["question"];
// 	$option0 = $_POST["option0"];
// 	$option1 = $_POST["option1"];
// 	$option2 = $_POST["option2"];
// 	$option3 = $_POST["option3"];
// 	$option4 = $_POST["option4"];
// 	$option5 = $_POST["option5"];
// 	$option6 = $_POST["option6"];
// 	$option7 = $_POST["option7"];
// 	$option8 = $_POST["option8"];
// 	$option9 = $_POST["option9"];
// 	$option10 = $_POST["option10"];
// 	$option11 = $_POST["option11"];
// 	$option12 = $_POST["option12"];
// 	$option13 = $_POST["option13"];
// 	$option14 = $_POST["option14"];
// 	$option15 = $_POST["option15"];
// 	$option16 = $_POST["option16"];
// 	$option17 = $_POST["option17"];
// 	$option18 = $_POST["option18"];
// 	$option19 = $_POST["option19"];
// //	$sort = (int)$_POST["sort"];
// 	$sort = $_POST["sort"]; //removed INT

	if (!$question || !$option0 || !$option1)
		show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA")."!", 1);

	if ($subact == "edit"){

		if (!is_valid_id($pollid))
			show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

		SQL_Query_exec("UPDATE polls SET " .
		"question = " . sqlesc($question) . ", " .
		"option0 = " . sqlesc($option0) . ", " .
		"option1 = " . sqlesc($option1) . ", " .
		"option2 = " . sqlesc($option2) . ", " .
		"option3 = " . sqlesc($option3) . ", " .
		"option4 = " . sqlesc($option4) . ", " .
		"option5 = " . sqlesc($option5) . ", " .
		"option6 = " . sqlesc($option6) . ", " .
		"option7 = " . sqlesc($option7) . ", " .
		"option8 = " . sqlesc($option8) . ", " .
		"option9 = " . sqlesc($option9) . ", " .
		"option10 = " . sqlesc($option10) . ", " .
		"option11 = " . sqlesc($option11) . ", " .
		"option12 = " . sqlesc($option12) . ", " .
		"option13 = " . sqlesc($option13) . ", " .
		"option14 = " . sqlesc($option14) . ", " .
		"option15 = " . sqlesc($option15) . ", " .
		"option16 = " . sqlesc($option16) . ", " .
		"option17 = " . sqlesc($option17) . ", " .
		"option18 = " . sqlesc($option18) . ", " .
		"option19 = " . sqlesc($option19) . ", " .
		"sort = " . sqlesc($sort) . " " .
    "WHERE id = $pollid");
	}else{
  	SQL_Query_exec("INSERT INTO polls VALUES(0" .
		", '" . get_date_time() . "'" .
    ", " . sqlesc($question) .
    ", " . sqlesc($option0) .
    ", " . sqlesc($option1) .
    ", " . sqlesc($option2) .
    ", " . sqlesc($option3) .
    ", " . sqlesc($option4) .
    ", " . sqlesc($option5) .
    ", " . sqlesc($option6) .
    ", " . sqlesc($option7) .
    ", " . sqlesc($option8) .
    ", " . sqlesc($option9) .
 		", " . sqlesc($option10) .
		", " . sqlesc($option11) .
		", " . sqlesc($option12) .
		", " . sqlesc($option13) .
		", " . sqlesc($option14) .
		", " . sqlesc($option15) .
		", " . sqlesc($option16) .
		", " . sqlesc($option17) .
		", " . sqlesc($option18) .
		", " . sqlesc($option19) . 
    ", " . sqlesc($sort) .
  	")");
	}

	show_error_msg("OK","Poll Updates ".T_("COMPLETE"), 1);
}
//////////////////////////////////////////show admin CP sql backup files
if ($action=="backups" && $do=="delete"){
   $filename = $_GET["filename"];
//   stdhead("Delete Backup");
//   begin_frame("Delete Backup");
   $delete_error = false;
   if (!unlink('backups/'.$filename)) { $delete_error = true; }
   if (!unlink('backups/'.$filename.'.gz')) { $delete_error = true; }
   header("Refresh: 3 ;url=admincp.php?action=backups");
//   end_frame();
stdhead();
show_error_msg(T_("SUCCESS"), "Selected Backup Files deleted", 0);
    if ($delete_error) {
          echo("<br><center><b>Has encountered a problem during the deletion</b></center><br><br><br>");
   } else {
          echo("<br><center><b>$filename<br><br><br>DELETED !!!</b></center><br><br><br>");
   }
          echo("<center>You'll be redirected in about 3 secs. If not, click <a href='admincp.php?action=backups'>here</a></center>");
   stdfoot();
}
if ($action=="backups"){
  stdhead("Backups");
  navmenu();
  begin_frame("Backups");
  $Namebk = array();
  $Sizebk = array();
  // CHECK ALL SQL FILES INTO THE BACKUPS FOLDER AND CREATE AN LIST
  $dir = opendir("backups/");
  while ($dir && ($file = readdir($dir)) !== false)
  {
            $ext = explode('.',$file);
            if ( $ext[1] == "sql")
            {
                  if ( $ext[2] != "gz" )
                  {
                            $Namebk[] = $ext[0];
                            $Sizebk[] = round( filesize("backups/".$file) / 1024, 2);
                  }
            }
  }
  // SORT THE LIST
  sort($Namebk);
  // OPEN TABLE
  echo ("<br/><br/><table style='text-align:center;' width='100%'>");
  // TABLE HEADER
  echo ("<tr bgcolor='#333333'>"); // Start table row
  echo ("<th scope='colgroup'><b>Date</b></th>"); // Date
  echo ("<th scope='colgroup'><b>Time</b></th>"); // Time
  echo ("<th scope='colgroup'><b>Size</b></th>"); // Size
  echo ("<th scope='colgroup'><b>Hash</b></th>"); // Hash
  echo ("<th scope='colgroup'><b>Download</b></th>"); // Download
  echo ("<th></th>"); // Delete
  echo ("</tr>"); // End table row
  // TABLE ROWS
  for( $x = count($Namebk) - 1; $x >= 0; $x--)
  {
            $data = explode('_', $Namebk[$x]);
            echo ("<tr bgcolor='#CCCCCC'>"); // Start table row
            echo ("<td>".$data[1]."</td>"); // Date
            echo ("<td>".$data[2]."</td>"); // Time
            echo ("<td>".$Sizebk[$x]." KByte</td>"); // Size
            echo ("<td>".$data[3]."</td>"); // Hash
            echo ("<td><a href='".$site_config['SITEURL']."/backups/".$Namebk[$x].".sql'>SQL</a> - <a href='".$site_config['SITEURL']."/backups/".$Namebk[$x].".sql.gz'>GZ</a></td>"); // Download
            echo ("<td><a href='".$site_config['SITEURL']."/admincp.php?action=backups&amp;do=delete&amp;filename=".$Namebk[$x].".sql'><img src='images/delete.png'></a></td>"); // Delete
            echo ("</tr>"); // End table row
  }
  // CLOSE TABLE
  echo ("</table>");
  // CREATE BACKUP LINK
  echo ("<br><br><center><a href='backup-database.php'>Backup Database</a> (or create a CRON task on ".$site_config["SITEURL"]."/backup-database.php)</center>");
  end_frame();
  stdfoot();
}
////////////////////////////////////////// end show admin CP sql backup files
if ($action=="forceclean"){
	$now = gmtime();
	SQL_Query_exec("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
	require_once("backend/cleanup.php");
	do_cleanup();
    
    autolink('admincp.php', T_("FORCE_CLEAN_COMPLETED"));
}

if ($action=="torrentlangs" && $do=="view"){
	stdhead(T_("TORRENT_LANGUAGES"));
	navmenu();
	begin_frame(T_("TORRENT_LANGUAGES"));
	echo "<center><a href='admincp.php?action=torrentlangs&amp;do=add'><b>Add New Language</b></a></center><br />";

	print("<i>Please note that language image is optional</i><br /><br />");

	echo("<center><table width='650' class='table_table'><tr>");
	echo("<th width='10' class='table_head'><b>Sort</b></th><th class='table_head'><b>".T_("NAME")."</b></th><th class='table_head'><b>Image</b></th><th width='30' class='table_head'></th></tr>");
	$query = "SELECT * FROM torrentlang ORDER BY sort_index ASC";
	$sql = SQL_Query_exec($query);
	while ($row = mysqli_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];

		print("<tr><td class='table_col1' align='center'>$priority</td><td class='table_col2'>$name</td><td class='table_col1' width='50' align='center'>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class='table_col1'><a href='admincp.php?action=torrentlangs&amp;do=edit&amp;id=$id'>[EDIT]</a> <a href='admincp.php?action=torrentlangs&amp;do=delete&amp;id=$id'>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
}

if ($action=="backups"){
	stdhead("Backups");
	navmenu();
	begin_frame("Backups");
	echo "<a href='backup-database.php'>Backup Database</a> (or create a CRON task on ".$site_config["SITEURL"]."/backup-database.php)";
	end_frame();
	stdfoot();
}

if ($action=="forceclean"){
	$now = gmtime();
	SQL_Query_exec("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
	require_once("backend/cleanup.php");
	do_cleanup();
    
    autolink('admincp.php', T_("FORCE_CLEAN_COMPLETED"));
}

if ($action=="torrentlangs" && $do=="view"){
	stdhead(T_("TORRENT_LANGUAGES"));
	navmenu();
	begin_frame(T_("TORRENT_LANGUAGES"));
	echo "<center><a href='admincp.php?action=torrentlangs&amp;do=add'><b>Add New Language</b></a></center><br />";

	print("<i>Please note that language image is optional</i><br /><br />");

	echo("<center><table width='650' class='table_table'><tr>");
	echo("<th width='10' class='table_head'><b>Sort</b></th><th class='table_head'><b>".T_("NAME")."</b></th><th class='table_head'><b>Image</b></th><th width='30' class='table_head'></th></tr>");
	$query = "SELECT * FROM torrentlang ORDER BY sort_index ASC";
	$sql = SQL_Query_exec($query);
	while ($row = mysqli_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];

		print("<tr><td class='table_col1' align='center'>$priority</td><td class='table_col2'>$name</td><td class='table_col1' width='50' align='center'>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\" src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class='table_col1'><a href='admincp.php?action=torrentlangs&amp;do=edit&amp;id=$id'>[EDIT]</a> <a href='admincp.php?action=torrentlangs&amp;do=delete&amp;id=$id'>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
}


if ($action=="torrentlangs" && $do=="edit"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

	$res = SQL_Query_exec("SELECT * FROM torrentlang WHERE id=$id");

	if (mysqli_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No Language with ID $id.",1);

	$arr = mysqli_fetch_array($res);

	if ($_GET["save"] == '1'){
  	
		$name = $_POST['name'];
		if ($name == "")
			show_error_msg(T_("ERROR"), "Language cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

		SQL_Query_exec("UPDATE torrentlang SET name=$name, sort_index=$sort_index, image=$image WHERE id=$id");

		show_error_msg(T_("COMPLETED"),"Language was edited successfully.",0);

	} else {
		begin_frame("Edit Language");
		print("<form method='post' action='?action=torrentlangs&amp;do=edit&amp;id=$id&amp;save=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Name: </b><input type='text' name='name' value=\"".$arr['name']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Sort: </b><input type='text' name='sort_index' value=\"".$arr['sort_index']."\" /></td></tr>\n");
		print("<tr><td align='left'><b>Image: </b><input type='text' name='image' value=\"".$arr['image']."\" /> single filename</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
        end_frame();
	}
	stdfoot();
}

if ($action=="torrentlangs" && $do=="delete"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	$id = (int)$_GET["id"];

	if ($_GET["sure"] == '1'){

		if (!is_valid_id($id))
			show_error_msg(T_("ERROR"),"Invalid Language item ID",1);

		$newlangid = (int) $_POST["newlangid"];

		SQL_Query_exec("UPDATE torrents SET torrentlang=$newlangid WHERE torrentlang=$id"); //move torrents to a new cat

		SQL_Query_exec("DELETE FROM torrentlang WHERE id=$id"); //delete old cat
		
		show_error_msg(T_("COMPLETED"),"Language Deleted OK",1);

	}else{
		begin_frame("Delete Language");
		print("<form method='post' action='?action=torrentlangs&amp;do=delete&amp;id=$id&amp;sure=1'>\n");
		print("<center><table border='0' cellspacing='0' cellpadding='5'>\n");
		print("<tr><td align='left'><b>Language ID to move all Languages To: </b><input type='text' name='newlangid' /> (Lang ID)</td></tr>\n");
		print("<tr><td align='center'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");
		print("</table></center>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="torrentlangs" && $do=="takeadd"){
  		$name = $_POST['name'];
		if ($name == "")
    		show_error_msg(T_("ERROR"), "Name cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

	SQL_Query_exec("INSERT INTO torrentlang (name, sort_index, image) VALUES ($name, $sort_index, $image)");

	if (mysqli_affected_rows($GLOBALS["DBconnector"]) == 1)
		show_error_msg(T_("COMPLETED"),"Language was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add Language",1);
}

if ($action=="torrentlangs" && $do=="add"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	begin_frame("Add Language");
	print("<center><form method='post' action='admincp.php'>\n");
	print("<input type='hidden' name='action' value='torrentlangs' />\n");
	print("<input type='hidden' name='do' value='takeadd' />\n");

	print("<table border='0' cellspacing='0' cellpadding='5'>\n");

	print("<tr><td align='left'><b>Name:</b> <input type='text' name='name' /></td></tr>\n");
	print("<tr><td align='left'><b>Sort:</b> <input type='text' name='sort_index' /></td></tr>\n");
	print("<tr><td align='left'><b>Image:</b> <input type='text' name='image' /></td></tr>\n");

	print("<tr><td colspan='2'><input type='submit' value='".T_("SUBMIT")."' /></td></tr>\n");

	print("</table></form><br /><br /></center>\n");
	end_frame();
	stdfoot();
}

if ($action=="avatars"){
	stdhead("Avatar Log");
	navmenu();

	begin_frame("Avatar Log");

	$query = SQL_Query_exec("SELECT count(*) FROM users WHERE enabled='yes' AND avatar !=''");
	$count = mysqli_fetch_row($query);
	$count = $count[0];

	list($pagertop, $pagerbottom, $limit) = pager(50, $count, 'admincp.php?action=avatars&amp;');
	echo ($pagertop);
	?>
	<table border="0" class="table_table" align="center">
	<tr>
	<th class="table_head"><?php echo T_("USER")?></th>
	<th class="table_head">Avatar</th>
	</tr><?php

	$query = "SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit";
	$res = SQL_Query_exec($query);

	while($arr = mysqli_fetch_assoc($res)){
			echo("<tr><td class='table_col1'><b><a href=\"account-details.php?id=" . $arr['id'] . "\">" . $arr['username'] . "</a></b></td><td class='table_col2'>");

			if (!$arr['avatar'])
				echo "<img width=\"80\" src='images/default_avatar.png' alt='' /></td></tr>";
			else
				echo "<img width=\"80\" src=\"".htmlspecialchars($arr["avatar"])."\" alt='' /></td></tr>";
	}
	?>
	</table>
	<?php
	echo ($pagerbottom);
	end_frame();
	stdfoot();
}

if ($action=="freetorrents"){
    
    /*
    * Todo:
    *  Optimize Query show freeleech ONLY!
    */
    
	stdhead("Free Leech ".T_("TORRENT_MANAGEMENT"));
	navmenu();

	$search = trim($_GET['search']);

	if ($search != '' ){
		$whereand = "AND name LIKE " . sqlesc("%$search%") . "";
	}

	$res2 = SQL_Query_exec("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand");
	$row = mysqli_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=freetorrents&amp;");

	begin_frame(T_("TORRENTS_FREE_LEECH"));

	print("<center><form method='get' action='?'>\n");
	print("<input type='hidden' name='action' value='freetorrents' />\n");
	print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
	print("<input type='submit' value='Search' />\n");
	print("</form></center>\n");

	echo $pagertop;
	?>
	<table align="center" cellpadding="0" cellspacing="0" class="table_table" width="100%" border="0">
	<tr> 
        <th class="table_head"><?php echo T_("NAME"); ?></th>
        <th class="table_head"><?php echo T_("VISIBLE"); ?></th>
        <th class="table_head"><?php echo T_("BANNED"); ?></th>
        <th class="table_head"><?php echo T_("SEEDERS"); ?></th>
        <th class="table_head"><?php echo T_("LEECHERS"); ?></th>
        <th class="table_head"><?php echo T_("EDIT"); ?></th>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit";
	$resqq = SQL_Query_exec($rqq);

	while ($row = mysqli_fetch_array($resqq)){
		
		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class='table_col1'>" . $smallname . "</td><td class='table_col2'>$row[visible]</td><td class='table_col1'>$row[banned]</td><td class='table_col2'>".number_format($row["seeders"])."</td><td class='table_col1'>".number_format($row["leechers"])."</td><td class='table_col2'><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size='1' face='verdana'>EDIT</font></a></td></tr>\n";
	}

	echo "</table>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}

if ($action=="bannedtorrents"){
	stdhead("Banned Torrents");
	navmenu();
		
	$res2 = SQL_Query_exec("SELECT COUNT(*) FROM torrents WHERE banned='yes'");
	$row = mysqli_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=bannedtorrents&amp;");

	begin_frame("Banned ".T_("TORRENT_MANAGEMENT"));

	print("<center><form method='get' action='?'>\n");
	print("<input type='hidden' name='action' value='bannedtorrents' />\n");
	print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
	print("<input type='submit' value='Search' />\n");
	print("</form></center>\n");

	echo $pagertop;
	?>
	<table align="center" cellpadding="0" cellspacing="0" class="table_table" width="100%" border="0">
	<tr>
	<th class="table_head"><?php echo T_("NAME"); ?></th>
	<th class="table_head">Visible</th>
	<th class="table_head">Seeders</th>
	<th class="table_head">Leechers</th>
	<th class="table_head">External?</th>
	<th class="table_head">Edit?</th>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned='yes' ORDER BY name";
	$resqq = SQL_Query_exec($rqq);

	while ($row = mysqli_fetch_assoc($resqq)){

		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class='table_col1'>" . $smallname . "</td><td class='table_col2'>$row[visible]</td><td class='table_col1'>".number_format($row["seeders"])."</td><td class='table_col2'>".number_format($row["leechers"])."</td><td class='table_col1'>$row[external]</td><td class='table_col2'><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size='1' face='verdana'>EDIT</font></a></td></tr>\n";
	}

	echo "</table>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}


if ($action=="masspm"){
	stdhead("Mass Private Message");
	navmenu();

    # Tidy Up...
    

	//send pm
	if ($_GET["send"] == '1'){

		$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

		$dt = sqlesc(get_date_time());
		$msg = $_POST['msg'];
        $subject = $_POST["subject"];

		if (!$msg)
			show_error_msg(T_("ERROR"),"Please Enter Something!",1);

		$updateset = array_map("intval", $_POST['clases']);

		$query = SQL_Query_exec("SELECT id FROM users WHERE class IN (".implode(",", $updateset).") AND enabled = 'yes' AND status = 'confirmed'");
		while($dat=mysqli_fetch_assoc($query)){
			SQL_Query_exec("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES ($sender_id, $dat[id], '" . get_date_time() . "', " . sqlesc($msg) .", ".sqlesc($subject).")");
		}

		write_log("A Mass PM was sent by ($CURUSER[username])");
		show_error_msg(T_("COMPLETE"), "Mass PM Sent",1);
		die;
	}

	begin_frame("Mass Private Message");
    
    print("<form name='masspm' method='post' action='admincp.php?action=masspm&amp;send=1'>\n"); 
	print("<table border='0' cellspacing='0' cellpadding='5' align='center' width='90%'>\n");
	

	$query = "SELECT group_id, level FROM groups";
	$res = SQL_Query_exec($query);

    echo "<tr><td><b>Send to:</b></td></tr>";
	while ($row = mysqli_fetch_array($res)){

		echo "<tr><td><input type='checkbox' name='clases[]' value='$row[group_id]' /> $row[level]<br /></td></tr>\n";
	}
                           
	?>   
    <tr>
    <td><b>Subject:</b><br /><input type="text" name="subject" size="30" /></td>
    </tr>
	<tr>
	<td><br /><b>Message: </b><br /> <?php print textbbcode("masspm", "msg"); ?></td>
	</tr>
    
	<tr>
	<td><b><?php echo T_("SENDER");?></b>
	<?php echo $CURUSER['username']?> <input name="sender" type="radio" value="self" checked="checked" />
	System <input name="sender" type="radio" value="system" /></td>
	</tr>

	<tr>
	<td><input type="submit" value="Send" /></td>
	</tr>
	</table></form>
	<?php
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="view"){
	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();

	begin_frame(T_("SITE_RULES_EDITOR"));

	$res = SQL_Query_exec("SELECT * FROM rules ORDER BY id");

	print("<center><a href='admincp.php?action=rules&amp;do=addsect'>Add New Rules Section</a></center><br />\n");	

	while ($arr=mysqli_fetch_assoc($res)){
		
		print("<table width='100%' cellspacing='0' class='table_table'><tr>");
        print("<th class='table_head'>".$arr["title"]."</th>");
        print("</tr><tr><td class='table_col1'>");
        print("<form method='post' action='admincp.php?action=rules&amp;do=edit'>");
		print(format_comment($arr["text"]));
		print("</td></tr><tr><td class='table_head' align='center'><input type='hidden' value='$arr[id]' name='id' /><input type='submit' value='Edit' /></form>");
		print("</td>");
        print("</tr></table>");
        print("<br />");
	}
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="edit"){

	if ($_GET["save"]=="1"){
		$id = (int)$_POST["id"];
		$title = sqlesc($_POST["title"]);
		$text = sqlesc($_POST["text"]);
		$public = sqlesc($_POST["public"]);
		$class = sqlesc($_POST["class"]);
		SQL_Query_exec("update rules set title=$title, text=$text, public=$public, class=$class where id=$id");
		write_log("Rules have been changed by ($CURUSER[username])");
		show_error_msg(T_("COMPLETE"), "Rules edited ok<br /><br /><a href='admincp.php?action=rules&amp;do=view'>Back To Rules</a>",1);
		die;
	}


	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();
	
	begin_frame("Edit Rule Section");
	$id = (int)$_POST["id"];
	$res = @mysqli_fetch_array(@SQL_Query_exec("select * from rules where id='$id'"));

	print("<form method=\"post\" action=\"admincp.php?action=rules&amp;do=edit&amp;save=1\">");
	print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
	print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\" value=\"$res[title]\" /></td></tr>\n");
	print("<tr><td style=\"vertical-align: top;\">Rules:</td><td><textarea cols=\"60\" rows=\"15\" name=\"text\">" . stripslashes($res["text"]) . "</textarea><br />NOTE: Remember that BB can be used (NO HTML)</td></tr>\n");

	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" ".($res["public"]=="yes"?"checked='checked'":"")." />For everybody<input type=\"radio\" name='public' value=\"no\" ".($res["public"]=="no"?"checked='checked'":"")." />Members Only (Min User Class: <input type=\"text\" name='class' value=\"$res[class]\" size=\"1\" />)</td></tr>\n");
	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" value=\"$res[id]\" name=\"id\" /><input type=\"submit\" value=\"".T_("SAVE")."\" style=\"width: 60px;\" /></td></tr>\n");
	print("</table></form>");
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="addsect"){

	if ($_GET["save"]=="1"){
		$title = sqlesc($_POST["title"]);
		$text = sqlesc($_POST["text"]);
		$public = sqlesc($_POST["public"]);
		$class = sqlesc($_POST["class"]);
		SQL_Query_exec("insert into rules (title, text, public, class) values($title, $text, $public, $class)");
		show_error_msg(T_("COMPLETE"), "New Section Added<br /><br /><a href='admincp.php?action=rules&amp;do=view'>Back To Rules</a>",1);
		die();
	}
	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();
	begin_frame(T_("ADD_NEW_RULES_SECTION"));
	print("<form method=\"post\" action=\"admincp.php?action=rules&amp;do=addsect&amp;save=1\">");
	print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
	print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\" /></td></tr>\n");
	print("<tr><td style=\"vertical-align: top;\">Rules:</td><td><textarea cols=\"60\" rows=\"15\" name=\"text\"></textarea><br />\n");
	print("<br />NOTE: Remember that BB can be used (NO HTML)</td></tr>\n");

	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" checked=\"checked\" />For everybody<input type=\"radio\" name='public' value=\"no\" />&nbsp;Members Only - (Min User Class: <input type=\"text\" name='class' value=\"0\" size=\"1\" />)</td></tr>\n");
	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
	print("</table></form>");
	end_frame();
	stdfoot();
}

#======================================================================#
# Configuration Panel by djhowarth //thisEach left alone here as this is a defunct action and this does not work and is unsafe. Left for reference.
#======================================================================#
 if ($action == "settings")
 {          
     if ($do == 'save')
     {                             
         #$file = new SplFileObject('backend/config.php', 'w');                                
         #$file->fwrite('<?php ' . "\r\n\r\n" . '$site_config = ' . var_export((array)$site_config, true) . ';');
         write_log( '<pre>', print_r($_POST, true), '</pre>' );
         die;
     }                               
     
     stdhead("Site Configuration");
     navmenu();
     
     begin_frame("Site Configuration - Incompleted!");
     ?>
     
     <!-- CSS to be moved... -->
     <style type="text/css">
     #sortable-list
     {
         padding: 0;
     }
     li.sortme
     {
         padding: 4px 8px; 
         color: #000; 
         cursor: move; 
         list-style: none; 
         width: 100px; 
         background: #ddd; 
         margin: 10px 0; 
         border: 1px solid #999;
     }
     #message-box
     {
         background: #fffea1; 
         border: 2px solid #fc0; 
         padding:4px 8px; 
         margin: 0 0 14px 0; 
         width: 500px; 
     }
     </style>
     
     <!-- JS to be moved... -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.js"></script>
    <script type="text/javascript">
/* when the DOM is ready */
jQuery(document).ready(function() {
  /* grab important elements */
  var sortInput = jQuery('#sort_order');
  var submit = jQuery('#autoSubmit');
  var messageBox = jQuery('#message-box');
  var list = jQuery('#sortable-list');
  /* create requesting function to avoid duplicate code */
  var request = function() {
    jQuery.ajax({
      beforeSend: function() {
        messageBox.text('Updating the sort order in the database.');
      },
      complete: function() {
        messageBox.text('Database has been updated.');
      },
      data: 'sort_order=' + sortInput[0].value + '&ajax=' + submit[0].checked + '&do_submit=1&byajax=1', //need [0]?
      type: 'post',
      url: '<?php echo $_SERVER["REQUEST_URI"]; ?>'
    });
  };
  /* worker function */
  var fnSubmit = function(save) {
    var sortOrder = [];
    list.children('li').thisEach(function(){
      sortOrder.push(jQuery(this).data('id'));
    });
    sortInput.val(sortOrder.join(','));
    console.log(sortInput.val());
    if(save) {
      request();
    }
  };
  /* store values */
  list.children('li').thisEach(function() {
    var li = jQuery(this);
    li.data('id',li.attr('title')).attr('title','');
  });
  /* sortables */
  list.sortable({
    opacity: 0.7,
    update: function() {
      fnSubmit(submit[0].checked);
    }
  });
  list.disableSelection();
  /* ajax form submission */
  jQuery('#dd-form').bind('submit',function(e) {
    if(e) e.preventDefault();
    fnSubmit(true);
  });
});
     </script>

     <form id="dd-form" method="post" action="admincp.php?action=settings&amp;do=save">
     <input type="hidden" name="sort_order" id="sort_order" value="<?php echo $site_config['torrenttable_columns']; ?>" />
     <table border="0" width="100%" cellpadding="3" cellspacing="3">
 
     <!-- File Path(s) -->
     <tr>
          <td colspan="2"><b>File Storage Paths:</b><br />&#9492; <small>Must be CHMOD 755 and absolute paths.</small></td>
     </tr>
     <tr>
          <td>Path to directory where .torrents will be stored:</td>
          <td><input type="text" name="site_config[torrent_dir]" value="<?php echo $site_config['torrent_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where .nfo's will be stored:</td>
          <td><input type="text" name="site_config[nfo_dir]" value="<?php echo $site_config['nfo_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where blocks's will be stored:</td>
          <td><input type="text" name="site_config[blocks_dir]" value="<?php echo $site_config['blocks_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where <i>Disk</i> cache will be stored:</td>
          <td><input type="text" name="site_config[cache_dir]" value="<?php echo $site_config['cache_dir']; ?>" size="50" /></td>
     </tr>
     <!-- File Path(s) -->
     
     <!-- Tracker Options -->
     <tr>
         <td colspan="2"><b>Tracker Settings:</b><br />&#9492; <small>Main settings and options.</small></td> 
     </tr>
     <tr>
         <td>Site Name:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITENAME"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Site Email:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITEEMAIL"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Site URL:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITEURL"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Default Theme:</td>
         <td>
         <select name="site_config[default_theme]">
         <?php $res = SQL_Query_exec("SELECT * FROM `stylesheets`");
               while ($row = mysqli_fetch_assoc($res)): ?>
         <option value="<?php echo $row["id"]; ?>" <?php echo ( $row["id"] == $site_config["default_theme"] ? 'selected="selected"' : null ); ?>><?php echo $row["name"]; ?></option>
         <?php endwhile; ?>
         </select>
         </td>
     </tr>
     <tr>
         <td>Default Language:</td>
         <td>
         <select name="site_config[default_language]">
         <?php $res = SQL_Query_exec("SELECT * FROM `languages`");
               while ($row = mysqli_fetch_assoc($res)): ?>
         <option value="<?php echo $row["id"]; ?>" <?php echo ( $row["id"] == $site_config["default_language"] ? 'selected="selected"' : null ); ?>><?php echo $row["name"]; ?></option>
         <?php endwhile; ?>
         </select>
         </td> 
     </tr>
     <tr>
         <td>Site Charset:</td>
         <td><input type="text" name="" value="<?php echo $site_config["CHARSET"]; ?>" size="8" /></td>
     </tr>
     <tr>
         <td>Announce Url:</td>
         <td><input type="text" name="" value="<?php echo $site_config["announce_list"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Passkey URL:</td>
         <td><input type="text" name="" value="<?php echo $site_config["PASSKEYURL"]; ?>" size="50" /></td>
     </tr>
     <!-- Tracker Options -->
   
     <!-- Image Uploads -->
     <tr>
         <td colspan="2"><b>Image Uploads:</b><br />&#9492; <small>Manage image uploads.</small></td>
     </tr>
     <tr>
         <td>Max File Size:</td>
         <td><input type="text" name="" value="<?php echo $site_config["image_max_filesize"]; ?>" size="5" /> kb</td>
     </tr>
     <tr>
         <td>File Types:</td>
         <td>
         <input type="checkbox" name="site_config[allowed_image_types][image/png]" value=".png" <?php echo ( isset($site_config['allowed_image_types']['image/png']) ? 'checked="checked"' : null ); ?> /> image/png
         <input type="checkbox" name="site_config[allowed_image_types][image/gif]" value=".gif" <?php echo ( isset($site_config['allowed_image_types']['image/gif']) ? 'checked="checked"' : null ); ?> /> image/gif
         <input type="checkbox" name="site_config[allowed_image_types][image/jpg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/jpg']) ? 'checked="checked"' : null ); ?> /> image/jpg  
         <input type="checkbox" name="site_config[allowed_image_types][image/jpeg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/jpeg']) ? 'checked="checked"' : null ); ?> /> image/jpeg 
         <input type="checkbox" name="site_config[allowed_image_types][image/pjpeg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/pjpeg']) ? 'checked="checked"' : null ); ?>/> image/pjpeg        
         </td>
     </tr>
     <!-- Image Uploads -->
     
     <!-- Wait Times -->
     <tr>
         <td colspan="2"><b>Wait Times:</b><br />&#9492; <small>Configure wait times.</small></td>
     </tr>
     <tr>
         <td>Enable Wait Times:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config['MEMBERSONLY_WAIT'] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config['MEMBERSONLY_WAIT'] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Usergroups Wait:</td>
         <td><input type="text" name="" value="<?php echo $site_config["WAIT_CLASS"]; ?>" size="50" /></td> 
     </tr>
     <tr>
         <td>Times (A):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSA"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOA"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITA"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (B):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSB"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOB"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITB"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (C):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSC"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOC"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITC"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (D):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSD"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOD"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITD"]; ?>" size="3" /></td>
     </tr>
     <!-- Wait Times -->
     
     <!-- Mail Settings -->
     <tr>
         <td colspan="2"><b>Mail Settings:</b><br />&#9492; <small>Configure outgoing mail.</small></td> 
     </tr>
     <tr>
         <td>Mail Type:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_type"] == "php" ? 'checked="checked"' : null ); ?> /> PHP <input type="radio" name="" value="" <?php echo ( $site_config["mail_type"] == "pear" ? 'checked="checked"' : null ); ?> /> Pear</td>
     </tr>
     <tr>
         <td>SMTP Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_host"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP User:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_user"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_pass"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP Port:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_port"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>SMTP Auth:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_smtp_auth"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["mail_smtp_auth"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>SMTP SSL:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_smtp_ssl"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["mail_smtp_ssl"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <!-- Mail Settings -->
     
     <!-- Cache Settings -->
     <tr>
         <td colspan="2"><b>Cache Settings:</b><br />&#9492; <small>Configure cache.</small></td> 
     </tr>            
     <tr>
         <td>Cache Type:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "disk" ? 'checked="checked"' : null ); ?> /> Disk <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "apc" ? 'checked="checked"' : null ); ?> /> APC <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "memcache" ? 'checked="checked"' : null ); ?> /> Memcache <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "xcache" ? 'checked="checked"' : null ); ?> /> XCache</td>
     </tr>
     <tr>
         <td>Memcache Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["cache_memcache_host"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Memcache Port:</td>
         <td><input type="text" name="" value="<?php echo $site_config["cache_memcache_port"]; ?>" size="50" /></td>
     </tr>
     <!-- Cache Settings -->
     
     <!-- Ratio Warnings -->
     <tr>
         <td colspan="2"><b>Ratio Warnings:</b><br />&#9492; <small>Configure ratio warnings.</small></td>
     </tr>
     <tr>
         <td>Enable:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ratiowarn_enable"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["ratiowarn_enable"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Minimum Ratio:</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_minratio"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Minimum Downloaded (GB):</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_mingigs"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Days to Warning:</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_daystowarn"]; ?>" size="3" /></td>
     </tr>
     <!-- Ratio Warnings -->
     
     <!-- Blocks Navigation -->
     <tr>
         <td colspan="2"><b>Blocks Management:</b><br />&#9492; <small>Configure Blocks settings.</small></td>
     </tr>
     <tr>
         <td>Left Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["LEFTNAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["LEFTNAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Right Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["RIGHTNAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["RIGHTNAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Middle Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["MIDDLENAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["MIDDLENAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <!-- Blocks Navigation -->
     
     <!-- Cleanup / Announce Settings -->
     <tr>
         <td colspan="2"><b>Cleanup &amp; Announce:</b><br /></td>
     </tr>
     <tr>
         <td>Peer Limit:</td>
         <td><input type="text" name="" value="<?php echo $site_config["PEERLIMIT"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Autoclean Interval:</td>
         <td><input type="text" name="" value="<?php echo $site_config["autoclean_interval"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Announce Interval:</td>
         <td><input type="text" name="" value="<?php echo $site_config["announce_interval"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Site Log Cleanup:</td>
         <td><input type="text" name="" value="<?php echo $site_config["LOGCLEAN"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Signup Timeout</td>
         <td><input type="text" name="" value="<?php echo $site_config["signup_timeout"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Dead Torrents:</td>
         <td><input type="text" name="" value="<?php echo $site_config["max_dead_torrent_time"]; ?>" size="4" /></td>
     </tr>
     <!-- Cleanup / Announce Settings -->
     
     <!-- Torrents Settings -->
     <tr>
         <td colspan="2"><b>Torrents Settings:</b><br />&#9492; <small>Configure Torrent settings.</small></td>
     </tr>                 
     <tr>
         <td>Allow External Torrents:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ALLOWEXTERNAL"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["ALLOWEXTERNAL"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Uploaders Only:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["UPLOADERSONLY"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["UPLOADERSONLY"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Allow Anonymous Upload:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ANONYMOUSUPLOAD"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["ANONYMOUSUPLOAD"] ? 'checked="checked"' : null ); ?> / > No</td>
     </tr>
     <tr>
         <td valign="top">Upload Rules:</td>
         <td><textarea name="" cols="39" rows="4"><?php echo $site_config["UPLOADRULES"]; ?></textarea></td>
     </tr>
     <!-- Torrents Settings -->
     
     <!-- TorrentTable -->
     <tr>
         <td colspan="2"><b>TorrentTable:</b><br />&#9492; <small>Configure TorrentTable.</small></td>
     </tr>
     <tr>
         <td valign="top">TorrentTable Columns:</td><!-- Needs finishing... -->
         <td>
         <?php $column = array('category', 'name', 'dl', 'uploader', 'comments', 'completed', 'size', 'seeders', 'leechers', 'health', 'ratio', 'external', 'wait', 'rating', 'added', 'nfo'); ?>
         <ul id="sortable-list">
           <?php for ($i = 0; $i < count($column); $i++): ?>
           <li class="sortme" title="<?php echo $column[$i]; ?>"><input type="checkbox" name="" value="" <?php echo ( in_array( $column[$i], explode(',', $site_config['torrenttable_columns']) ) ? 'checked="checked"' : null ); ?> /> <?php echo $column[$i]; ?></li>
           <?php endfor; ?>
         </ul>
         </td>
     </tr>
     <tr>
         <td>TorrentTable Expand:</td>
         <td>
         <?php $expand = array(''); ?>
         </td>
     </tr>
     <!-- TorrentTable -->
     
     <tr>
         <td colspan="2" align="right">
         <input type="reset" value="Reset" />
         <input type="submit" value="Update" />
         </td>
     </tr>
     </table>
     </form>

     <?php 
     end_frame();
     stdfoot();
 }
 
if ($action == "reports" && $do == "view") {

      $page = 'admincp.php?action=reports&amp;do=view&amp;';
      $pager[] = substr($page, 0, -4);

      if ($_POST["mark"])
      {
          if (!@count($_POST["reports"])) show_error_msg(T_("ERROR"), "Nothing selected to mark.", 1);
          $ids = array_map("intval", $_POST["reports"]);
          $ids = implode(",", $ids);
          SQL_Query_exec("UPDATE reports SET complete = '1', dealtwith = '1', dealtby = '$CURUSER[id]' WHERE id IN ($ids)");
          header("Refresh: 2; url=admincp.php?action=reports&do=view");
          show_error_msg(T_("SUCCESS"), T_("CP_ENTRIES_MARK_COMP"), 1);
      }
      
      if ($_POST["del"])
      {
          if (!@count($_POST["reports"])) show_error_msg(T_("ERROR"), "Nothing selected to delete.", 1);
          $ids = array_map("intval", $_POST["reports"]);
          $ids = implode(",", $ids);
          SQL_Query_exec("DELETE FROM reports WHERE id IN ($ids)");
          header("Refresh: 2; url=admincp.php?action=reports&do=view");
          show_error_msg(T_("SUCCESS"), "Entries marked deleted.", 1);
      }
      
      $where = array();
      
      switch ( $_GET["type"] )
      {
          case "user":
            $where[] = "type = 'user'";
            $pager[] = "type=user";    
            break;
          case "torrent":
            $where[] = "type = 'torrent'";
            $pager[] = "type=torrent";
            break;
          case "comment":
            $where[] = "type = 'comment'";
            $pager[] = "type=comment";  
            break;
          case "forum":
            $where[] = "type = 'forum'";
            $pager[] = "type=forum";  
            break;
          default:
            $where = null;
            break;
      }
  
      switch ( $_GET["completed"] )
      {
          case 1:
            $where[] = "complete = '1'";
            $pager[] = "complete=1";
            break;
          default:
            $where[] = "complete = '0'";
            $pager[] = "complete=0";
            break;
      }
      
      $where = implode(" AND ", $where);
      $pager = implode("&amp;", $pager);
                                
      $num = get_row_count("reports", "WHERE $where");
      
      list($pagertop, $pagerbottom, $limit) = pager(25, $num, "$pager&amp;");
      
      $res = SQL_Query_exec("SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra, reports.reason, reports.type, users.username, reports.complete FROM `reports` INNER JOIN users ON reports.addedby = users.id WHERE $where ORDER BY reports.id DESC $limit");
      
      stdhead("Reported Items");
      navmenu();    

      begin_frame("Reported Items");
      ?>
        
      <table align="right">
      <tr>
          <td valign="top">
          <form id='sort' action=''>
          <b>Type:</b>
          <select name="type" onchange="window.location='<?php echo $page; ?>type='+this.options[this.selectedIndex].value+'&amp;completed='+document.forms['sort'].completed.options[document.forms['sort'].completed.selectedIndex].value">
          <option value="">All Types</option>
          <option value="user" <?php echo ($_GET['type'] == "user" ? " selected='selected'" : ""); ?>>Users</option>
          <option value="torrent" <?php echo ($_GET['type'] == "torrent" ? " selected='selected'" : ""); ?>>Torrents</option>
          <option value="comment" <?php echo ($_GET['type'] == "comment" ? " selected='selected'" : ""); ?>>Comments</option>
          <option value="forum" <?php echo ($_GET['type'] == "forum" ? " selected='selected'" : ""); ?>>Forum</option>
          </select>
          <b>Completed:</b>
          <select name="completed" onchange="window.location='<?php echo $page; ?>completed='+this.options[this.selectedIndex].value+'&amp;type='+document.forms['sort'].type.options[document.forms['sort'].type.selectedIndex].value">
          <option value="0" <?php echo ($_GET['completed'] == 0 ? " selected='selected'" : ""); ?>>No</option>
          <option value="1" <?php echo ($_GET['completed'] == 1 ? " selected='selected'" : ""); ?>>Yes</option>
          </select>
          </form>     
          </td>
      </tr>
      </table>
      <br />
      <br />
      <br />
      
      <form id="reports" method="post" action="admincp.php?action=reports&amp;do=view">
      <table cellpadding="3" cellspacing="3" class="table_table" width="100%" align="center">
      <tr>
          <th class="table_head">Reported By</th>
          <th class="table_head">Subject</th>
          <th class="table_head">Type</th>
          <th class="table_head">Reason</th>
          <th class="table_head">Dealt With</th>
          <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
      </tr>
      
      <?php if (!mysqli_num_rows($res)): ?>
      <tr>
          <td class="table_col1" colspan="6" align="center">No reports found.</td>
      </tr>
      <?php endif; ?>
      
      <?php
      while ($row = mysqli_fetch_assoc($res)):  
          
      
      $dealtwith = '<b>No</b>';
      if ($row["dealtby"] > 0)
      {
          $q = SQL_Query_exec("SELECT username FROM users WHERE id = '$row[dealtby]'");
          $r = mysqli_fetch_assoc($q);
          $dealtwith = 'By <a href="account-details.php?id='.$row['dealtby'].'">'.$r['username'].'</a>';
      }    
      
      switch ( $row["type"] )
      {
          case "user":
            $q = SQL_Query_exec("SELECT username FROM users WHERE id = '$row[votedfor]'");
            break;
          case "torrent":
            $q = SQL_Query_exec("SELECT name FROM torrents WHERE id = '$row[votedfor]'");
            break;
          case "comment":
            $q = SQL_Query_exec("SELECT text, news, torrent FROM comments WHERE id = '$row[votedfor]'");
            break;
          case "forum":
            $q = SQL_Query_exec("SELECT subject FROM forum_topics WHERE id = '$row[votedfor]'");
            break;
      }
      
      $r = mysqli_fetch_row($q);
      
      if ($row["type"] == "user")
          $link = "account-details.php?id=$row[votedfor]";
      else if ($row["type"] == "torrent")
          $link = "torrents-details.php?id=$row[votedfor]";
      else if ($row["type"] == "comment")
          $link = "comments.php?type=".($r[1] > 0 ? "news" : "torrent")."&amp;id=".($r[1] > 0 ? $r[1] : $r[2])."#comment$row[votedfor]";
      else if ($row["type"] == "forum")
          $link = "forums.php?action=viewtopic&amp;topicid=$row[votedfor]&amp;page=last#post$row[votedfor_xtra]";
      ?>
      <tr>
          <td class="table_col1" align="center" width="10%"><a href="account-details.php?id=<?php echo $row['addedby']; ?>"><?php echo $row['username']; ?></a></td>
          <td class="table_col2" align="center" width="15%"><a href="<?php echo $link; ?>"><?php echo CutName($r[0], 40); ?></a></td>
          <td class="table_col1" align="center" width="10%"><?php echo $row['type']; ?></td>
          <td class="table_col2" align="center" width="50%"><?php echo htmlspecialchars($row['reason']); ?></td>
          <td class="table_col1" align="center" width="10%"><?php echo $dealtwith; ?></td>
          <td class="table_col2" align="center" width="5%"><input type="checkbox" name="reports[]" value="<?php echo $row["id"]; ?>" /></td>
      </tr>
      <?php endwhile; ?>
      
      <tr>
          <td colspan="6" align="center" class="table_head">
          <?php if ($_GET["completed"] != 1): ?>
          <input type="submit" name="mark" value="Mark Completed" />
          <?php endif; ?>
          <input type="submit" name="del" value="Delete" />
          </td>
      </tr>
      </table>
      </form>
  
      <?php
    
      print $pagerbottom;
      
      end_frame();
      stdfoot();
  }
  
#======================================================================#
# Warned Users - Updated by djhowarth (11-12-2011)
#======================================================================#
if ($action == "warned")
{
    if ($do == "delete") 
    {
        if ($_POST["removeall"])
        {
            $res = SQL_Query_exec("SELECT `id` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes'");
            while ($row = mysqli_fetch_assoc($res))
            {
                SQL_Query_exec("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` = '$row[id]'");
                SQL_Query_exec("UPDATE `users` SET `warned` = 'no' WHERE `id` = '$row[id]'");
            }
        }
        else
        {
            if (!@count($_POST['warned'])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1);
            $ids = array_map("intval", $_POST["warned"]);
            $ids = implode(", ", $ids);
                
            SQL_Query_exec("DELETE FROM `warnings` WHERE `active` = 'yes' AND `userid` IN ($ids)");
            SQL_Query_exec("UPDATE `users` SET `warned` = 'no' WHERE `id` IN ($ids)");
        }
        
        
        autolink("admincp.php?action=warned", "Entries Confirmed");
    }
    
    $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' AND warned = 'yes'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=warned&amp;');
    
    $res = SQL_Query_exec("SELECT `id`, `username`, `class`, `added`, `last_access` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `warned` = 'yes' ORDER BY `added` DESC $limit");

    stdhead("Warned Users");
    navmenu();
    
    begin_frame("Warned Users");
    ?>
    
    <center>
    This page displays all users which are enabled and have active warnings, they can be mass deleted or deleted per user. Please note that if you delete a warning which was for poor ratio then
    this is extending the time user has left to expire. <?php echo number_format($count); ?> users are warned;
    </center>

    <br />
    <?php if ($count > 0): ?>
    <br />
    <form id="warned" method="post" action="admincp.php?action=warned&amp;do=delete">
    <table cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>   
        <th class="table_head">Added</th>  
        <th class="table_head">Last Access</th>
        <th class="table_head">Warnings</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>  
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>#warnings"><?php echo number_format(get_row_count("warnings", "WHERE userid = '$row[id]' AND active = 'yes'")); ?></a></td>
        <td class="table_col2" align="center"><input type="checkbox" name="warned[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td class="table_head" colspan="6" align="right">
        <input type="submit" value="Remove Checked" />
        <input type="submit" name="removeall" value="Remove All" />
        </td>
    </tr>
    </table>         
    </form>
    <?php else: ?>
    <center><b>No Warned Users...</b></center>
    <?php
    endif;
    
    if ($count > 25) echo $pagerbottom;

    end_frame();
    stdfoot(); 
}

#======================================================================#
#    Manual Conf Reg - Updated by djhowarth (29-10-2011)
#======================================================================#
if ($action == "confirmreg")
{
    if ($do == "confirm") 
    {
        if ($_POST["confirmall"])
            SQL_Query_exec("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
        else
        {
            if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), T_("NOTHING_SELECTED"), 1); 
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            SQL_Query_exec("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");  
        }
        
        autolink("admincp.php?action=confirmreg", "Entries Confirmed");
    }
    
    $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=confirmreg&amp;'); 
    
    $res = SQL_Query_exec("SELECT `id`, `username`, `email`, `added`, `ip` FROM `users` WHERE `status` = 'pending' AND `invited_by` = '0' ORDER BY `added` DESC $limit");

    stdhead("Manual Registration Confirm");
    navmenu();
    
    begin_frame("Manual Registration Confirm");
    ?>
    
    <center>
    This page displays all unconfirmed users excluding users which have been invited by current members. <?php echo number_format($count); ?> members are pending;
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="confirmreg" method="post" action="admincp.php?action=confirmreg&amp;do=confirm">
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Registered</th>
        <th class="table_head">IP</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo $row["username"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right">
        <input type="submit" value="Confirm Checked" />
        <input type="submit" name="confirmall" value="Confirm All" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}

#======================================================================#
#    View Pending Invited Users - Created by djhowarth (18-11-2011) 
#======================================================================#
if ($action == "pendinginvite")
{
    if ($do == "del") 
    {
        if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);

        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);
        
        $res = SQL_Query_exec("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
        while ($row = mysqli_fetch_assoc($res))
        {    
             # We remove the invitee from the inviter and give them back there invite.
             $invitees = str_replace("$row[id] ", "", $row["invitees"]);
             SQL_Query_exec("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
             SQL_Query_exec("DELETE FROM `users` WHERE `id` = '$row[id]'");
        }

        autolink("admincp.php?action=pendinginvite", "Entries Deleted");
    }
    
    $count = get_row_count("users", "WHERE status = 'pending' AND invited_by != '0'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=pendinginvite&amp;');  
                                                                     
    $res = SQL_Query_exec("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
    
    stdhead("Invited Pending Users");
    navmenu();
    
    begin_frame("Invited Pending Users");
    ?>
    
    <center>
    This page displays all invited users which have been sent invites but haven't yet activated there account. By deleting a user the inviter will recieve their invite back and any data associated with the invitee will be deleted. <?php echo number_format($count); ?> members are pending;
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="pendinginvite" method="post" action="admincp.php?action=pendinginvite">
    <input type="hidden" name="do" value="del" />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head">Invited</th>
        <th class="table_head">Invited By</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><?php echo $row["username"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><a href="account-details.php?id=<?php echo $row["invited_by"]; ?>"><?php echo $row["inviter"]; ?></a></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right">
        <input type="submit" value="Delete Checked" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}

#======================================================================#
# Invited Users - Created by djhowarth (11-12-2011) 
#======================================================================#
if ($action == "invited")
{
    if ($do == "del") 
    {
        if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);

        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);
        
        $res = SQL_Query_exec("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
        while ($row = mysqli_fetch_assoc($res))
        {    
             # We remove the invitee from the inviter and give them back there invite.
             $invitees = str_replace("$row[id] ", "", $row["invitees"]);
             SQL_Query_exec("UPDATE `users` SET `invites` = `invites` + 1, `invitees` = '$invitees' WHERE `id` = '$row[invited_by]'");
             deleteaccount($row['id']);
        }

        autolink("admincp.php?action=invited", "Entries Deleted");
    }
    
    $count = get_row_count("users", "WHERE status = 'confirmed' AND invited_by != '0'");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=invited&amp;');  
                                                                     
    $res = SQL_Query_exec("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
    
    stdhead("Invited Users");
    navmenu();
                    
    begin_frame("Invited Users");
    ?>
    
    <center>
    This page displays all invited users which have been sent invites and have activated there account. By deleting users the inviter will recieve there invite back and any data associated with the invitee will be deleted. <?php echo number_format($count); ?> members have confirmed invites;
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="invited" method="post" action="admincp.php?action=invited">
    <input type="hidden" name="do" value="del" />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head">E-mail</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>
        <th class="table_head">Invited</th>
        <th class="table_head">Last Access</th> 
        <th class="table_head">Invited By</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></a></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo get_user_class_name($row["class"]); ?></td>     
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td>  
        <td class="table_col2" align="center"><?php echo ( $row['inviter'] ) ? '<a href="account-details.php?id='.$row["invited_by"].'">'.$row["inviter"].'</a>' : 'Unknown User'; ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="7" align="right">
        <input type="submit" value="Delete Checked" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}
       
#======================================================================#
#  Simple User Search - Updated by djhowarth (21-11-2011) 
#======================================================================#
if ($action == "users")
{
    if ($CURUSER['delete_users'] == 'no' || $CURUSER['delete_torrents'] == 'no')
        autolink("admincp.php", "You do not have permission to be here.");
    
    if ($do == "del") 
    {
        if (!@count($_POST["users"])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);

        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);

        $res = SQL_Query_exec("SELECT `id`, `username` FROM `users` WHERE `id` IN ($ids)");
        while ($row = mysqli_fetch_row($res))
        {
            write_log("Account '$row[1]' (ID: $row[0]) was deleted by $CURUSER[username]");  
            deleteaccount($row[0]); 
        }
        
        if ($_POST['inc']) 
        {
            $res = SQL_Query_exec("SELECT `id`, `name` FROM `torrents` WHERE `owner` IN ($ids)");
            while ($row = mysqli_fetch_row($res))
            {
                write_log("Torrent '$row[1]' (ID: $row[0]) was deleted by $CURUSER[username]");    
                deletetorrent($row["id"]);
            }  
        } 

        autolink("admincp.php?action=users", "Entries Deleted");
    }
    
    $where = null;
    
    if ( !empty( $_GET['search'] ) )
    {
          $search = sqlesc('%' . $_GET['search'] . '%');
        
          $where  = "AND username LIKE " . $search . " OR email LIKE " . $search . "
                     OR ip LIKE " . $search;
    }

    $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' $where");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=users&amp;');  
                                                                     
    $res = SQL_Query_exec("SELECT id, username, class, email, ip, added, last_access FROM users WHERE enabled = 'yes' AND status = 'confirmed' $where ORDER BY username DESC $limit");
    
    stdhead(T_("USERS_SEARCH_SIMPLE"));
    navmenu();
    
    begin_frame(T_("USERS_SEARCH_SIMPLE"));
    ?>
    
    <center>
    This page displays all users which are enabled and confirmed. You can search for users and results will be returned
    matched against there username, e-mail and ip. You can also choose to delete them. If no results are shown please try
    redefining your search.
    
    <br />
    <form method="get" action="admincp.php">
    <input type="hidden" name="action" value="users" />
    Search: <input type="text" name="search" size="30" value="<?php echo htmlspecialchars( $_GET['search'] ); ?>" />
    <input type="submit" value="Search" />
    </form>
    </center>

    <?php if ($count > 0): ?>
    <br />
    <form id="usersearch" method="post" action="admincp.php?action=users">
    <input type="hidden" name="do" value="del" />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>
        <th class="table_head">E-mail</th>
        <th class="table_head">IP</th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Visited</th>  
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td> 
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td class="table_head" colspan="7" align="center">
        <input type="submit" name="inc" value="Delete (inc. torrents)" />
        <input type="submit" value="Delete" />
        </td>
    </tr>
    </table>         
    </form>
    <?php 
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}


#======================================================================#
#  SQL Error Log - Added by djhowarth (23-12-2011)
#======================================================================#
  if ($action == 'sqlerr')
  {
      if ($_POST['do'] == 'delete')
      {
          if (!@count($_POST['ids'])) show_error_msg(T_("ERROR"), "Nothing Selected.", 1);
          $ids = array_map('intval', $_POST['ids']);
          $ids = implode(',', $ids);
          
          SQL_Query_exec("DELETE FROM `sqlerr` WHERE `id` IN ($ids)");
          autolink("admincp.php?action=sqlerr", "Entries deleted.");
      }
      
      
      $count = get_row_count('sqlerr');
      
      list($pagertop, $pagerbottom, $limit) = pager(25, $count, 'admincp.php?action=sqlerr&amp;');
      
      $res = SQL_Query_exec("SELECT * FROM `sqlerr` ORDER BY `time` DESC $limit");
      
      stdhead('SQL Error');
      navmenu();
      
      begin_frame('SQL Error');
      
      if ($count > 0): ?>
      <form id="sqlerr" method="post" action="admincp.php?action=sqlerr">
      <input type="hidden" name="do" value="delete" />
      <table cellpadding="5" class="table_table" width="100%">
      <tr>
          <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);"</th>
          <th class="table_head">Message</th>
          <th class="table_head">Added</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($res)): ?>
      <tr>
          <td class="table_col1"><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" /></td>
          <td class="table_col2"><?php echo $row['txt']; ?></td>
          <td class="table_col1"><?php echo utc_to_tz($row['time']); ?></td>
      </tr>
      <?php endwhile; ?>
      <tr>
          <td align="right" colspan="3">
          <input type="submit" value="Delete" />
          </td>
      </tr>
      </table>
      </form>
      <?php 
      else:
        echo('<center><b>No Error logs found...</b></center>');
      endif;
            
      if ($count > 25) echo($pagerbottom);
      
      end_frame();
      stdfoot();
  }

#======================================================================#
#  Strong Privacy Users - Added by djhowarth (01-12-2011) 
#======================================================================#
if ($action == "privacylevel")
{
    $where = array();
    
    switch ( $_GET['type'] )
    {
        case 'low': 
              $where[] = "privacy = 'low'";    break;
        case 'normal':
              $where[] = "privacy = 'normal'"; break;
        case 'strong':                         
              $where[] = "privacy = 'strong'"; break;
        default:
              break;
    }
    
    $where[] = "enabled = 'yes'";
    $where[] = "status = 'confirmed'";
    
    $where = implode(' AND ', $where);
    
    $count = get_row_count("users", "WHERE $where");
    
    list($pagertop, $pagerbottom, $limit) = pager(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));  
                                                                     
    $res = SQL_Query_exec("SELECT id, username, class, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit");
    
    stdhead("Privacy Level");
    navmenu();
    
    begin_frame("Privacy Level");
    ?>
    
    <center>
    This page displays all users which are enabled, confirmed grouped by their privacy level.
    </center>

    <br />
    <table align="right">
    <tr>
        <td valign="top">
        <form id='sort' action=''>
        <b>Privacy Level:</b>
        <select name="type" onchange="window.location='admincp.php?action=privacylevel&type='+this.options[this.selectedIndex].value">
        <option value="">Any</option>
        <option value="low" <?php echo ($_GET['type'] == "low" ? " selected='selected'" : ""); ?>>Low</option>
        <option value="normal" <?php echo ($_GET['type'] == "normal" ? " selected='selected'" : ""); ?>>Normal</option>
        <option value="strong" <?php echo ($_GET['type'] == "strong" ? " selected='selected'" : ""); ?>>Strong</option>
        </select>
        </form>     
    </td>
    </tr>
    </table>
    <br />
    <br />
    
    <?php if ($count > 0): ?>
    <br />
    <table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="table_table">
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo T_("CLASS");?></th>
        <th class="table_head">E-mail</th>
        <th class="table_head">IP</th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Visited</th>  
    </tr>
    <?php while ($row = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="account-details.php?id=<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></a></td>
        <td class="table_col2" align="center"><?php echo get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo utc_to_tz($row["last_access"]); ?></td> 
    </tr>
    <?php endwhile; ?>
    </table>         
    <?php else: ?>
    <center><b>Nothing Found...</b></center>
    <?php  
    endif;
    
    if ($count > 25) echo $pagerbottom;
    
    end_frame();
    stdfoot(); 
}
                             
#======================================================================#
# Word Censor Filter
#======================================================================#
if($action == "censor") {
stdhead("Censor");
navmenu();
if($site_config["OLD_CENSOR"])
{
//Output
if ($_POST['submit'] == 'Add Censor'){
$query = "INSERT INTO censor (word, censor) VALUES (" . sqlesc($_POST['word']) . "," . sqlesc($_POST['censor']) . ");";
             SQL_Query_exec($query);
             }
if ($_POST['submit'] == 'Delete Censor'){
  $aquery = "DELETE FROM censor WHERE word = " . sqlesc($_POST['censor']) . " LIMIT 1";
  SQL_Query_exec($aquery);
  }

begin_frame(T_("WORD_CENSOR"));  
/*------------------
|HTML form for Word Censor
------------------*/
?>

<form method="post" action="admincp.php?action=censor">  
<table width='100%' cellspacing='3' cellpadding='3' align='center'>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Word:  <input type="text" name="word" id="word" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="verdana" size="1">Censor With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="verdana"><input type="submit" name="submit" value="Add Censor" /></font></td>
</tr>
</table>
</form>

<form method="post" action="admincp.php?action=censor">
<table>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
|Get the words currently censored
-------------*/

$select = "SELECT word FROM censor ORDER BY word";
$sres = SQL_Query_exec($select);
while ($srow = mysqli_fetch_array($sres))
{
        echo "<option>" . $srow[0] . "</option>\n";
        }
echo'</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="verdana"><input type="submit" name="submit" value="Delete Censor" /></font></td>
</tr></table></form>';
}
else
{
$to=isset($_GET["to"])?htmlentities($_GET["to"]):$to='';
switch ($to)
  {
    case 'write':
         begin_frame($LANG['ACP_CENSORED']);
         if (isset($_POST["badwords"]))
            {
            $f=fopen("censor.txt","w+");
            @fwrite($f,$_POST["badwords"]);
            fclose($f);
            }
			show_error_msg(T_("SUCCESS"),"Censor Updated!",0);
         break;


    case '':
    case 'read':
    default:
      $f=@fopen("censor.txt","r");
      $badwords=@fread($f,filesize("censor.txt"));
      @fclose($f);
	  begin_frame($LANG['ACP_CENSORED']);
      echo'<form action="admincp.php?action=censor&to=write" method="post" enctype="multipart/form-data">
  <table width="100%" align="center">
    <tr>
      <td align="center">'.$LANG['ACP_CENSORED_NOTE'].'</td>
    </tr>
    <tr>
      <td align="center"><textarea name="badwords" rows="20" cols="60">'.$badwords.'</textarea></td>
    </tr>
    <tr>
      <td align="center">
        <input type="submit" name="write" value="'.T_("CONFIRM").'" />&nbsp;&nbsp;
        <input type="submit" name="write" value="'.T_("CANCEL").'" />
      </td>
    </tr>
  </table>
</form><br />';
break;
}
}
end_frame();
stdfoot();
}
// End forum Censored Words


// IP Bans (TorrentialStorm)
if ($action == "ipbans") {
    stdhead(T_("BANNED_IPS"));
    navmenu();

    if ($do == "del") {
        if (!@count($_POST["delids"])) show_error_msg(T_("ERROR"), T_("NONE_SELECTED"), 1);
        $delids = array_map('intval', $_POST["delids"]);
        $delids = implode(', ', $delids);
        $res = SQL_Query_exec("SELECT * FROM bans WHERE id IN ($delids)");
        while ($row = mysqli_fetch_assoc($res)) {
            SQL_Query_exec("DELETE FROM bans WHERE id=$row[id]");
            
            # Needs to be tested...
            if (is_ipv6($row["first"]) && is_ipv6($row["last"])) {
                $first = long2ip6($row["first"]);
                $last  = long2ip6($row["last"]);
            } else {
                $first = long2ip($row["first"]);
                $last  = long2ip($row["last"]);
            }
            
            write_log("IP Ban ($first - $last) was removed by $CURUSER[id] ($CURUSER[username])");
        }
        show_error_msg(T_("SUCCESS"), "Ban(s) deleted.", 0);
    }

    if ($do == "add") {
        $first = trim($_POST["first"]);
        $last = trim($_POST["last"]);
        $comment = trim($_POST["comment"]);
        if ($first == "" || $last == "" || $comment == "")
            show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").". Go back and try again", 1);

	if (!validip($first) || !validip($last))
            show_error_msg(T_("ERROR"), "Bad IP address.");
        $comment = sqlesc($comment);
        $added = sqlesc(get_date_time());
        SQL_Query_exec("INSERT INTO bans (added, addedby, first, last, comment) VALUES($added, $CURUSER[id], '$first', '$last', $comment)");
        switch (mysqli_errno($GLOBALS["DBconnector"])) {
            case 1062:
                show_error_msg(T_("ERROR"), "Duplicate ban.", 0);
            break;
            case 0:
                show_error_msg(T_("SUCCESS"), "Ban added.", 0);
            break;
            default:
                show_error_msg(T_("ERROR"), T_("THEME_DATEBASE_ERROR")." ".htmlspecialchars(mysqli_error($GLOBALS["DBconnector"])), 0);
        }
    }

    begin_frame(T_("BANNED_IPS"));
    echo "<p align=\"justify\">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<br />
    If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><br />";

    $count = get_row_count("bans");
    if ($count == 0)
    print("<b>No Bans Found</b><br />\n");
    else {
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "admincp.php?action=ipbans&amp;"); // 50 per page
        echo $pagertop;

        echo "<form id='ipbans' action='admincp.php?action=ipbans&amp;do=del' method='post'><table width='98%' cellspacing='0' cellpadding='5' align='center' class='table_table'>
        <tr>
            <th class='table_head'>".T_("DATE_ADDED")."</th>
            <th class='table_head'>First IP</th>
            <th class='table_head'>Last IP</th>
            <th class='table_head'>".T_("ADDED_BY")."</th>
            <th class='table_head'>Comment</th>
            <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>";

        $res = SQL_Query_exec("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");
        while ($arr = mysqli_fetch_assoc($res)) {
            echo "<tr>
                <td align='center' class='table_col1'>".date('d/m/Y H:i:s', utc_to_tz_time($arr["added"]))."</td>
                <td align='center' class='table_col2'>$arr[first]</td>
                <td align='center' class='table_col1'>$arr[last]</td>
                <td align='center' class='table_col2'><a href='account-details.php?id=$arr[addedby]'>$arr[username]</a></td>
                <td align='center' class='table_col1'>$arr[comment]</td>
                <td align='center' class='table_col2'><input type='checkbox' name='delids[]' value='$arr[id]' /></td>
            </tr>";
        }
        echo "</table><br /><center><input type='submit' value='Delete Checked' /></center></form><br />";
        echo $pagerbottom;
    }

    echo "<br />";
    print("<form method='post' action='admincp.php?action=ipbans&amp;do=add'>\n");
    print("<table cellspacing='0' cellpadding='5' align='center' class='table_table' width='98%'>\n");
    print("<tr><th class='table_head' align='center'>Add Ban</th></tr>\n");
    print("<tr><td class='table_col1' align='center'>First IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='first' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Last IP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='last' size='40' /></td></tr>\n");
    print("<tr><td class='table_col1' align='center'>Comment: <input type='text' name='comment' size='40' /></td></tr>\n");
    print("<tr><td class='table_head' align='center'><input type='submit' value='Okay' /></td></tr>\n");
    print("</table></form><br />\n");

    end_frame();
    stdfoot();
}
// End IP Bans (TorrentialStorm)



// Advanced User Search (Ported from v1 - TorrentialStorm)
if ($action == "usersearch") {
	if ($do == "warndisable") {
		if (empty($_POST["warndisable"]))
			show_error_msg(T_("ERROR"), "You must select a user to edit.", 1);

		if (!empty($_POST["warndisable"])){
			$enable = $_POST["enable"];
			$disable = $_POST["disable"];
			$unwarn = $_POST["unwarn"];
			$warnlength = (int) $_POST["warnlength"];
			$warnpm = $_POST["warnpm"];
			$_POST['warndisable'] = array_map("intval", $_POST['warndisable']);
			$userid = implode(", ", $_POST['warndisable']);

			if ($disable != '') {
				SQL_Query_exec("UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($enable != '') {
				SQL_Query_exec("UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($unwarn != '') {
				$msg = "Your Warning Has Been Removed";
				foreach ($_POST["warndisable"] as $userid) {
					SQL_Query_exec("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));
				}

				$r = SQL_Query_exec("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));
				$user = mysqli_fetch_array($r);
				$exmodcomment = $user["modcomment"];
				$modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $CURUSER['username'] . ".\n". $modcomment . $exmodcomment;
				SQL_Query_exec("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));

				SQL_Query_exec("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($warn != '') {
				if (empty($_POST["warnpm"]))
					show_error_msg(T_("ERROR"), "You must type a reason/mod comment.", 1);

					$msg = "You have received a warning, Reason: $warnpm";

					$r = SQL_Query_exec("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));
					$user = mysqli_fetch_array($r);
					$exmodcomment = $user["modcomment"];
					$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
					SQL_Query_exec("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));

					SQL_Query_exec("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
					foreach ($_POST["warndisable"] as $userid) {
						SQL_Query_exec("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysqli_errno($GLOBALS["DBconnector"]) . ") " . mysqli_error($GLOBALS["DBconnector"]));
					}
			}

		}

		autolink("$_POST[referer]", "Redirecting back");
		die;
	}
	stdhead(T_("ADVANCED_USER_SEARCH"));
	navmenu();
	begin_frame("Search");

	if ($_GET['h']) {
		echo "<table width='65%' border='0' align='center'><tr><td align='left'>\n
			Fields left blank will be ignored;\n
			Wildcards * and ? may be used in Name, ".T_("EMAIL")." and Comments, as well as multiple values\n
			separated by spaces (e.g. 'wyz Max*' in Name will list both users named\n
			'wyz' and those whose names start by 'Max'. Similarly '~' can be used for\n
			negation, e.g. '~alfiest' in comments will restrict the search to users\n
			that do not have 'alfiest' in their comments).<br /><br />\n
			The Ratio field accepts 'Inf' and '---' besides the usual numeric values.<br /><br />\n
			The subnet mask may be entered either in dotted decimal or CIDR notation\n
			(e.g. 255.255.255.0 is the same as /24).<br /><br />\n
			Uploaded and Downloaded should be entered in GB.<br /><br />\n
			For search parameters with multiple text fields the second will be\n
			ignored unless relevant for the type of search chosen. <br /><br />\n
			The History column lists the number of forum posts and comments,\n
			respectively, as well as linking to the history page.\n
			</td></tr></table><br /><br />\n";
	} else {
		echo "<p align='center'>[<a href='admincp.php?action=usersearch&amp;h=1'>Instructions</a>]";
		echo "&nbsp;-&nbsp;[<a href='admincp.php?action=usersearch'>Reset</a>]</p>\n";
	}

?>
    <br />
	<form method="get" action="admincp.php">
	<input type="hidden" name="action" value="usersearch" />
	<table border="0" class="table_table" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <th class="table_head" colspan="6">Search Filter</th>
    </tr>
	<tr>

	<td class="table_col1" valign="middle">Name:</td>
	<td class="table_col2"><input name="n" type="text" value="<?php echo $_GET['n']?>" size="35" /></td>

	<td class="table_col1" valign="middle">Ratio:</td>
	<td class="table_col2"><select name="rt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['rt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>
	<input name="r" type="text" value="<?php echo $_GET['r']?>" size="5" maxlength="4" />
	<input name="r2" type="text" value="<?php echo $_GET['r2']?>" size="5" maxlength="4" /></td>

	<td class="table_col1" valign="middle">Member status:</td>
	<td class="table_col2"><select name="st">
	<?php
	$options = array("(any)","confirmed","pending");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['st']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td class="table_col1" valign="middle"><?php echo T_("EMAIL")?>:</td>
	<td class="table_col2"><input name="em" type="text" value="<?php echo $_GET['em']?>" size="35" /></td>
	<td class="table_col1" valign="middle">IP:</td>
	<td class="table_col2"><input name="ip" type="text" value="<?php echo $_GET['ip']?>" maxlength="17" /></td>

	<td class="table_col1" valign="middle">Account status:</td>
	<td class="table_col2"><select name="as">
	<?php
	$options = array("(any)", "enabled", "disabled");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i'  ".(($_GET['as']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>
	<td class="table_col1" valign="middle">Comment:</td>
	<td class="table_col2"><input name="co" type="text" value="<?php echo $_GET['co']?>" size="35" /></td>
	<td class="table_col1" valign="middle">Mask:</td>
	<td class="table_col2"><input name="ma" type="text" value="<?php echo $_GET['ma']?>" maxlength="17" /></td>
	<td class="table_col1" valign="middle">Class:</td>
	<td class="table_col2"><select name="c"><option value='1'>(any)</option>
	<?php
	$class = $_GET['c'];
	if (!is_valid_id($class)) {
		$class = '';
	}
	$groups = classlist();
	foreach ($groups as $group) {
		$id = $group["group_id"] + 2;
		echo "<option value='$id' ".($class == $id ? " selected='selected'" : "").">".htmlspecialchars($group["level"])."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>

	<td class="table_col1" valign="middle">Joined:</td>

	<td class="table_col2"><select name="dt">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['dt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="d" type="text" value="<?php echo $_GET['d']?>" size="12" maxlength="10" />

	<input name="d2" type="text" value="<?php echo $_GET['d2']?>" size="12" maxlength="10" /></td>


	<td class="table_col1" valign="middle">Uploaded (GB):</td>

	<td class="table_col2"><select name="ult" id="ult">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['ult']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ul" type="text" id="ul" size="8" maxlength="7" value="<?php echo $_GET['ul']?>" />

	<input name="ul2" type="text" id="ul2" size="8" maxlength="7" value="<?php echo $_GET['ul2']?>" /></td>
	<td class="table_col1">&nbsp;</td>

	<td class="table_col2">&nbsp;</td></tr>
	<tr>

	<td class="table_col1" valign="middle">Last Seen:</td>

	<td class="table_col2"><select name="lst">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['lst']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ls" type="text" value="<?php echo $_GET['ls']?>" size="12" maxlength="10" />


	<input name="ls2" type="text" value="<?php echo $_GET['ls2']?>" size="12" maxlength="10" /></td>
	<td class="table_col1" valign="middle">Downloaded (GB):</td>

	<td class="table_col2"><select name="dlt" id="dlt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['dlt']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="dl" type="text" id="dl" size="8" maxlength="7" value="<?php echo $_GET['dl']?>" />

	<input name="dl2" type="text" id="dl2" size="8" maxlength="7" value="<?php echo $_GET['dl2']?>" /></td>

	<td class="table_col1" valign="middle">Warned:</td>

	<td class="table_col2"><select name="w">
	<?php
	$options = array("(any)","Yes","No");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value='$i' ".(($_GET['w']=="$i")?"selected='selected'":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td colspan="6" align="center"><input name="submit" value="Search" type="submit" /></td></tr>
	</table>
	<br /><br />
	</form>

<?php

	// Validates date in the form [yy]yy-mm-dd;
	// Returns date if valid, 0 otherwise.
	function mkdate($date) {
		if (strpos($date, '-'))
			$a = explode('-', $date);
		elseif (strpos($date, '/'))
			$a = explode('/', $date);
		else
			return 0;
		for ($i = 0; $i < 3; $i++) {
			if (!is_numeric($a[$i]))
				return 0;
		}
		if (checkdate($a[1], $a[2], $a[0]))
			return date ("Y-m-d", mktime (0,0,0,$a[1],$a[2],$a[0]));
		else
			return 0;
	}

	// ratio as a string
	function ratios ($up, $down, $color = true) {
		if ($down > 0) {
			$r = number_format($up / $down, 2);
			if ($color)
				$r = "<font color='".get_ratio_color($r)."'>$r</font>";
		} elseif ($up > 0)
			$r = "Inf.";
		else
			$r = "---";
		return $r;
	}

	// checks for the usual wildcards *, ? plus mySQL ones
	function haswildcard ($text){
		if (strpos($text, '*') === false && strpos($text, '?') === false && strpos($text,'%') === False && strpos($text,'_') === False)
			return False;
		else
			return True;
	}

	///////////////////////////////////////////////////////////////////////////////

	if (count($_GET) > 0 && !$_GET['h']) {
		// name
		$names = explode(' ',trim($_GET['n']));
		if ($names[0] !== "") {
			foreach($names as $name) {
				if (substr($name,0,1) == '~') {
					if ($name == '~') continue;
					$names_exc[] = substr($name,1);
				} else
					$names_inc[] = $name;
			}

			if (is_array($names_inc)) {
				$where_is .= isset($where_is)?" AND (":"(";
				foreach($names_inc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
				unset($name_is);
			}

			if (is_array($names_exc)) {
				$where_is .= isset($where_is)?" AND NOT (":" NOT (";
				foreach($names_exc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
			}
			$q .= ($q ? "&amp;" : "") . "n=".urlencode(trim($_GET['n']));
		}

		// email
		$emaila = explode(' ', trim($_GET['em']));
		if ($emaila[0] !== "") {
			$where_is .= isset($where_is)?" AND (":"(";
			foreach($emaila as $email) {
				if (strpos($email,'*') === False && strpos($email,'?') === False && strpos($email,'%') === False) {
					if (!validemail($email)) {
						show_error_msg(T_("ERROR"), "Bad email.");
					}
					$email_is .= (isset($email_is)?" OR ":"")."u.email =".sqlesc($email);
				} else {
					$sql_email = str_replace(array('?','*'), array('_','%'), $email);
					$email_is .= (isset($email_is)?" OR ":"")."u.email LIKE ".sqlesc($sql_email);
				}
			}
			$where_is .= $email_is.")";
			$q .= ($q ? "&amp;" : "") . "em=".urlencode(trim($_GET['em']));
		}

		//class
		// NB: the c parameter is passed as two units above the real one
		$class = $_GET['c'] - 2;
		if (is_valid_id($class + 1)) {
			$where_is .= (isset($where_is)?" AND ":"")."u.class=$class";
			$q .= ($q ? "&amp;" : "") . "c=".($class+2);
		}

		// IP
		$ip = trim($_GET['ip']);
		if ($ip) {
			$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
			if (!preg_match($regex, $ip)) {
				show_error_msg(T_("ERROR"), "Bad IP.");
			}

			$mask = trim($_GET['ma']);
			if ($mask == "" || $mask == "255.255.255.255") {
				$where_is .= (isset($where_is)?" AND ":"")."u.ip = '$ip'";
			} else {
				if (substr($mask,0,1) == "/") {
					$n = substr($mask, 1, strlen($mask) - 1);
					if (!is_numeric($n) or $n < 0 or $n > 32) {
						show_error_msg(T_("ERROR"), "Bad subnet mask.");
					} else {
						$mask = long2ip(pow(2,32) - pow(2,32-$n));
					}
				} elseif (!preg_match($regex, $mask)) {
					show_error_msg(T_("ERROR"), "Bad subnet mask.");
				}
				$where_is .= (isset($where_is)?" AND ":"")."INET_ATON(u.ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')";
				$q .= ($q ? "&amp;" : "") . "ma=$mask";
			}
			$q .= ($q ? "&amp;" : "") . "ip=$ip";
		}

		// ratio
		$ratio = trim($_GET['r']);
		if ($ratio) {
			if ($ratio == '---') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded = 0 and u.downloaded = 0";
			} elseif (strtolower(substr($ratio,0,3)) == 'inf') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded > 0 and u.downloaded = 0";
			} else {
				if (!is_numeric($ratio) || $ratio < 0) {
					show_error_msg(T_("ERROR"), "Bad ratio.");
				}
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " (u.uploaded/u.downloaded)";
				$ratiotype = $_GET['rt'];
				$q .= ($q ? "&amp;" : "") . "rt=$ratiotype";
				if ($ratiotype == "3") {
					$ratio2 = trim($_GET['r2']);
					if (!$ratio2) {
						show_error_msg(T_("ERROR"), "Two ratios needed for this type of search.");
					}
					if (!is_numeric($ratio2) or $ratio2 < $ratio) {
						show_error_msg(T_("ERROR"), "Bad second ratio.");
					}
					$where_is .= " BETWEEN $ratio and $ratio2";
					$q .= ($q ? "&amp;" : "") . "r2=$ratio2";
				} elseif ($ratiotype == "2") {
					$where_is .= " < $ratio";
				} elseif ($ratiotype == "1") {
					$where_is .= " > $ratio";
				} else {
					$where_is .= " BETWEEN ($ratio - 0.004) and ($ratio + 0.004)";
				}
			}
			$q .= ($q ? "&amp;" : "") . "r=$ratio";
		}

		// comment
		$comments = explode(' ',trim($_GET['co']));
		if ($comments[0] !== "") {
			foreach($comments as $comment) {
				if (substr($comment,0,1) == '~') {
					if ($comment == '~') continue;
					$comments_exc[] = substr($comment,1);
				} else {
					$comments_inc[] = $comment;
				}

				if (is_array($comments_inc)) {
					$where_is .= isset($where_is)?" AND (":"(";
					foreach($comments_inc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
					unset($comment_is);
				}

				if (is_array($comments_exc)) {
					$where_is .= isset($where_is)?" AND NOT (":" NOT (";
					foreach($comments_exc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
				}
			}
				$q .= ($q ? "&amp;" : "") . "co=".urlencode(trim($_GET['co']));
		}

		$unit = 1073741824; // 1GB

		// uploaded
		$ul = trim($_GET['ul']);
		if ($ul) {
			if (!is_numeric($ul) || $ul < 0) {
				show_error_msg(T_("ERROR"), "Bad uploaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.uploaded ";
			$ultype = $_GET['ult'];
			$q .= ($q ? "&amp;" : "") . "ult=$ultype";
			if ($ultype == "3") {
				$ul2 = trim($_GET['ul2']);
				if(!$ul2) {
					show_error_msg(T_("ERROR"), "Two uploaded amounts needed for this type of search.");
				}
				if (!is_numeric($ul2) or $ul2 < $ul) {
					show_error_msg(T_("ERROR"), "Bad second uploaded amount.");
				}
				$where_is .= " BETWEEN ".$ul*$unit." and ".$ul2*$unit;
				$q .= ($q ? "&amp;" : "") . "ul2=$ul2";
			} elseif ($ultype == "2") {
				$where_is .= " < ".$ul*$unit;
			} elseif ($ultype == "1") {
				$where_is .= " >". $ul*$unit;
			} else {
				$where_is .= " BETWEEN ".($ul - 0.004)*$unit." and ".($ul + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "ul=$ul";
		}

		// downloaded
		$dl = trim($_GET['dl']);
		if ($dl) {
			if (!is_numeric($dl) || $dl < 0) {
				show_error_msg(T_("ERROR"), "Bad downloaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.downloaded ";
			$dltype = $_GET['dlt'];
			$q .= ($q ? "&amp;" : "") . "dlt=$dltype";
			if ($dltype == "3") {
				$dl2 = trim($_GET['dl2']);
				if(!$dl2) {
					show_error_msg(T_("ERROR"), "Two downloaded amounts needed for this type of search.");
				}
				if (!is_numeric($dl2) or $dl2 < $dl) {
					show_error_msg(T_("ERROR"), "Bad second downloaded amount.");
				}
				$where_is .= " BETWEEN ".$dl*$unit." and ".$dl2*$unit;
				$q .= ($q ? "&amp;" : "") . "dl2=$dl2";
			} elseif ($dltype == "2") {
				$where_is .= " < ".$dl*$unit;
			} elseif ($dltype == "1") {
				$where_is .= " > ".$dl*$unit;
			} else {
				$where_is .= " BETWEEN ".($dl - 0.004)*$unit." and ".($dl + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "dl=$dl";
		}

		// date joined
		$date = trim($_GET['d']);
		if ($date) {
			if (!$date = mkdate($date)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "d=$date";
			$datetype = $_GET['dt'];
			$q .= ($q ? "&amp;" : "") . "dt=$datetype";
			if ($datetype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(added) - UNIX_TIMESTAMP('$date')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.added ";
				if ($datetype == "3") {
					$date2 = mkdate(trim($_GET['d2']));
					if ($date2) {
						if (!$date = mkdate($date)) {
							show_error_msg(T_("ERROR"), "Invalid date.");
						}
						$q .= ($q ? "&amp;" : "") . "d2=$date2";
						$where_is .= " BETWEEN '$date' and '$date2'";
					} else {
						show_error_msg(T_("ERROR"), "Two dates needed for this type of search.");
					}
				} elseif ($datetype == "1") {
					$where_is .= "< '$date'";
				} elseif ($datetype == "2") {
					$where_is .= "> '$date'";
				}
			}
		}

		// date last seen
		$last = trim($_GET['ls']);
		if ($last) {
			if (!$last = mkdate($last)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "ls=$last";
			$lasttype = $_GET['lst'];
			$q .= ($q ? "&amp;" : "") . "lst=$lasttype";
			if ($lasttype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(last_access) - UNIX_TIMESTAMP('$last')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.last_access ";
				if ($lasttype == "3") {
					$last2 = mkdate(trim($_GET['ls2']));
					if ($last2) {
						$where_is .= " BETWEEN '$last' and '$last2'";
						$q .= ($q ? "&amp;" : "") . "ls2=$last2";
					} else {
						show_error_msg(T_("ERROR"), "The second date is not valid.");
					}
				} elseif ($lasttype == "1") {
					$where_is .= "< '$last'";
				} elseif ($lasttype == "2") {
					$where_is .= "> '$last'";
				}
			}
		}

		// status
		$status = $_GET['st'];
		if ($status) {
			$where_is .= ((isset($where_is))?" AND ":"");
			if ($status == "1") {
				$where_is .= "u.status = 'confirmed'";
			} else {
				$where_is .= "u.status = 'pending' AND u.invited_by = '0'";
			}
			$q .= ($q ? "&amp;" : "") . "st=$status";
		} 

		// account status
		$accountstatus = $_GET['as'];
		if ($accountstatus) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($accountstatus == "1") {
				$where_is .= " u.enabled = 'yes'";
			} else {
				$where_is .= " u.enabled = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "as=$accountstatus";
		}

		//donor
		$donor = $_GET['do'];
		if ($donor) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($donor == 1) {
				$where_is .= " u.donated > '1'";
			} else {
				$where_is .= " u.donated < '1'";
			}
			$q .= ($q ? "&amp;" : "") . "do=$donor";
		}

		//warned
		$warned = $_GET['w'];
		if ($warned) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($warned == 1) {
				$where_is .= " u.warned = 'yes'";
			} else {
				$where_is .= " u.warned = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "w=$warned";
		}

		// disabled IP
		$disabled = $_GET['dip'];
		if ($disabled) {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN users AS u2 ON u.ip = u2.ip";
			$where_is .= ((isset($where_is))?" AND ":"")."u2.enabled = 'no'";
			$q .= ($q ? "&amp;" : "") . "dip=$disabled";
		}

		// active
		$active = $_GET['ac'];
		if ($active == "1") {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN peers AS p ON u.id = p.userid";
			$q .= ($q ? "&amp;" : "") . "ac=$active";
		}

		$from_is = "users AS u".$join_is;
		$distinct = isset($distinct)?$distinct:"";

        # To Avoid Confusion we skip invite_* which are invited users which haven't confirmed yet, visit admincp.php?action=pendinginvited
        $where_is .= (isset($where_is))?" AND ":"";   
        $where_is .= "u.username NOT LIKE '%invite_%'";
        
		$queryc = "SELECT COUNT(".$distinct."u.id) FROM ".$from_is.
		(($where_is == "")?"":" WHERE $where_is ");

		$querypm = "FROM ".$from_is.(($where_is == "")?" ":" WHERE $where_is ");

		$select_is = "u.id, u.username, u.email, u.status, u.added, u.last_access, u.ip,
		u.class, u.uploaded, u.downloaded, u.donated, u.modcomment, u.enabled, u.warned, u.invited_by";

		$query = "SELECT ".$distinct." ".$select_is." ".$querypm;

		$res = SQL_Query_exec($queryc);
		$arr = mysqli_fetch_row($res);
		$count = $arr[0];

		$q = isset($q)?($q."&amp;"):"";

		$perpage = 25;

		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=usersearch&amp;$q");

		$query .= $limit;

		$res = SQL_Query_exec($query);

		if (mysqli_num_rows($res) == 0) {
		show_error_msg("Warning","No user was found.", 0);
		} else {
			if ($count > $perpage) {
				echo $pagertop;
			}
            echo "<form action='admincp.php?action=usersearch&amp;do=warndisable' method='post'>";
			echo "<table border='0' class='table_table' cellspacing='0' cellpadding='0' width='100%'>\n";
			echo "<tr><th class='table_head'>".T_("NAME")."</th>
			<th class='table_head'>IP</th>
			<th class='table_head'>".T_("EMAIL")."</th>".
			"<th class='table_head'>Joined:</th>".
			"<th class='table_head'>Last Seen:</th>".
			"<th class='table_head'>Status</th>".
			"<th class='table_head'>Enabled</th>".
			"<th class='table_head'>Ratio</th>".
			"<th class='table_head'>Uploaded</th>".
			"<th class='table_head'>Downloaded</th>".
			"<th class='table_head'>History</th>".
			"<th class='table_head' colspan='2'>Status</th></tr>\n";

			while ($user = mysqli_fetch_array($res)) {
                
				if ($user['added'] == '0000-00-00 00:00:00')
					$user['added'] = '---';
				if ($user['last_access'] == '0000-00-00 00:00:00')
					$user['last_access'] = '---';

			if ($user['ip']) {
				$ipstr = $user['ip'];
			} else {
				$ipstr = "---";
			}

			$pul = $user['uploaded'];
			$pdl = $user['downloaded'];


			$auxres = SQL_Query_exec("SELECT COUNT(DISTINCT p.id) FROM forum_posts AS p LEFT JOIN forum_topics as t ON p.topicid = t.id
			LEFT JOIN forum_forums AS f ON t.forumid = f.id WHERE p.userid = " . $user['id'] . " AND f.minclassread <= " .
			$CURUSER['class']);

			$n = mysqli_fetch_row($auxres);
			$n_posts = $n[0];

			$auxres = SQL_Query_exec("SELECT COUNT(id) FROM comments WHERE user = ".$user['id']);
			$n = mysqli_fetch_row($auxres);
			$n_comments = $n[0];

			echo "<tr><td class='table_col1' align='center'><b><a href='account-details.php?id=$user[id]'>$user[username]</a></b></td>" .
				"<td class='table_col2' align='center'>" . $ipstr . "</td><td class='table_col1' align='center'>" . $user['email'] . "</td>".
				"<td class='table_col2' align='center'>" . utc_to_tz($user['added']) . "</td>".
				"<td class='table_col1' align='center'>" . $user['last_access'] . "</td>".
				"<td class='table_col2' align='center'>" . $user['status'] . "</td>".
				"<td class='table_col1' align='center'>" . $user['enabled']."</td>".
				"<td class='table_col2' align='center'>" . ratios($pul,$pdl) . "</td>".
				"<td class='table_col1' align='center'>" . mksize($user['uploaded']) . "</td>".
				"<td class='table_col2' align='center'>" . mksize($user['downloaded']) . "</td>".
				"<td class='table_col1' align='center'>$n_posts ".P_("POST", $n_posts)."<br />$n_comments ".P_("COMMENT", $n_comments)."</td>".
				// This line actually needs rewriting, difficult to edit.                                                                                                                                                                                                                                                                                                                                          
				"<td class='table_col2' align='center'>".($user["enabled"] == "yes" && $user["warned"] == "no" ? "--" : ($user["enabled"] == "no" ? "<img src=\"images/disable.png\" title=\"".T_("DISABLED")."\" alt=\"Disabled\" />" : "") . ($user["warned"] == "yes" ? "<img src=\"images/warned.png\" title=\"".T_("WARNED")."\" alt=\"Warned\" />" : "")) . "</td>"."<td class='table_col1' align='center'><input type='checkbox' name=\"warndisable[]\" value='" . $user['id'] . "' /><input type='hidden' name=\"referer\" value=\"$_SERVER[REQUEST_URI]\" /></td></tr>\n";
			}
			echo "</table>
            <br />
			<table border='0' align='center' cellspacing='0' cellpadding='0'>
			<tr><td colspan='2'></td></tr>
			<tr><td align='right'><img src=\"images/disable.png\" alt=\"Disabled\" /> <input type='submit' name='disable' value=\"Disable Selected Accounts\" /></td><td style=\"border: none; padding: 2px;\" align='left'><input type='submit' name='enable' value=\"Enable Selected Accounts\" /> <img src=\"images/disable.png\" alt=\"Disabled\" /> <img src=\"images/check.gif\" alt=\"Ok\" /></td></tr>
			<tr><td colspan='2'><br /><br /></td></tr>
			<tr><td align='center'><img src=\"images/warned.png\" alt=\"Warned\" /> <input type='submit' name='warn' value=\"Warn Selected\" /></td><td align='left'><input type='submit' name='unwarn' value=\"Remove Warning Selected\" /> <img src=\"images/warned.png\" alt=\"Warned\" /> <img src=\"images/check.gif\" alt=\"Ok\" /></td></tr>
			<tr><td align='center' colspan='2'>Mod Comment (reason):<input type='text' size='30' name='warnpm' /></td></tr>
			</table></form>\n";
   
			if ($count > $perpage) {
				echo $pagerbottom;
			}
		}
	}

	end_frame();
	stdfoot();
}
// End Advanced User Search

// Forum management 
if ($action == "forum") {

    $error_ac == "";
    if ($_POST["do"] == "add_this_forum") {
        
        $new_forum_name = $_POST["new_forum_name"];
        $new_desc = $_POST["new_desc"];
        $new_forum_sort = (int) $_POST["new_forum_sort"];
        $new_forum_cat  = (int) $_POST["new_forum_cat"];
        $minclassread = (int)  $_POST["minclassread"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $guest_read = sqlesc($_POST["guest_read"]);
        
        if ($new_forum_name == "") $error_ac .= "<li>".T_("CP_FORUM_NAME_WAS_EMPTY")."</li>\n";
        if ($new_desc == "") $error_ac .= "<li>".T_("CP_FORUM_DESC_WAS_EMPTY")."</li>\n";
        if ($new_forum_sort == "") $error_ac .= "<li>".T_("CP_FORUM_SORT_ORDER_WAS_EMPTY")."</li>\n";
        if ($new_forum_cat == "") $error_ac .= "<li>".T_("CP_FORUM_CATAGORY_WAS_EMPTY")."</li>\n";

        if ($error_ac == "") {
            $res = SQL_Query_exec("INSERT INTO forum_forums (`name`, `description`, `sort`, `category`, `minclassread`, `minclasswrite`, `guest_read`) VALUES (".sqlesc($new_forum_name).", ".sqlesc($new_desc).", ".sqlesc($new_forum_sort).", '$new_forum_cat', '$minclassread', '$minclasswrite', $guest_read)");
            if ($res)
                autolink("admincp.php?action=forum", T_("CP_FORUM_NEW_ADDED_TO_DB"));
            else
                echo "<h4>".T_("CP_COULD_NOT_SAVE_TO_DB")."</h4>";
        } 
        else
            autolink("admincp.php?action=forum", $error_ac);
    }

    if ($_POST["do"] == "add_this_forumcat") {
        
        $new_forumcat_name = $_POST["new_forumcat_name"];
        $new_forumcat_sort = $_POST["new_forumcat_sort"];
        
        if ($new_forumcat_name == "") $error_ac .= "<li>".T_("CP_FORUM_CAT_NAME_WAS_EMPTY")."</li>\n";
        if ($new_forumcat_sort == "") $error_ac .= "<li>".T_("CP_FORUM_CAT_SORT_WAS_EMPTY")."</li>\n";

        if ($error_ac == "") {
            $res = SQL_Query_exec("INSERT INTO forumcats (`name`, `sort`) VALUES (".sqlesc($new_forumcat_name).", '".intval($new_forumcat_sort)."')");
            if ($res)
                autolink("admincp.php?action=forum", "Thank you, new forum cat added to db ...");
            else
                echo "<h4>".T_("CP_COULD_NOT_SAVE_TO_DB")."</h4>";
        } 
        else
            autolink("admincp.php?action=forum", $error_ac);
    }

    if ($_POST["do"] == "save_edit") {
        
        $id = (int) $_POST["id"];
        $changed_sort = (int) $_POST["changed_sort"];
        $changed_forum = sqlesc($_POST["changed_forum"]);
        $changed_forum_desc = sqlesc($_POST["changed_forum_desc"]);
        $changed_forum_cat = (int) $_POST["changed_forum_cat"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $minclassread  = (int) $_POST["minclassread"];
        $guest_read = sqlesc($_POST["guest_read"]);
        
        SQL_Query_exec("UPDATE forum_forums SET sort = '$changed_sort', name = $changed_forum, description = $changed_forum_desc, category = '$changed_forum_cat', minclassread='$minclassread', minclasswrite='$minclasswrite', guest_read=$guest_read WHERE id='$id'");
        autolink("admincp.php?action=forum", "<center><b>".T_("CP_UPDATE_COMPLETED")."</b></center>");
    }

    if ($_POST["do"] == "save_editcat") {
        
        $id = (int) $_POST["id"];
        $changed_sortcat = (int) $_POST["changed_sortcat"];
        
        SQL_Query_exec("UPDATE forumcats SET sort = '$changed_sortcat', name = ".sqlesc($_POST["changed_forumcat"])." WHERE id='$id'");
        autolink("admincp.php?action=forum", "<center><b>".T_("CP_UPDATE_COMPLETED")."</b></center>");
    }

    if ($_POST["do"] == "delete_forum" && is_valid_id($_POST["id"])) 
    {
        SQL_Query_exec("DELETE FROM forum_forums WHERE id = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_topics WHERE forumid = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_posts WHERE topicid = $_POST[id]");
        SQL_Query_exec("DELETE FROM forum_readposts WHERE topicid = $_POST[id]");
        autolink("admincp.php?action=forum", T_("CP_FORUM_DELETED"));
    }
    
    if ($_POST["do"] == "delete_forumcat" && is_valid_id($_POST["id"])) 
    {
        SQL_Query_exec("DELETE FROM forumcats WHERE id = $_POST[id]");
        
        $res = SQL_Query_exec("SELECT id FROM forum_forums WHERE category = $_POST[id]");
        
        while ( $row = mysqli_fetch_assoc($res) )
        {
            $res2 = SQL_Query_exec("SELECT id FROM forum_topics WHERE forumid = $row[id]");
            
            while ( $arr = mysqli_fetch_assoc($res2) )
            {
                SQL_Query_exec("DELETE FROM forum_posts WHERE topicid = $arr[id]");
                SQL_Query_exec("DELETE FROM forum_readposts WHERE topicid = $arr[id]");
            }
            
            SQL_Query_exec("DELETE FROM forum_topics WHERE forumid = $row[id]");  
            SQL_Query_exec("DELETE FROM forum_forums WHERE id = $row[id]");  
        }
        
        autolink("admincp.php?action=forum", T_("CP_FORUM_CAT_DELETED"));
    }
    
    stdhead(T_("FORUM_MANAGEMENT"));
    
    $groupsres = SQL_Query_exec("SELECT group_id, level FROM groups ORDER BY group_id ASC");
    while ($groupsrow = mysqli_fetch_row($groupsres))
        $groups[$groupsrow[0]] = $groupsrow[1];

    if ($_GET["do"] == "edit_forum") {
        
        $id = (int) $_GET["id"];
        
        $q = SQL_Query_exec("SELECT * FROM forum_forums WHERE id = '$id'");
        $r = mysqli_fetch_array($q);
        
        if (!$r)
             autolink("admincp.php?action=forum", T_("FORUM_INVALID"));
        
        begin_frame(T_("FORUM_MANAGEMENT_EDIT"));   
    ?>
          <form action="admincp.php?action=forum" method="post">
          <input type="hidden" name="do" value="save_edit" />
          <input type="hidden" name="id" value="<?php echo $id; ?>" />
          <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
          <tr class='f-form'>
          <td>New Name for Forum:</td>
          <td align='right'><input type="text" name="changed_forum" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td>
          </tr><tr class='f-form'>
          <td><?php echo T_("CP_FORUM_NEW_SORT_ORDER");?>:</td>
          <td align='right'><input type="text" name="changed_sort" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td>
          </tr><tr class='f-form'>
          <td>Description:</td>
          <td align='right'><textarea cols='50' rows='5' name='changed_forum_desc'><?php echo $r["description"]; ?></textarea></td>
          </tr><tr class='f-form'>
          <td>New Category:</td>
          <td align='right'><select name='changed_forum_cat'>
    <?php
    $query = SQL_Query_exec("SELECT * FROM forumcats ORDER BY sort, name");
    while ($row = mysqli_fetch_array($query))
        echo "<option value='{$row['id']}'>{$row['name']}</option>";

    echo "</select></td></tr>
    <tr class='f-form'><td>".T_("FORUM_MIN_CLASS_READ").":</td>
    <td align='right'><select name='minclassread'>";

    foreach ($groups as $id => $level) {
        $s = $r["minclassread"] == $id ? " selected='selected'" : "";
        echo "<option value='$id' $s>$level</option>";
    }

    echo "</select></td></tr><tr class='f-form'><td>".T_("FORUM_MIN_CLASS_POST").":</td>
    <td align='right'><select name='minclasswrite'>";

    foreach ($groups as $id => $level) {
        $s = $r["minclasswrite"] == $id ? " selected='selected'" : "";
        echo "<option value='$id' $s>$level</option>";
    }
    ?>
    </select></td></tr><tr class='f-form'>
    <td><td><?php echo T_("FORUM_ALLOW_GUEST_READ");?>:</td><td align='right'><input type="radio" name="guest_read" value="yes" <?php echo $r["guest_read"] == "yes" ? "checked='checked'" : ""?> />Yes, <input type="radio" name="guest_read" value="no" <?php echo $r["guest_read"] != "yes" ? "checked='checked'" : ""?> />No</td></tr>
    <tr class='f-form'><td><input type="submit" class="button" value="Change" /></td></tr>
    </table>
    </form>
    <?php
        end_frame();
        stdfoot();
    }

if ($_GET["do"] == "del_forum") {
    
    $id = (int) $_GET["id"];
    
    $t = SQL_Query_exec("SELECT * FROM forum_forums WHERE id = '$id'");
    $v = mysqli_fetch_array($t);
    
    if (!$v)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID"));
    
    begin_frame(T_("CONFIRM")); 
?>
    <form class='a-form' action="admincp.php?action=forum" method="post">
    <input type="hidden" name="do" value="delete_forum" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php echo T_("CP_FORUM_REALY_DEL");?> <?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo T_("CP_FORUM_THIS_WILL_REALLY_DEL");?>.
    <input type="submit" name="delcat" class="button" value="Delete" />
    </form>
<?php
          end_frame();
          stdfoot();
}

if ($_GET["do"] == "del_forumcat") {
    
    $id = (int) $_GET["id"];

    $t = SQL_Query_exec("SELECT * FROM forumcats WHERE id = '$id'");
    $v = mysqli_fetch_array($t);
    
    if (!$v)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID_CAT"));
    
    begin_frame(T_("CONFIRM")); 
?>
  <form class='a-form' action="admincp.php?action=forum" method="post">
  <input type="hidden" name="do" value="delete_forumcat" />
  <input type="hidden" name="id" value="<?php echo $id; ?>" />
      <?php echo T_("CP_FORUM_REALY_DEL_CAT");?><?php echo "<b>$v[name] with ID$v[id] ???</b>"; ?> <?php echo T_("CP_FORUM_THIS_WILL_REALLY_DEL");?>.
      <input type="submit" name="delcat" class="button" value="Delete" />
      </form>
<?php
          end_frame();
          stdfoot();
}

if ($_GET["do"] == "edit_forumcat") {
    
    $id = (int) $_GET["id"];

    $q = SQL_Query_exec("SELECT * FROM forumcats WHERE id = '$id'");
    $r = mysqli_fetch_array($q);
    
    if (!$r)
         autolink("admincp.php?action=forum", T_("FORUM_INVALID_CAT")); 
         
    begin_frame(T_("CP_CATEGORY_EDIT"));
    ?>
    <form action="admincp.php?action=forum" method="post">
    <input type="hidden" name="do" value="save_editcat" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <table class='f-border a-form' align='center' width='80%' cellspacing='2' cellpadding='5'>
    <tr class='f-title'><td><?php echo T_("CP_FORUM_NEW_NAME_CAT");?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_forumcat" class="option" size="35" value="<?php echo $r["name"]; ?>" /></td></tr>
    <tr><td align='center'<td><?php echo T_("CP_FORUM_NEW_SORT_ORDER");?>:</td></tr>
    <tr><td align='center'><input type="text" name="changed_sortcat" class="option" size="35" value="<?php echo $r["sort"]; ?>" /></td></tr>
    <tr><td align='center'><input type="submit" class="button" value="Change" /></td></tr>
    </table>
    </form>
    <?php
    end_frame();
    stdfoot();
}
    
    if (!$do) {
        navmenu();
        begin_frame(T_("FORUM_MANAGEMENT"));
        $query = SQL_Query_exec("SELECT * FROM forumcats ORDER BY sort, name");
        $allcat = mysqli_num_rows($query);
        $forumcat = array();
        while ($row = mysqli_fetch_array($query))
            $forumcat[] = $row;

        echo "
    <form action='admincp.php' method='post'>   
    <input type='hidden' name='sid' value='$sid' />
<input type='hidden' name='action' value='forum' />
<input type='hidden' name='do' value='add_this_forum' />
<table class='table_table' align='center' width='80%' cellspacing='2' cellpadding='5'>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_NAME").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_name' size='90' maxlength='30'  value='$new_forum_name' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_SORT_ORDER").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_sort' size='30' maxlength='10'  value='$new_forum_sort' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_DESC").":</td>
<td class='table_col2' align='right'><textarea cols='50' rows='5' name='new_desc'>$new_desc</textarea></td>
</tr>
<tr>
<td class='table_col1'>".T_("CP_FORUM_NEW_CAT").":</td>
<td class='table_col2' align='right'><select name='new_forum_cat'>";
foreach ($forumcat as $row)
    echo "<option value='{$row['id']}'>{$row['name']}</option>";

echo "</select></td>
</tr>
<tr>
<td class='table_col1'>".T_("FORUM_MIN_CLASS_READ").":</td>
<td class='table_col2' align='right'><select name='minclassread'>";

foreach ($groups as $id => $level) {
    $s = $r["minclassread"] == $id ? " selected='selected'" : "";
    echo "<option value='$id' $s>$level</option>";
}

echo "</select></td></tr>
<tr>
<td class='table_col1'>".T_("FORUM_MIN_CLASS_POST").":</td>
<td class='table_col2' align='right'><select name='minclasswrite'>";

foreach ($groups as $id => $level) {
    $s = $r["minclasswrite"] == $id ? " selected='selected'" : "";
    echo "<option value='$id' $s>$level</option>";
}

echo "</select></td>
</tr>".
"<tr>
<td class='table_col1'>".T_("FORUM_ALLOW_GUEST_READ").":</td>
<td class='table_col2' align='right'><input type=\"radio\" name=\"guest_read\" value=\"yes\" checked='checked' />Yes, <input type=\"radio\" name=\"guest_read\" value=\"no\" />No</td></tr>".
"<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='Add new forum' />
<input type='reset' value='".T_("RESET")."' />
</th>
</tr>";

#if($error_ac != "") echo "<tr><td colspan='2' align='center' style='background:#eeeeee;border:2px red solid'><b>COULD  NOT ADD NEW forum:</b><br /><ul>$error_ac</ul></td></tr>\n";

echo "</table>
</form>

<b>".T_("FORUM_CURRENT").":</b>
<table class='table_table' align='center' width='80%' cellspacing='0' cellpadding='4'>";

echo "<tr><th class='table_head' width='60'><font size='2'><b>".T_("ID")."</b></font></th><th class='table_head' width='120'>".T_("NAME")."</th><th class='table_head' width='250'>DESC</th><th class='table_head' width='45'>".T_("SORT")."</th><th class='table_head' width='45'>CATEGORY</th><th class='table_head' width='18'>".T_("EDIT")."</th><th class='table_head' width='18'>".T_("DEL")."</th></tr>\n";
$query = SQL_Query_exec("SELECT * FROM forum_forums ORDER BY sort, name");
$allforums = mysqli_num_rows($query);
if ($allforums == 0) {
    echo "<tr><td class='table_col1' colspan='7' align='center'>No Forums found</td></tr>\n";
} else {
    while($row = mysqli_fetch_array($query)) {
        foreach ($forumcat as $cat)
            if ($cat['id'] == $row['category'])
                $category = $cat['name'];
            
            echo "<tr><td class='table_col1' width='60' align='center'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1'  width='250'>$row[description]</td><td class='table_col2' width='45' align='center'>$row[sort]</td><td class='table_col1' width='45'>$category</td>\n";
            echo "<td class='table_col2' width='18' align='center'><a href='admincp.php?action=forum&amp;do=edit_forum&amp;id=$row[id]'>[".T_("EDIT")."]</a></td>\n";
            echo "<td class='table_col1' width='18' align='center'><a href='admincp.php?action=forum&amp;do=del_forum&amp;id=$row[id]'><img src='images/delete.gif' alt='".T_("FORUM_DELETE_CATEGORY")."' width='17' height='17' border='0' /></a></td></tr>\n";
    }
}
echo "</table>
<br /><b>".T_("FORUM_CURRENT_CATS").":</b><table class='table_table' align='center' width='80%' cellspacing='0' cellpadding='4'>
<tr><th class='table_head' width='60'><font size='2'><b>".T_("ID")."</b></font></th><th class='table_head' width='120'>".T_("NAME")."</th><th class='table_head' width='18'>".T_("SORT")."</th><th class='table_head' width='18'>".T_("EDIT")."</th><th class='table_head' width='18'>".T_("DEL")."</th></tr>\n";

if ($allcat == 0) {
    echo "<tr class='table_col1'><td class='f-border' colspan='7' align='center'>".T_("FORUM_NO_CAT_FOUND")."</td></tr>\n"; 
} else {
    foreach ($forumcat as $row) {
        echo "<tr><td class='table_col1' width='60'><font size='2'><b>ID($row[id])</b></font></td><td class='table_col2' width='120'> $row[name]</td><td class='table_col1' width='18'>$row[sort]</td>\n";
        echo "<td class='table_col2' width='18'><a href='admincp.php?action=forum&amp;do=edit_forumcat&amp;id=$row[id]'>[".T_("EDIT")."]</a></td>\n";
        echo "<td class='table_col1' width='18'><a href='admincp.php?action=forum&amp;do=del_forumcat&amp;id=$row[id]'><img src='images/delete.gif' alt='".T_("FORUM_DELETE_CATEGORY")."' width='17' height='17' border='0' /></a></td></tr>\n";
    }
}
echo "</table>\n";

echo "<br />
<form action='admincp.php?action=forum' method='post'>
<input type='hidden' name='do' value='add_this_forumcat' /> 
<table class='table_table' align='center' width='80%' cellspacing='2' cellpadding='5'>
<tr>
<td class='table_col1'>".T_("FORUM_NAME_OF_NEW_CAT").":</td>
<td class='table_col2' align='right' class='f-form'><input type='text' name='new_forumcat_name' size='60' maxlength='30'  value='$new_forumcat_name' /></td>
</tr>
<tr>
<td class='table_col1'>".T_("FORUM_CAT_SORT_ORDER").":</td>
<td class='table_col2' align='right'><input type='text' name='new_forumcat_sort' size='20' maxlength='10'  value='$new_forumcat_sort' /></td>
</tr>

<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' value='".T_("FORUM_ADD_NEW_CAT")."' />
<input type='reset' value='".T_("RESET")."' />
</th>
</tr>
</table>
</form>";
end_frame();
stdfoot();
    } // End New Forum


} // End Forum management 

?>
