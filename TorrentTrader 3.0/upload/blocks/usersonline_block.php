<?php
begin_block(T_("ONLINE_USERS"));

$expires = 600; // Cache time in seconds

if (($rows = $TTCache->Get("usersonline_block", $expires)) === false) {
	$res = SQL_Query_exec("SELECT id, username FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND privacy !='strong' AND UNIX_TIMESTAMP('".get_date_time()."') - UNIX_TIMESTAMP(users.last_access) <= 900");

	$rows = array();
	while ($row = mysqli_fetch_assoc($res)) {
		$rows[] = $row;
	}

	$TTCache->Set("usersonline_block", $rows, $expires);
}

if (!$rows) {
	echo T_("NO_USERS_ONLINE");
} else {
		echo "<div id='uOnline' class='bMenu'><ul>\n";;
	for ($i = 0, $cnt = count($rows), $n = $cnt - 1; $i < $cnt; $i++) {
		$row = &$rows[$i];
		echo "<li><a href='account-details.php?id=$row[id]'>$row[username]</a>".($i < $n ? ", " : "")."</li>\n";;
	}
		echo "</ul></div>\n";;
}

end_block();
?>