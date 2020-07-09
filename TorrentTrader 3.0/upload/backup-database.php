<?php
//# For Security Purposes if you wish to use it.
//if ( $_SERVER['PHP_AUTH_USER'] != 'username' && $_SERVER['PHP_AUTH_PASS'] != 'password' )
//{
//	  header('WWW-Authenticate: Basic realm="your_site"');
//	  header('HTTP/1.1 401 Unauthorized');
//	  die;
//}
  require_once("backend/functions.php");

  // CONNECT TO THE DATABASE
  dbconn();

  // CHECK THE ADMIN PRIVILEGES
  if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
        show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
  }

  // CREATE THE RANDOM HASH
  $RandomString=chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
  $md5string = md5($RandomString);

  // COMPOSE THE FILENAME
  $curdate = str_replace (" ", "_",  utc_to_tz());
  $filename = 'backups/db-backup_'.$curdate.'_'.$md5string.'.sql';

  // COMPOSE THE HEADER OF THE SQL FILE
  $return = "//\n";
  $return .= "//  TorrentTrader v3.0\n";
  $return .= "//  Database BackUp\n";
  $return .= "//  ".date("y-m-d H:i:s")."\n";
  $return .= "//\n\n";

  // LIST ALL TABLES ON THE DATABASE
  $tables = array();
  $res = SQL_Query('SHOW TABLES');
  $result = $res->execute();
  while($row = mysqli_fetch_row($result))
  {
        $tables[] = $row[0];
  }

  // RETRIEVE THE TABLES
  foreach($tables as $table)
  {
        $result = SQL_Query_exec('SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(SQL_Query_exec('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
        for ($i = 0; $i < $num_fields; $i++)
        {
          while($row = mysqli_fetch_row($result))
          {
                $return.= 'INSERT INTO '.$table.' VALUES(';

                for($j=0; $j<$num_fields; $j++)
                {
                  $row[$j] = addslashes($row[$j]);
                  $row[$j] = preg_replace("/\n/","/\\n/",$row[$j]);
                  if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                  if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
          }
        }
        $return.="\n\n\n";
  }

  // OLD FOPEN/FWRITE/FCLOSE METHOD
  //$handle = fopen($filename,'w+');
  //fwrite($handle,$return);
  //fclose($handle);

  // NEW METHOD TO STORE THE RESULT ON FILES
  $create_error = true;
  if ( file_put_contents($filename, $return) >=  1) { $create_error = false; }
  if ( file_put_contents($filename.'.gz', gzencode( $return,9)) >= 1 ) { $create_error = false; }

  if ($create_error) {
        autolink("admincp.php?action=backups", "Has encountered a error during the backup.<br><br>");
  } else {
        autolink("admincp.php?action=backups", "BackUp Complete.<br><br>");
  }
?>