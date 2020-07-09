<?php
require_once ("backend/functions.php");
require_once ("backend/bbcode.php");
dbconn();
loggedinonly();

# Todo: Clean this shit up, move to admincp

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
	 show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}


$sure = $_GET['sure'];
$del = $_GET['del'];
$team = htmlspecialchars($_GET['team']);
$edited = (int)$_GET['edited'];
$id = (int)$_GET['id'];
$team_name = $_GET['team_name'];
$team_info = $_GET['team_info'];
$team_image = $_GET['team_image'];
$team_description = $_GET['team_description'];
$teamownername = $_GET['team_owner'];
$editid = $_GET['editid'];
$editmembers = $_GET['editmembers'];
$name = $_GET['name'];
$image = $_GET['image'];
$owner = $_GET['owner'];
$info = $_GET['info'];
$add = $_GET['add'];
$added = sqlesc(get_date_time());


stdhead(T_("TEAMS"));
begin_frame(T_("TEAMS_MANAGEMENT"));


//Delete Team
if($sure == "yes") {
	
	$query = "UPDATE users SET team=0 WHERE team=" .sqlesc($del) . "";
	$sql = SQL_Query_exec($query);

	$query = "DELETE FROM teams WHERE id=" .sqlesc($del) . " LIMIT 1";
	$sql = SQL_Query_exec($query);
	echo("Team Successfully Deleted![<a href='teams-create.php'>Back</a>]");
	write_log($CURUSER['username']." has deleted team id:$del");
	end_frame();
	stdfoot();
	die();
}

if($del > 0) {
	echo("You and in the truth wish to delete team? ($team) ( <b><a href='teams-create.php?del=$del&amp;team=$team&amp;sure=yes'>Yes!</a></b> / <b><a href='teams-create.php'>No!</a></b> )");
	end_frame();
	stdfoot();
	die();
}

//Edit Team
if($edited == 1) {
    
    if (!$team_name || !$teamownername|| !$team_info) {
         print 'One or more fields left empty.';
         end_frame();
         stdfoot();
         die;
    }
    
    $team_name = sqlesc($team_name);
    $team_image = sqlesc($team_image);
    $teamownername = sqlesc($teamownername);
    $team_info = sqlesc($team_info);
    
	$aa = SQL_Query_exec("SELECT class, id FROM users WHERE username=$teamownername");
	$ar = mysqli_fetch_assoc($aa);
	$team_owner = $ar["id"];
	$query = "UPDATE teams SET	name = $team_name, info = $team_info, owner = $team_owner, image = $team_image WHERE id=".sqlesc($id);
	$sql = SQL_Query_exec($query);

	SQL_Query_exec("UPDATE users SET team = '$id' WHERE id= '$team_owner'");

	if($sql) {
		echo("<table cellspacing='0' cellpadding='5' width='50%'>");
		echo("<tr><td><div align='center'><b>Successfully Edited</b>[<a href='teams-create.php'>Back</a>]</div></tr>");
		echo("</table>");
		write_log($CURUSER['username']." has edited team ($team_name)");
		end_frame();
		stdfoot();
		die();
	}
}

if($editid > 0) {
	echo("<form name='smolf3d' method='get' action='teams-create.php'>");
    echo("<input type='hidden' name='id' value='$editid' />");
    echo("<input type='hidden' name='edited' value='1' />");   
	echo("<table cellspacing='0' cellpadding='5' width='50%'>");
	echo("<tr><td>".T_("TEAM_NAME").": </td><td align='right'><input type='text' size='50' name='team_name' value='$name' /></td></tr>");
	echo("<tr><td>".T_("TEAM_LOGO_URL").": </td><td align='right'><input type='text' size='50' name='team_image' value='$image' /></td></tr>");
	echo("<tr><td>".T_("TEAM_OWNER_NAME").": </td><td align='right'><input type='text' size='50' name='team_owner' value='$owner' /></td></tr>");
	echo("<tr><td valign='top'>".T_("DESCRIPTION").": </td><td align='right'><textarea name='team_info' cols='35' rows='5'>$info</textarea><br />(BBCode is allowed)</td></tr>");
	echo("<tr><td></td><td><div align='right'><input type='submit' value='Update' /></div></td></tr>");
	echo("</table></form>");
	end_frame();
	stdfoot();
	die();
}

//View Members
if($editmembers > 0) {
	echo("<center><table class='table_table' cellspacing='0' align='center'><tr>");
	echo("<th class='table_head'>Username</th><td class='table_head'>".T_("UPLOADED").": </th><th class='table_head'>Downloaded</th></tr>");
	$query = "SELECT id,username,uploaded,downloaded FROM users WHERE team=$editmembers";
	$sql = SQL_Query_exec($query);
	while ($row = mysqli_fetch_array($sql)) {
		$username = htmlspecialchars($row['username']);
		$uploaded = mksize($row['uploaded']);
		$downloaded = mksize($row['downloaded']);
		
		echo("<tr><td class='table_col1'><a href='account-details.php?id=$row[id]'>$username</a></td><td class='table_col2'>$uploaded</td><td class='table_col1'>$downloaded</td></tr>");
	}
	echo "</table></center>";
	end_frame();
	stdfoot();
	die();
}


