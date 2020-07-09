<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("SEEDERS_WANTED"));

	$external = "external = 'no'";
	// Uncomment below to include external torrents
	$external = 1;

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("seedwanted_block", $expires)) === false) {
		$res = SQL_Query_exec("SELECT id, name, seeders, leechers FROM torrents WHERE seeders = 0 AND leechers > 0 AND banned = 'no' AND $external ORDER BY leechers DESC LIMIT 5");
		$rows = array();

		while ($row = mysqli_fetch_assoc($res)) {
			$rows[] = $row;
		}

		$TTCache->Set("seedwanted_block", $rows, $expires);
	}


	if (!$rows) {
		echo "<br />".T_("NOTHING_FOUND")."<br />";
	} else {
		echo "<div id='sNeeded' class='bMenu'><ul>\n";
		foreach ($rows as $row) { 
			$char1 = 18; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1));
			echo "<li><a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</a><br /> - [".T_("LEECHERS").": " . number_format($row["leechers"]) . "]</li>\n";
		}
	echo "</ul></div>\n";
	}
	end_block();
}
?>