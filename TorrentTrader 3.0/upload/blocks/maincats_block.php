<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
begin_block(T_("BROWSE_TORRENTS"));
	$catsquery = SQL_Query_exec("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
	echo "<div id='maincats' class='bMenu'><ul>\n";
	echo "<li><a href='torrents.php'>".T_("SHOW_ALL")."</a></li>\n";
	while($catsrow = mysqli_fetch_assoc($catsquery)){
		echo "<li><a href='torrents.php?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a></li>\n";
	}
	echo "</ul></div>\n";

end_block();
}
?>
