<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("LATEST_TORRENTS"));

	$expire = 900; // time in seconds

	if (($latestuploadsrecords = $TTCache->Get("latestuploadsblock", $expire)) === false) {
		$latestuploadsquery = SQL_Query_exec("SELECT id, name, size, seeders, leechers FROM torrents WHERE banned='no' AND visible = 'yes' ORDER BY id DESC LIMIT 5");

		$latestuploadsrecords = array();
		while ($latestuploadsrecord = mysqli_fetch_assoc($latestuploadsquery))
			$latestuploadsrecords[] = $latestuploadsrecord;
		$TTCache->Set("latestuploadsblock", $latestuploadsrecords, $expire);
	}

	if ($latestuploadsrecords) {
		foreach ($latestuploadsrecords as $row) { 
			$char1 = 18; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1));
			echo "<a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</a><br />\n";
			echo "- [".T_("SIZE").": ".mksize($row["size"])."]<br /><br />\n";
		}
	} else {
		print("<center>".T_("NOTHING_FOUND")."</center>\n");
	}
	end_block();
}
?>