//Add Team
if($add == 'true') {
    
    if (!$team_name || !$teamownername|| !$team_description) {
         print 'One or more fields left empty.';
         end_frame();
         stdfoot();
         die;
    }
    
    $team_name = sqlesc($team_name);
    $team_description = sqlesc($team_description);
    $team_image = sqlesc($team_image);
    $teamownername = sqlesc($teamownername);
    
	$aa = SQL_Query_exec("SELECT id FROM users WHERE username = $teamownername");
	$ar = mysqli_fetch_assoc($aa);
	$team_owner = $ar["id"];
    
    if ( !$team_owner )
    {
          print 'This user does not exist.';
          end_frame();
          stdfoot();
          die;
    }
        
	$query = "INSERT INTO teams SET	name = $team_name, owner = $team_owner, info = $team_description, image = $team_image, added = $added";
	$sql = SQL_Query_exec($query);

	$tid = mysqli_insert_id($GLOBALS["DBconnector"]);

	SQL_Query_exec("UPDATE users SET team = '$tid' WHERE id= '$team_owner'");

	if($sql) {
		write_log($CURUSER['username']." has created new team ($team_name)");
		$success = TRUE;
	}else{
		$success = FALSE;
	}
}

print("<b>Add new team:</b>");
print("<br />");
print("<br />");
echo("<form name='smolf3d' method='get' action='teams-create.php'>");
echo("<center><table cellspacing='0' cellpadding='5' width='50%'>");
echo("<tr><td>".T_("TEAM").": </td><td align='left'><input type='text' size='50' name='team_name' /></td></tr>");
echo("<tr><td>".T_("TEAM_OWNER_NAME").": </td><td align='left'><input type='text' size='50' name='team_owner' /></td></tr>");
echo("<tr><td valign='top'>".T_("DESCRIPTION").": </td><td align='left'><textarea name='team_description' cols='35' rows='5'></textarea><br />(BBCode is allowed)</td></tr>");
echo("<tr><td>".T_("TEAM_LOGO_URL").": </td><td align='left'><input type='text' size='50' name='team_image' /><input type='hidden' name='add' value='true' /></td></tr>");
echo("<tr><td></td><td><div align='left'><input value='".T_("TEAM_CREATE")."' type='submit' /></div></td></tr>");
echo("</table></center>");
if($success == TRUE) {
	print("<b>team successfully added!</b>");
}
echo("<br />");
echo("</form>");

//ELSE Display ".T_("TEAMS")."
print("<b>Current ".T_("TEAMS").":</b>");
print("<br />");
print("<br />");
echo("<center><table class='table_table' cellspacing='0' cellpadding='3' width='98%'><tr>");
echo("<td class='table_head'>ID</td><td class='table_head'>".T_("TEAM_LOGO")."</td><td class='table_head'>".T_("TEAM_NAME")."</td><td class='table_head'>".T_("TEAM_OWNER_NAME")."</td><td class='table_head'>".T_("DESCRIPTION")."</td><td class='table_head'>".T_("OTHER")."</td></tr>");
$query = "SELECT * FROM teams";
$sql = SQL_Query_exec($query);
while ($row = mysqli_fetch_array($sql)) {
	$id = (int)$row['id'];
	$name = htmlspecialchars($row['name']);
	$image = htmlspecialchars($row['image']);
	$owner = (int)$row['owner'];
	$info = format_comment($row['info']);
	$OWNERNAME1 = SQL_Query_exec("SELECT username, class FROM users WHERE id=$owner");
	$OWNERNAME2 = mysqli_fetch_array($OWNERNAME1);
	$OWNERNAME = $OWNERNAME2['username'];

	echo("<tr><td class='table_col1'><b>$id</b> </td> <td class='table_col2' align='center'><img src='$image' alt='' /></td> <td class='table_col1'><b>$name</b></td><td class='table_col2'><a href='account-details.php?id=$owner'>$OWNERNAME</a></td><td class='table_col1'>$info</td><td class='table_col2'><a href='teams-create.php?editmembers=$id'>[Members]</a>&nbsp;<a href='teams-create.php?editid=$id&amp;name=$name&amp;image=$image&amp;info=$info&amp;owner=$OWNERNAME'>[".T_("EDIT")."]</a>&nbsp;<a href='teams-create.php?del=$id&amp;team=$name'>[Delete]</a></td></tr>");
}
echo "</table></center>";

end_frame();
stdfoot();

?> 
