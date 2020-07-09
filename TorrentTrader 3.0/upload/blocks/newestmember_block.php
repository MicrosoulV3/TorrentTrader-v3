<?php
//USERS ONLINE
begin_block(T_("NEWEST_MEMBERS"));

$expire = 600; // time in seconds
if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
	$res = SQL_Query_exec("SELECT id, username FROM users WHERE enabled = 'yes' AND status='confirmed' AND privacy != 'strong' ORDER BY id DESC LIMIT 5");
	$rows = array();

	while ($row = mysqli_fetch_assoc($res))
		$rows[] = $row;

	$TTCache->Set("newestmember_block", $rows, $expire);
}

if (!$rows) {
	echo T_("NOTHING_FOUND");
} else {
		echo "<div id='nMember' class='bMenu'><ul>\n";
	foreach ($rows as $row) {
		echo "<li><a href='account-details.php?id=$row[id]'>$row[username]</a></li>\n";
	}
		echo "</ul></div>\n";
}

end_block();
?>