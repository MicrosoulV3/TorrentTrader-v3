<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("MOST_ACTIVE"));

	$where = "WHERE banned = 'no' AND visible = 'yes'";
	//uncomment the following line to exclude external torrents
	//$where = "WHERE external !='yes' AND banned ='no' AND visible = 'yes'"  

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
		$res = SQL_Query_exec("SELECT id, name, seeders, leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");

		$rows = array();
		while ($row = mysqli_fetch_assoc($res))
			$rows[] = $row;

		$TTCache->Set("mostactivetorrents_block", $rows, $expires);
	}

	if ($rows) {
		foreach ($rows as $row) { 
				$char1 = 18; //cut length 
				$smallname = htmlspecialchars(CutName($row["name"], $char1));
				echo "<a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</a><br /> - [S: " . number_format($row["seeders"]) . " - L: " . number_format($row["leechers"]) . "]<br /><br />\n";
		}

} else {
	print("<center>".T_("NOTHING_FOUND")."</center>\n");
}
end_block();
}
?>