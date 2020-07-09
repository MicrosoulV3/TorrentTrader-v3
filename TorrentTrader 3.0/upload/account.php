<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

stdhead(T_("USERCP"));

function navmenu(){
?>
	<br />
      <div class="f-border">
		<table cellpadding='0' cellspacing='3' width='100%'>
		<tr class="f-title">
		<th width='100%' height="32" align='center'>
		<?php print("<a href='account.php'><b>".T_("YOUR_PROFILE")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href='account.php?action=edit_settings&amp;do=edit'><b>".T_("YOUR_SETTINGS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href='account.php?action=changepw'><b>".T_("CHANGE_PASS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href='account.php?action=mytorrents'><b>".T_("YOUR_TORRENTS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href='mailbox.php'><b>".T_("YOUR_MESSAGES")."</b></a>");?>
		</th>
        </tr>
		</table>
	  </div>
    <br />
	<?php
}//end func

$action = $_REQUEST["action"];
$do = $_REQUEST["do"];

if (!$action){
	begin_frame(T_("USER").": $CURUSER[username] (".T_("ACCOUNT_PROFILE").")");

	$usersignature = stripslashes($CURUSER["signature"]);

	navmenu();
	?>
	<table class="f-border comment" cellpadding="10" border="0" width="100%">
	<tr>
    <td width="50%" valign="top">
	<b><?php echo T_("USERNAME"); ?>:</b> <?php echo $CURUSER["username"]; ?><br />
	<b><?php echo T_("CLASS"); ?>:</b> <?php echo $CURUSER["level"]; ?><br />
	<b><?php echo T_("EMAIL"); ?>:</b> <?php echo $CURUSER["email"]; ?><br />
	<b><?php echo T_("JOINED"); ?>:</b> <?php echo utc_to_tz($CURUSER["added"]); ?><br />
	<b><?php echo T_("AGE"); ?>:</b> <?php echo $CURUSER["age"]; ?><br />
	<b><?php echo T_("GENDER"); ?>:</b> <?php echo T_($CURUSER["gender"]); ?><br />
	<b><?php echo T_("PREFERRED_CLIENT"); ?>:</b> <?php echo htmlspecialchars($CURUSER["client"]); ?><br />
	<b><?php echo T_("DONATED"); ?>:</b> <?php echo $site_config['currency_symbol']; ?><?php echo number_format($CURUSER["donated"], 2); ?><br />
	<b><?php echo T_("CUSTOM_TITLE"); ?>:</b> <?php echo format_comment($CURUSER["title"]); ?><br />
	<b><?php echo T_("ACCOUNT_PRIVACY_LVL"); ?>:</b> <?php echo T_($CURUSER["privacy"]); ?><br />
	<b><?php echo T_("SIGNATURE"); ?>:</b> <?php echo format_comment($usersignature); ?><br />
	<b><?php echo T_("PASSKEY"); ?>:</b> <?php echo $CURUSER["passkey"]; ?><br />
	<?php
		if ($CURUSER["invited_by"]) {
			$res = SQL_Query_exec("SELECT username FROM users WHERE id=$CURUSER[invited_by]");
			$row = mysqli_fetch_assoc($res);
			echo "<b>".T_("INVITED_BY").":</b> <a href=\"account-details.php?id=$CURUSER[invited_by]\">$row[username]</a><br />";
		}
		echo "<b>".T_("INVITES").":</b> " . number_format($CURUSER["invites"]) . "<br />";
		$invitees = array_reverse(explode(" ", $CURUSER["invitees"]));
		$rows = array();
		foreach ($invitees as $invitee) {
			$res = SQL_Query_exec("SELECT id, username FROM users WHERE id='$invitee' and status='confirmed'");
			if ($row = mysqli_fetch_assoc($res)) {
				$rows[] = "<a href=\"account-details.php?id=$row[id]\">$row[username]</a>";
			}
		}
		if ($rows)
			echo "<b>".T_("INVITED").":</b> ".implode(", ", $rows)."<br />";
	?>
	<?php print("<b>".T_("IP").":</b> " . $CURUSER["ip"] . "\n"); ?><br />
	</td></tr>
	</table>
	<br /><br />
	<?php
	end_frame();
}

/////////////// MY TORRENTS ///////////////////

if ($action=="mytorrents"){
begin_frame(T_("YOUR_TORRENTS"));
navmenu();
//page numbers
$page = (int) $_GET['page'];
$perpage = 200;

$res = SQL_Query_exec("SELECT COUNT(*) FROM torrents WHERE torrents.owner = " . $CURUSER["id"] ."");
$arr = mysqli_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "$i\n";
  else
    $pagemenu .= "<a href='account.php?action=mytorrents&amp;page=$i'>$i</a>\n";

if ($page == 1)
  $browsemenu .= "";
else
  $browsemenu .= "<a href='account.php?action=mytorrents&amp;page=" . ($page - 1) . "'>[Prev]</a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "";
else
  $browsemenu .= "<a href='account.php?action=mytorrents&amp;page=" . ($page + 1) . "'>[Next]</a>";

$offset = ($page * $perpage) - $perpage;
//end page numbers


$where = "WHERE torrents.owner = " . $CURUSER["id"] ."";
$orderby = "ORDER BY added DESC";

$query = SQL_Query_exec("SELECT torrents.id, torrents.category, torrents.name, torrents.added, torrents.hits, torrents.banned, torrents.comments, torrents.seeders, torrents.leechers, torrents.times_completed, categories.name AS cat_name, categories.parent_cat AS cat_parent FROM torrents LEFT JOIN categories ON category = categories.id $where $orderby LIMIT $offset,$perpage");

$allcats = mysqli_num_rows($query);
	if($allcats == 0) {
		echo '<div class="f-border comment"><br /><b>'.T_("NO_UPLOADS").'</b></div>';
	}else{
		print("<p align='center'>$pagemenu<br />$browsemenu</p>");
?>
    <table align="center" cellpadding="5" cellspacing="3" class="table_table" width="100%">
    <tr class="table_head">
        <th><?php echo T_("TYPE"); ?></th>
        <th><?php echo T_("NAME"); ?></th>
        <th><?php echo T_("COMMENTS"); ?></th>
        <th><?php echo T_("HITS"); ?></th>
        <th><?php echo T_("SEEDS"); ?></th>
        <th><?php echo T_("LEECHERS"); ?></th>
        <th><?php echo T_("COMPLETED"); ?></th>
        <th><?php echo T_("ADDED"); ?></th>
        <th><?php echo T_("EDIT"); ?></th>
    </tr>
    
<?php
  
		while($row = mysqli_fetch_assoc($query))
			{
			$char1 = 35; //cut length 
			$smallname = CutName(htmlspecialchars($row["name"]), $char1);
			echo "<tr><td class='table_col2' align='center'>$row[cat_parent]: $row[cat_name]</td><td class='table_col1' align='left'><a href='torrents-details.php?id=$row[id]'>$smallname</a></td><td class='table_col2' align='center'><a href='comments.php?type=torrent&amp;id=$row[id]'>".number_format($row["comments"])."</a></td><td class='table_col1' align='center'>".number_format($row["hits"])."</td><td class='table_col2' align='center'>".number_format($row["seeders"])."</td><td class='table_col1' align='center'>".number_format($row["leechers"])."</td><td class='table_col2' align='center'>".number_format($row["times_completed"])."</td><td class='table_col1' align='center'>".get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"]))."</td><td class='table_col2'><a href='torrents-edit.php?id=$row[id]'>EDIT</a></td></tr>\n";
			}
		echo "</table><br />";
		print("<p align='center'>$pagemenu<br />$browsemenu</p>");
	}

end_frame();
}


/////////////////////// EDIT SETTINGS ////////////////
if ($action=="edit_settings"){

	if ($do=="edit"){
	begin_frame(T_("EDIT_ACCOUNT_SETTINGS"));

	navmenu();
	?>
	<form enctype="multipart/form-data" method="post" action="account.php">
	<input type="hidden" name="action" value="edit_settings" />
	<input type="hidden" name="do" value="save_settings" />
	<table class="f-border" cellspacing="0" cellpadding="5" width="100%" align="center">
	<?php

	$ss_r = SQL_Query_exec("SELECT * from stylesheets");
	$ss_sa = array();
	while ($ss_a = mysqli_fetch_assoc($ss_r))
	{
	  $ss_id = $ss_a["id"];
	  $ss_name = $ss_a["name"];
	  $ss_sa[$ss_name] = $ss_id;
	}
	ksort($ss_sa);
	reset($ss_sa);
	while (list($ss_name, $ss_id) = thisEach($ss_sa))
	{
	  if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected='selected'"; else $ss = "";
	  $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
	}

	$countries = "<option value='0'>----</option>\n";
	$ct_r = SQL_Query_exec("SELECT id,name from countries ORDER BY name");
	while ($ct_a = mysqli_fetch_assoc($ct_r))
	  $countries .= "<option value='$ct_a[id]'" . ($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">$ct_a[name]</option>\n";

	$teams = "<option value='0'>--- ".T_("NONE_SELECTED")." ----</option>\n";
	$sashok = SQL_Query_exec("SELECT id,name FROM teams ORDER BY name");
	while ($sasha = mysqli_fetch_assoc($sashok))
		$teams .= "<option value='$sasha[id]'" . ($CURUSER["team"] == $sasha['id'] ? " selected='selected'" : "") . ">$sasha[name]</option>\n"; 


	$acceptpms = $CURUSER["acceptpms"] == "yes";
	print ("<tr><td align='right' class='alt2'><b>" . T_("ACCEPT_PMS") . ":</b> </td><td class='alt2'><input type='radio' name='acceptpms'" . ($acceptpms ? " checked='checked'" : "") .
	  " value='yes' /><b>".T_("FROM_ALL")."</b> <input type='radio' name='acceptpms'" .
	  ($acceptpms ? "" : " checked='checked'") . " value='no' /><b>" . T_("FROM_STAFF_ONLY") . "</b><br /><i>".T_("ACCEPTPM_WHICH_USERS")."</i></td></tr>");

	$gender = "<option value='Male'" . ($CURUSER["gender"] == "Male" ? " selected='selected'" : "") . ">" . T_("MALE") . "</option>\n"
		 ."<option value='Female'" . ($CURUSER["gender"] == "Female" ? " selected='selected'" : "") . ">" . T_("FEMALE") . "</option>\n";

	// START CAT LIST SQL
	$r = SQL_Query_exec("SELECT id,name,parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
	if (mysqli_num_rows($r) > 0)
	{
		$categories .= "<table><tr>\n";
		$i = 0;
		while ($a = mysqli_fetch_assoc($r))
		{
		  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
		  $categories .= "<td class='bottom' style='padding-right: 5px'><input name='cat$a[id]' type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked='checked'" : "") . " value='yes' />&nbsp;" .htmlspecialchars($a["parent_cat"]).": " . htmlspecialchars($a["name"]) . "</td>\n";
		  ++$i;
		}
		$categories .= "</tr></table>\n";
	}

	// END CAT LIST SQL
	function priv($name, $descr) {
		global $CURUSER;
		if ($CURUSER["privacy"] == $name)
			return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
		return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
	}

	print("<tr><td align='right' class='alt3'><b>" . T_("ACCOUNT_PRIVACY_LVL") . ":</b> </td><td align='left' class='alt3'>". priv("normal", "<b>" . T_("NORMAL") . "</b>") . " " . priv("low", "<b>" . T_("LOW") . "</b>") . " " . priv("strong", "<b>" . T_("STRONG") . "</b>") . "<br /><i>".T_("ACCOUNT_PRIVACY_LVL_MSG")."</i></td></tr>");
	print("<tr><td align='right' class='alt2'><b>" . T_("EMAIL_NOTIFICATION") . ":</b> </td><td align='left' class='alt2'><input type='checkbox' name='pmnotif' " . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "") .
	   " value='yes' /><b>" . T_("PM_NOTIFY_ME") . "</b><br /><i>".T_("EMAIL_WHEN_PM")."</i></td></tr>");

	   //print("<tr><td align=right class=alt3 valign=top><b>".T_("CATEGORY_FILTER").": </b></td><td align=left class=alt3><i>The system will only display the following categories when browsing (uncheck all to disable filter).</i><br />".$categories."</td></tr>");

	print("<tr><td align='right' class='alt3'><b>" . T_("THEME") . ":</b> </td><td align='left' class='alt3'><select name='stylesheet'>\n$stylesheets\n</select></td></tr>");
	print("<tr><td align='right' class='alt2'><b>" . T_("PREFERRED_CLIENT") .":</b> </td><td align='left' class='alt2'><input type='text' size='20' maxlength='20' name='client' value=\"" . htmlspecialchars($CURUSER["client"]) . "\" /></td></tr>");
	print("<tr><td align='right' class='alt3'><b>" . T_("AGE") . ":</b> </td><td align='left' class='alt3'><input type='text' size='3' maxlength='2' name='age' value=\"" . htmlspecialchars($CURUSER["age"]) . "\" /></td></tr>");
	print("<tr><td align='right' class='alt2'><b>" . T_("GENDER") . ":</b> </td><td align='left' class='alt2'><select size='1' name='gender'>\n$gender\n</select></td></tr>");
	print("<tr><td align='right' class='alt3'><b>" . T_("COUNTRY") . ":</b> </td><td align='left' class='alt3'><select name='country'>\n$countries\n</select></td></tr>");

	if ($CURUSER["class"] > 1)
		print("<tr><td align='right' class='alt2'><b>".T_("TEAM").":</b> </td><td align='left' class='alt2'><select name='teams'>\n$teams\n</select></td></tr>");

	print("<tr><td align='right' class='alt3'><b>" . T_("AVATAR_UPLOAD") . ":</b> </td><td align='left' class='alt3'><input type='text' name='avatar' size='50' value=\"" . htmlspecialchars($CURUSER["avatar"]) .
	  "\" /><br />\n<i>".T_("AVATAR_LINK")."</i><br /></td></tr>");
	print("<tr><td align='right' class='alt2'><b>" . T_("CUSTOM_TITLE") . ":</b> </td><td align='left' class='alt2'><input type='text' name='title' size='50' value=\"" . strip_tags($CURUSER["title"]) .
	  "\" /><br />\n <i>" . T_("HTML_NOT_ALLOWED") . "</i></td></tr>");
	print("<tr><td align='right' class='alt3' valign='top'><b>" . T_("SIGNATURE") . ":</b> </td><td align='left' class='alt3'><textarea name='signature' cols='50' rows='10'>" . htmlspecialchars($CURUSER["signature"]) .
	  "</textarea><br />\n <i>".sprintf(T_("MAX_CHARS"), 150).", " . T_("HTML_NOT_ALLOWED") . "</i></td></tr>");

	print("<tr><td align='right' class='alt2'><b>".T_("RESET_PASSKEY").":</b> </td><td align='left' class='alt2'><input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i>".T_("RESET_PASSKEY_MSG").".</i></td></tr>");

    if ($site_config["SHOUTBOX"])
        print("<tr><td align='right' class='table_col3'><b>".T_("HIDE_SHOUT").":</b></td><td align='left' class='table_col3'><input type='checkbox' name='hideshoutbox' value='yes' ".($CURUSER['hideshoutbox'] == 'yes' ? 'checked="checked"' : '')." />&nbsp;<i>".T_("HIDE_SHOUT_TEXT")."</i></td></tr> ");
	
    print("<tr><td align='right' class='alt2'><b>" . T_("EMAIL") . ":</b> </td><td align='left' class='alt2'><input type=\"text\" name=\"email\" size=\"50\" value=\"" . htmlspecialchars($CURUSER["email"]) .
	  "\" /><br />\n<i>".T_("REPLY_TO_CONFIRM_EMAIL")."</i><br /></td></tr>");

	ksort($tzs);
	reset($tzs);
	while (list($key, $val) = thisEach($tzs)) {
	if ($CURUSER["tzoffset"] == $key)
		$tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
	else
		$tz .= "<option value=\"$key\">$val[0]</option>\n";
	}

	print("<tr><td align='right' class='alt3'><b>".T_("TIMEZONE").":</b> </td><td align='left' class='alt3'><select name='tzoffset'>$tz</select></td></tr>");

	?>
	<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("SUBMIT");?>" /> <input type="reset" value="<?php echo T_("REVERT");?>" /></td></tr>
	</table></form>

	<?php
	end_frame();
	}


	if ($do == "save_settings"){
	begin_frame(T_("EDIT_ACCOUNT_SETTINGS"));

	navmenu();
		$set = array();
		  $updateset = array();
		  $changedemail = $newsecret = 0;

          $email = $_POST["email"];
		  if ($email != $CURUSER["email"]) {
				if (!validemail($email))
					$message = T_("NOT_VALID_EMAIL");
				$changedemail = 1;
		  }

		  $acceptpms = $_POST["acceptpms"];
		  $pmnotif = $_POST["pmnotif"];
		  $privacy = $_POST["privacy"];
		  $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
		  $r = SQL_Query_exec("SELECT id FROM categories");
		  $rows = mysqli_num_rows($r);
		  for ($i = 0; $i < $rows; ++$i) {
				$a = mysqli_fetch_assoc($r);
				if ($_POST["cat$a[id]"] == 'yes')
				  $notifs .= "[cat$a[id]]";
		  }

		  if ($_POST['resetpasskey']) $updateset[] = "passkey=''";
          
          $avatar = strip_tags( $_POST["avatar"] );
          
          if ( $avatar != null )
          {    
               # Allowed Image Extenstions.
               $allowed_types = &$site_config["allowed_image_types"];    
              
               # We force http://
               if ( !preg_match( "#^\w+://#i", $avatar ) ) $avatar = "http://" . $avatar;

               # Clean Avatar Path.
               $avatar = cleanstr( $avatar );
               
               # Validate Image.
               $im = @getimagesize( $avatar );
               
               if ( !$im[ 2 ] || !@array_key_exists( $im['mime'], $allowed_types ) )
                     $message = "The avatar url was determined to be of a invalid nature.";
                     
               # Save New Avatar.
               $updateset[] = "avatar = " . sqlesc($avatar);
          }
          
		  $title = strip_tags($_POST["title"]);
		  $signature = $_POST["signature"];
		  $stylesheet = $_POST["stylesheet"];
		  $language = $_POST["language"];
		  $client = strip_tags($_POST["client"]);
		  $age = $_POST["age"];
		  $gender= $_POST["gender"];
		  $country = $_POST["country"];
		  $teams = $_POST["teams"];
		  $privacy = $_POST["privacy"];
		  $timezone = (int)$_POST['tzoffset'];

		  if (is_valid_id($stylesheet))
			$updateset[] = "stylesheet = '$stylesheet'";
		  if (is_valid_id($language))
			$updateset[] = "language = '$language'";
		  if (is_valid_id($teams))
			$updateset[] = "team = '$teams'";
		  if (is_valid_id($country))
			$updateset[] = "country = $country";
		  if ($acceptpms == "yes")
			$acceptpms = 'yes';
		  else
			$acceptpms = 'no';
		  if (is_valid_id($age))
				$updateset[] = "age = '$age'";
          
          $hideshoutbox = ($_POST["hideshoutbox"] == "yes") ? "yes" : "no";

            $updateset[] = "hideshoutbox = ".sqlesc($hideshoutbox);    
			$updateset[] = "acceptpms = ".sqlesc($acceptpms);
			$updateset[] = "commentpm = " . sqlesc($pmnotif == "yes" ? "yes" : "no");
			$updateset[] = "notifs = ".sqlesc($notifs);
			$updateset[] = "privacy = ".sqlesc($privacy);
			$updateset[] = "gender = ".sqlesc($gender);
			$updateset[] = "client = ".sqlesc($client);
			$updateset[] = "signature = ".sqlesc($signature);
			$updateset[] = "title = ".sqlesc($title);
			$updateset[] = "tzoffset = $timezone";

		  /* ****** */

		  if (!$message) {

			if ($changedemail) {
				$sec = mksecret();
				$hash = md5($sec . $email . $sec);
				$obemail = rawurlencode($email);
				$updateset[] = "editsecret = " . sqlesc($sec);
				$thishost = $_SERVER["HTTP_HOST"];
				$thisdomain = preg_replace('/^www\./is', "", $thishost);
$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on {$site_config["SITEURL"]} should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

{$site_config["SITEURL"]}/account-ce.php?id={$CURUSER["id"]}&secret=$hash&email=$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

				sendmail($email, "$site_config[SITENAME] profile update confirmation", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
				$mailsent = 1;
			} //changedemail

			SQL_Query_exec("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]."");
			$edited=1;
			echo "<br /><br /><center><b><font class='error'>Updated OK</font></b></center><br /><br />";
			if ($changedemail) {
				echo "<br /><center><b>".T_("EMAIL_CHANGE_SEND")."</b></center><br /><br />";
			}
		  }else{
			echo "<br /><br /><center><b><font class='error'>".T_("ERROR").": ".$message."</font></b></center><br /><br />";
		  }// message


		end_frame();
	}// end do

}//end action

if ($action=="changepw"){

	if ($do=="newpassword"){

        $chpassword = $_POST['chpassword'];
        $passagain = $_POST['passagain'];

        if ($chpassword != "") {

					if (strlen($chpassword) < 6)
						$message = T_("PASS_TOO_SHORT");
					if ($chpassword != $passagain)
						$message = T_("PASSWORDS_NOT_MATCH");
					$chpassword = passhash($chpassword);
                    $secret = mksecret();
		}

		if ((!$chpassword) || (!$passagain))
			$message = "You must enter something!";

		begin_frame();
		navmenu();

		if (!$message){
			SQL_Query_exec("UPDATE users SET password = " . sqlesc($chpassword) . ", secret = " . sqlesc($secret) . "  WHERE id = " . $CURUSER["id"]);
			echo "<br /><br /><center><b>".T_("PASSWORD_CHANGED_OK")."</b></center>";
			logoutcookie();
		}else{
			echo "<br /><br /><b><center>".$message."</center></b><br /><br />";
		}


		end_frame();
		stdfoot();
		die();
	}//do

	begin_frame(T_("CHANGE_YOUR_PASS"));
	navmenu();
	?>
    
	<form method="post" action="account.php?action=changepw">
	<input type="hidden" name="do" value="newpassword" />
    <div class="f-border">
    <br />
    <table border="0" align="center" cellpadding="10">
    <tr class="alt3">
        <td align="right"><b><?php echo T_("NEW_PASSWORD"); ?>:</b></td>
        <td align="left"><input type="password" name="chpassword" size="50" /></td>
    </tr>
    <tr class="alt3">
        <td align="right"><b><?php echo T_("REPEAT"); ?>:</b></td>
        <td align="left"><input type="password" name="passagain" size="50" /></td>
    </tr>
    <tr class="alt2">
        <td colspan="2" align="center">
        <input type="reset" value="<?php echo T_("REVERT"); ?>" />
        <input type="submit" value="<?php echo T_("SUBMIT"); ?>" />
        </td>
    </tr>
    </table>
    <br />
    </div>
	</form>
    
	<?php
	end_frame();
}

stdfoot();
?>