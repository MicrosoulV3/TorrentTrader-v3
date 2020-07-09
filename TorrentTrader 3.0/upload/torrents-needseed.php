<?php
 require_once("backend/functions.php");
 dbconn();
 
 // Check permissions
 if ($site_config["MEMBERSONLY"]) {
     loggedinonly();
     
     if ($CURUSER["view_torrents"] == "no")
         show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
 }  
 
 $res = SQL_Query_exec("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
 
 if (mysqli_num_rows($res) == 0)
     show_error_msg(T_("ERROR"), T_("NO_TORRENT_NEED_SEED"), 1);
     
     stdhead(T_("TORRENT_NEED_SEED"));
     begin_frame(T_("TORRENT_NEED_SEED"));
     
     echo T_("TORRENT_NEED_SEED_MSG");
     
     ?>

     <table cellpadding="5" cellspacing="3" class="table_table" align="center" width="98%">
     <tr>
         <th class="table_head"><?php echo T_("TORRENT_NAME"); ?></th>
         <th class="table_head"><?php echo T_("UPLOADER"); ?></th>
         <th class="table_head"><?php echo T_("LOCAL_EXTERNAL"); ?></th>
         <th class="table_head"><?php echo T_("SIZE"); ?></th>
         <th class="table_head"><?php echo T_("SEEDS"); ?></th>
         <th class="table_head"><?php echo T_("LEECHERS"); ?></th>
         <th class="table_head"><?php echo T_("COMPLETE"); ?></th>
         <th class="table_head"><?php echo T_("ADDED"); ?></th>
     </tr>
     
     <?php 
     
     while ($row = mysqli_fetch_assoc($res)) { 
        
        $type = ($row["external"] == "yes") ? T_("EXTERNAL") : T_("LOCAL"); 

        if ($row["anon"] == "yes" && ($CURUSER["edit_torrents"] == "no" || $CURUSER["id"] != $row["owner"]))
            $owner = T_("ANONYMOUS");
        elseif ($row["username"])
            $owner = "<a href='account-details.php?id=".$row["owner"]."'>".$row["username"]."</a>";
        else
            $owner = T_("UNKNOWN_USER");

        ?>
        
        <tr>
           <td class="table_col1" align="center"><a href="torrents-details.php?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 40) ?></a></td>
           <td class="table_col2" align="center"><?php echo $owner; ?></td>
           <td class="table_col1" align="center"><?php echo $type; ?></td>
           <td class="table_col2" align="center"><?php echo mksize($row["size"]); ?></td>
           <td class="table_col1" align="center"><?php echo number_format($row["seeders"]); ?></td>
           <td class="table_col2" align="center"><?php echo number_format($row["leechers"]); ?></td>
           <td class="table_col1" align="center"><?php echo number_format($row["times_completed"]); ?></td>
           <td class="table_col2" align="center"><?php echo utc_to_tz($row["added"]); ?></td>
        </tr>
        
     <?php
     
     }
     
     ?>
     
     </table>
     
     <?php
     
     end_frame();
     stdfoot();

?>
