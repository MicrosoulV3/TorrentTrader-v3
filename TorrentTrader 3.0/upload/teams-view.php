<?php
 require_once("backend/functions.php"); 
 dbconn(); 

 if ($site_config["MEMBERSONLY"]) {
     loggedinonly();
 }
 
 # Possibly Add Caching, Pagination...
 $res = SQL_Query_exec("SELECT teams.id, teams.name, teams.image, teams.info, teams.owner, teams.added, users.username, (SELECT GROUP_CONCAT(id, ' ', username) FROM users WHERE FIND_IN_SET(users.team, teams.id) AND users.enabled = 'yes' AND users.status = 'confirmed') AS members FROM teams LEFT JOIN users ON teams.owner = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed'");
                                                 
 if (mysqli_num_rows($res) == 0)
     show_error_msg(T_("ERROR"), "No teams available, to create a group please contact <a href='staff.php'>staff</a>.", 1);
     
 stdhead("Teams View");
 begin_frame("Teams View");
 
 echo '<center>Please <a href="staff.php">contact</a> a member of staff if you would like a new team creating</center><br />';
 
 while ($row = mysqli_fetch_assoc($res)):
 ?>
  
 <table border="0" cellspacing="0" cellpadding="3" width="100%" class="table_table">
 <tr>
     <th class="table_head" width="10%"></th>
     <th class="table_head" width="90%">Owner: <?php echo ( $row["username"] ) ? '<a href="account-details.php?id='.$row["owner"].'">'.htmlspecialchars($row["username"]).'</a>' : "Unknown User"; ?> - Added: <?php echo utc_to_tz($row["added"]); ?></th>
 </tr>
 <tr>
     <td class="table_col1" width="10%"><img src="<?php echo htmlspecialchars($row["image"]); ?>" border="0" alt="<?php echo htmlspecialchars($row["name"]); ?>" title="<?php echo htmlspecialchars($row["name"]); ?>" /></td>
     <td class="table_col2" width="90%" valign="top" align="left"><b>Name:</b><?php echo htmlspecialchars($row["name"]); ?><br /><b>Info:</b> <?php echo format_comment($row["info"]); ?></td>
 </tr>
 <tr>
    <td class="table_col1" colspan="2">
    <b>Members:</b> 
    <?php foreach ( explode(',', $row['members']) as $member ): $member = explode(" ", $member); ?>
    <a href="account-details.php?id=<?php echo $member[0]; ?>"><?php echo htmlspecialchars($member[1]); ?></a>,
    <?php endforeach; ?>
    </td>
 </tr>
 </table>
 <br />
 
 <?php
 endwhile;
 
 end_frame();
 stdfoot();
 
?>