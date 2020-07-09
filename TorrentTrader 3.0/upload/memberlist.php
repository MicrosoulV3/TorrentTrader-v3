<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

if ($CURUSER["view_users"]=="no")
    show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);
    
$search = trim($_GET['search']);
$class = (int) $_GET['class'];
$letter = trim($_GET['letter']);

if (!$class)
	unset($class);

$q = $query = null;
if ($search) {
	$query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
	if ($search) {
		$q = "search=" . htmlspecialchars($search);
	}
} elseif ($letter) {
	if (strlen($letter) > 1)
		unset($letter);
	if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
		unset($letter);
	} else {
		$query = "username LIKE '$letter%' AND status='confirmed'";
	}
	$q = "letter=$letter";
}

if (!$query) {
	$query = "status='confirmed'";
}

if ($class) {
	$query .= " AND class=$class";
	$q .= ($q ? "&amp;" : "") . "class=$class";
}

stdhead(T_("USERS"));
begin_frame(T_("USERS"));
print("<center><br /><form method='get' action='memberlist.php'>\n");
print(T_("SEARCH").": <input type='text' size='30' name='search' />\n");
print("<select name='class'>\n");
print("<option value='-'>(any class)</option>\n");
$res = SQL_Query_exec("SELECT group_id, level FROM groups");
while ($row = mysqli_fetch_assoc($res)) {
	print("<option value='$row[group_id]'" . ($class && $class == $row['group_id'] ? " selected='selected'" : "") . ">".htmlspecialchars($row['level'])."</option>\n");
}
print("</select>\n");
print("<input type='submit' value='".T_("SEARCH")."' />\n");
print("</form></center>\n");

print("<p align='center'>\n");

print("<a href='memberlist.php'><b>".T_("ALL")."</b></a> - \n");
foreach (range("a", "z") as $l) {
	$L = strtoupper($l);
	if ($l == $letter)
		print("<b>$L</b>\n");
	else
		print("<a href='memberlist.php?letter=$l'><b>$L</b></a>\n");
}

print("</p>\n");

$page = (int) $_GET['page'];
$perpage = 25;

$res = SQL_Query_exec("SELECT COUNT(*) FROM users WHERE $query");
$arr = mysqli_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "$i\n";
  else
    $pagemenu .= "<a href='?$q&amp;page=$i'>$i</a>\n";

if ($page == 1)
  $browsemenu .= "";
//  $browsemenu .= "[Prev]";
else
  $browsemenu .= "<a href='?$q&amp;page=" . ($page - 1) . "'>[Prev]</a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "";
//  $browsemenu .= "[Next]";
else
  $browsemenu .= "<a href='?$q&amp;page=" . ($page + 1) . "'>[Next]</a>";

$offset = max( 0, ( $page * $perpage ) - $perpage );

$res = SQL_Query_exec("SELECT users.*, groups.level FROM users INNER JOIN groups ON groups.group_id=users.class WHERE $query ORDER BY username LIMIT $offset,$perpage");

print("<br /><table border='0' class='table_table' width='100%' cellpadding='3' cellspacing='3'><tr><th class='table_head'>" . T_("USERNAME") . "</th><th class='table_head'>" . T_("REGISTERED") . "</th><th class='table_head'>" . T_("LAST_ACCESS") . "</th><th class='table_head'>" . T_("CLASS") . "</th><th class='table_head'>" . T_("COUNTRY") . "</th></tr>\n");
while ($arr = mysqli_fetch_assoc($res)) {
	
		$cres = SQL_Query_exec("SELECT name,flagpic FROM countries WHERE id=$arr[country]");

		if ($carr = mysqli_fetch_assoc($cres)) {
			$country = "<td align=\"center\" class='table_col1' style='padding: 0px'><img src='$site_config[SITEURL]/images/countries/$carr[flagpic]' title='".htmlspecialchars($carr['name'])."' alt='".htmlspecialchars($carr['name'])."' /></td>";
		} else {
			$country = "<td align=\"center\"  class='table_col1' style='padding: 0px'><img src='$site_config[SITEURL]/images/countries/unknown.gif' alt='Unknown' /></td>";
		}
	

/*	if ($arr['added'] == '0000-00-00 00:00:00')
		$arr['added'] = '-';
	if ($arr['last_access'] == '0000-00-00 00:00:00')
		$arr['last_access'] = T_("NEVER");*/

  print("<tr><td class='table_col1' align='left'><a href='account-details.php?id=$arr[id]'><b>$arr[username]</b></a>" .($arr["donated"] > 0 ? "<img src='$site_config[SITEURL]/images/star.png' border='0' alt='Donated' />" : "")."</td>" .
  "<td align=\"center\" class='table_col2'>".utc_to_tz($arr["added"])."</td><td align=\"center\" class='table_col1'>".utc_to_tz($arr["last_access"])."</td>".
    "<td class='table_col2' align='center'>" . T_($arr["level"]) . "</td>$country</tr>\n");
}
print('</table>');

print("<br /><p align='center'>$pagemenu<br />$browsemenu</p>");
end_frame();
stdfoot();

?>