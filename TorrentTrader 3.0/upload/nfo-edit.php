<?php
  require_once("backend/functions.php");
  dbconn();
  loggedinonly();
  
  error_reporting(0);
                         
  if ($CURUSER["edit_torrents"] == "no")
      show_error_msg(T_("ERROR"), T_("NFO_PERMISSION"), 1);
  
  $id = ( int ) cleanstr($_REQUEST["id"]); 
  $do = $_POST["do"];
  
  $nfo = $site_config["nfo_dir"] . "/$id.nfo";
  
  if ($do == "update") { 
                                                                   
      if ( is_file( $nfo ) )  
      {
           file_put_contents( $nfo, $_POST['content'] );
           
           write_log("NFO ($id) was updated by $CURUSER[username].");
        
           show_error_msg(T_("NFO_UPDATED"), T_("NFO_UPDATED"), 1);
      }
  }
  
  if ($do == "delete") {   
      
      $reason = htmlspecialchars($_POST["reason"]);

      if (get_row_count("torrents", "WHERE `nfo` = 'yes' AND `id` = $id"))
      {
          unlink($nfo);
          write_log("NFO ($id) was deleted by $CURUSER[username] $reason");
          SQL_Query_exec("UPDATE `torrents` SET `nfo` = 'no' WHERE `id` = $id");
          show_error_msg(T_("NFO_DELETED"), T_("NFO_DELETED"), 1);
      }
      
      show_error_msg(T_("ERROR"), sprintf(T_("NFO_NOT_EXIST"), $id), 1);
  } 
  
  if ((!is_valid_id($id)) || (!$contents = file_get_contents($nfo))) {
       show_error_msg(T_("ERROR"), T_("NFO_NOT_FOUND"), 1);
  }

  stdhead(T_("NFO_EDITOR"));
  begin_frame(T_("NFO_EDIT"));
  ?>
  
  <center>
  <form method="post" action="nfo-edit.php">
  <input type="hidden" name="id" value="<?php echo $id; ?>" />
  <input type="hidden" name="do" value="update" />
  <textarea class="nfo" name="content" cols="100%" rows="80"><?php echo htmlspecialchars(stripslashes($contents)); ?></textarea><br />
  <input type="reset" value="<?php echo T_("RESET"); ?>" />
  <input type="submit" value="<?php echo T_("SAVE"); ?>" />
  </form>
  </center>
  
  <?php
  end_frame();
  
  begin_frame(T_("NFO_DELETE"));
  ?>
  
  <center>
  <form method="post" action="nfo-edit.php">
  <input type="hidden" name="id" value="<?php echo $id; ?>" />
  <input type="hidden" name="do" value="delete" />
  <b><?php echo T_("NFO_REASON"); ?>:</b> <input type="text" name="reason" size="40" />
  <input type="submit" value="<?php echo T_("DEL"); ?>" />
  </form>
  </center>
  
  <?php
  end_frame();
  
  stdfoot();

?>