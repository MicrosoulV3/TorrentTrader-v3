<?php
  require_once("backend/functions.php");
  dbconn();
  loggedinonly();
  
  $dt = get_date_time(gmtime() - 180);
  
  $res = SQL_Query_exec("SELECT `users`.`id`, `users`.`username`, `users`.`class`, `users`.`last_access` FROM `users` INNER JOIN `groups` ON `users`.`class` = `groups`.`group_id`  WHERE `users`.`enabled` = 'yes' AND `users`.`status` = 'confirmed' AND `groups`.`staff_page` = 'yes' ORDER BY `username`");
  while ( $row = mysqli_fetch_assoc( $res ) )
  {
      $table[$row["class"]] = $table[$row["class"]] .
        "<td><img src='images/button_o".($row["last_access"] > $dt ? "n" : "ff")."line.png' alt='' /> ". 
        "<a href='account-details.php?id=".$row["id"]."'>".$row["username"]."</a> ".       
        "<a href='mailbox.php?compose&amp;id=".$row["id"]."'><img src='images/button_pm.gif' border='0' alt='' /></a></td>";
        
      ++$col[$row["class"]];
      
      if ($col[$row["class"]] <= 4)
          $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
      else 
      {
          $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
          $col[$row["class"]] = 2;
      }
  }

  $where = null;
  if ($CURUSER["edit_users"] == "no")
      $where = "AND `staff_public` = 'yes'";
  
  $res = SQL_Query_exec("SELECT `group_id`, `level`, `staff_public` FROM `groups` WHERE `staff_page` = 'yes' $where ORDER BY `staff_sort`");

  if (mysqli_num_rows($res) == 0)
      show_error_msg(T_("ERROR"), T_("NO_STAFF_HERE"), 1);
      
  stdhead(T_("STAFF"));
  begin_frame(T_("STAFF"));
  ?>
  
  <table cellpadding="0" width="100%" align="center">
  <?php while ($row = mysqli_fetch_assoc($res)): if ( !isset($table[$row["group_id"]]) ) continue; ?>
  <tr>
      <td colspan="14"><b><?php echo T_($row["level"]); ?></b> <?php if ($row["staff_public"] == "no") echo("<font color='#ff0000'>[".T_("HIDDEN FROM PUBLIC")."]</font>"); ?></td>
  </tr>
  <tr>
      <td colspan="14"><hr /></td>
  </tr>
  <tr>
      <?php echo $table[$row["group_id"]]; ?>
  </tr>
  <tr>
      <td colspan="14"></td>
  </tr>
  <?php endwhile; ?>
  </table>
  
  <?php
  end_frame();
  stdfoot();

?>
