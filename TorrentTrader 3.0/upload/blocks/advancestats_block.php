<?php
$date_time = get_date_time(gmtime()-(3600*24)); // the 24hrs is the hours you want listed
$registered = number_format(get_row_count("users"));
$ncomments = number_format(get_row_count("comments"));
$nmessages = number_format(get_row_count("messages"));
$ntor = number_format(get_row_count("torrents"));
$totaltoday = number_format(get_row_count("users", "WHERE users.last_access>='$date_time'"));
$regtoday = number_format(get_row_count("users", "WHERE users.added>='$date_time'"));
$todaytor = number_format(get_row_count("torrents", "WHERE torrents.added>='$date_time'"));
$guests = number_format(getguests());
$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");
$members = number_format(get_row_count("users", "WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(users.last_access) < 900"));
$totalonline = $members + $guests;

$result = SQL_Query_exec("SELECT SUM(downloaded) AS totaldl FROM users"); 
while ($row = mysqli_fetch_array ($result)) { 
	$totaldownloaded = $row["totaldl"]; 
} 

$result = SQL_Query_exec("SELECT SUM(uploaded) AS totalul FROM users"); 
while ($row = mysqli_fetch_array ($result)) { 
	$totaluploaded      = $row["totalul"]; 
}
$localpeers = $leechers+$seeders;
if($CURUSER["edit_users"]=="yes") {
begin_block(T_("STATS"));

    echo "<div align='left'>";
echo "<b>".T_("TORRENTS")."</b>";
echo "<br /><small>".T_("TRACKING").":<b> $ntor ".P_("TORRENT", $ntor)."</b></small>";
echo "<br /><small>".T_("NEW_TODAY").":<b> " . $todaytor . "</b></small>";
echo "<br /><small>".T_("SEEDERS").":<b> " . number_format($seeders) . "</b></small>";
echo "<br /><small>".T_("LEECHERS").":<b> " . number_format($leechers) . "</b></small>";
echo "<br /><small>".T_("PEERS").":<b> " . number_format($localpeers) . "</b></small>";
echo "<br /><small>".T_("DOWNLOADED").":<b> " . mksize($totaldownloaded) . "</b></small>";
echo "<br /><small>".T_("UPLOADED").":<b> " . mksize($totaluploaded) . "</b></small>";
echo "<br /><br /><b>".T_("MEMBERS")."</b>";
echo "<br /><small>".T_("WE_HAVE").":<b> $registered ".P_("MEMBER", $registered)."</b></small>";
echo "<br /><small>".T_("NEW_TODAY").":<b> " . $regtoday . "</b></small>";
echo "<br /><small>".T_("VISITORS_TODAY").": <b>" . $totaltoday . "</b></small>";
echo "<br /><br /><b>".T_("ONLINE")."</b>";
echo "<br /><small>".T_("TOTAL_ONLINE").":<b> " . $totalonline . "</b></small>";
echo "<br /><small>".T_("MEMBERS").":<b> " . $members . "</b></small>";
echo "<br /><small>".T_("GUESTS_ONLINE").":<b> " . $guests . "</b></small>";
echo "<br /><small>".T_("COMMENTS_POSTED").":<b> " . $ncomments . "</b></small>";
echo "<br /><small>".T_("MESSAGES_SENT").":<b> " . $nmessages . "</b></small>";
echo "<br /><br /></div>";
end_block();
}
if($CURUSER["edit_users"]=="no") {
begin_block(T_("STATS"));
    echo "<div align='left'>";
echo "<b>".T_("TORRENTS")."</b>";
echo "<br /><small>".T_("TRACKING").":<b> $ntor ".P_("TORRENT", $ntor)."</b></small>";
echo "<br /><small>".T_("NEW_TODAY").":<b> " . $todaytor . "</b></small>";
echo "<br /><br /></div>";
end_block();
}
?>