<?php
  require_once("backend/functions.php");
  dbconn();
  loggedinonly();
  
  if ($CURUSER["view_torrents"] == "no")
      show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1); 

  $id = (int) $_GET["id"];
  
  if (isset($_COOKIE["reseed$id"]))
      show_error_msg(T_("ERROR"), T_("RESEED_ALREADY_ASK"), 1);
      
  $res = SQL_Query_exec("SELECT `owner`, `banned`, `external` FROM `torrents` WHERE `id` = $id");
  $row = mysqli_fetch_assoc($res);
  
  if (!$row || $row["banned"] == "yes" || $row["external"] == "yes")
       show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);  
  
  $res2 = SQL_Query_exec("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");

  $message = sprintf(T_('RESEED_MESSAGE'), $CURUSER['username'], $site_config['SITEURL'], $id);
  
  while ( $row2 = mysqli_fetch_assoc($res2) )
  {
      SQL_Query_exec("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('".T_("RESEED_MES_SUBJECT")."', '".$CURUSER['id']."', '".$row2['id']."', '".get_date_time()."', ".sqlesc($message).")");
  }
  
  if ($row["owner"] && $row["owner"] != $CURUSER["id"])
      SQL_Query_exec("INSERT INTO `messages` (`subject`, `sender`, `receiver`, `added`, `msg`) VALUES ('Torrent Reseed Request', '".$CURUSER['id']."', '".$row['owner']."', '".get_date_time()."', ".sqlesc($message).")"); 
      
  setcookie("reseed$id", $id, time() + 86400, '/');
  
  show_error_msg("Complete", T_("RESEED_SENT"), 1);
  
?>