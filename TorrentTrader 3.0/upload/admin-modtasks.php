<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();


if($CURUSER["edit_users"]!="yes")
	show_error_msg(T_("ACCESS_DENIED"),T_("YOU_DONT_HAVE_EDIT_USER_PERM"),1);

$action = $_POST["action"];

if (!$action)
	show_error_msg(T_("ERROR"), T_("TASK_NOT_FOUND"), 1);

if ($action == 'edituser'){
	$userid = $_POST["userid"];
	$title = sqlesc($_POST["title"]);
	$downloaded = strtobytes($_POST["downloaded"]);
	$uploaded = strtobytes($_POST["uploaded"]);
	$signature = sqlesc($_POST["signature"]);
	$avatar = strip_tags($_POST["avatar"]);
	$ip = sqlesc($_POST["ip"]);
	$class = (int) $_POST["class"];
	$donated = (float) $_POST["donated"];
	$password = $_POST["password"];
	$warned = sqlesc($_POST["warned"]);
	$forumbanned = sqlesc($_POST["forumbanned"]);
	$modcomment = sqlesc($_POST["modcomment"]);
	$enabled = sqlesc($_POST["enabled"]);
	$invites =(int) $_POST["invites"];
	$class = (int)$_POST["class"];
	$email = $_POST["email"];

	if (!is_valid_id($userid))
		show_error_msg(T_("EDITING_FAILED"), T_("INVALID_USERID"),1);

	if (!validemail($email))
		show_error_msg(T_("EDITING_FAILED"), T_("EMAIL_ADDRESS_NOT_VALID"), 1);

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
             show_error_msg("Error", "The avatar url was determined to be of a invalid nature.", 1);
    }
    
    # Save New Avatar.
    $avatar = sqlesc($avatar);
          
	//change user class
	$res = SQL_Query_exec("SELECT class FROM users WHERE id=$userid");
	$arr = mysqli_fetch_row($res);
	$uc = $arr[0];

	// skip if class is same as current
	if ($uc != $class && $class > 0) {
		if ($userid == $CURUSER["id"]) {
			show_error_msg(T_("EDITING_FAILED"), T_("YOU_CANT_DEMOTE_YOURSELF"),1);
		} elseif ($uc >= get_user_class()) {
			show_error_msg(T_("EDITING_FAILED"), T_("YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"),1);
		} else {
			@SQL_Query_exec("UPDATE users SET class=$class WHERE id=$userid");
			// Notify user
			$prodemoted = ($class > $uc ? "promoted" : "demoted");
			$msg = sqlesc("You have been $prodemoted to '" . get_user_class_name($class) . "' by " . $CURUSER["username"] . ".");
			$added = sqlesc(get_date_time());
			@SQL_Query_exec("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)");
		}
	}
	//continue updates


	SQL_Query_exec("UPDATE users SET email='$email', title=$title, downloaded='$downloaded', uploaded='$uploaded', signature=$signature, avatar=$avatar, ip=$ip, donated=$donated, forumbanned=$forumbanned, warned=$warned, modcomment=$modcomment, enabled=$enabled, invites=$invites WHERE id=$userid");

	write_log($CURUSER['username']." has edited user: $userid details");

	if ($_POST['resetpasskey']=='yes'){
		SQL_Query_exec("UPDATE users SET passkey='' WHERE id=$userid");
		//write_log($CURUSER['username']." ".T_("PASSKEY")." has been reset for: $userid");
	}

	$chgpasswd = $_POST['chgpasswd']=='yes' ? true : false;
	if ($chgpasswd) {
		$passreq = SQL_Query_exec("SELECT password FROM users WHERE id=$userid");
		$passres = mysqli_fetch_assoc($passreq);
		if($password != $passres['password']){
			$password = passhash($password);
			SQL_Query_exec("UPDATE users SET password='$password' WHERE id=$userid");
			write_log($CURUSER['username']." has changed password for user: $userid");
		}
	}
  
  header("Location: account-details.php?id=$userid");
  die;
}

if ($action == 'addwarning'){
	$userid = (int)$_POST["userid"];
	$reason = mysqli_real_escape_string($GLOBALS["DBconnector"],$_POST["reason"]);
	$expiry = (int)$_POST["expiry"];
	$type = mysqli_real_escape_string($GLOBALS["DBconnector"],$_POST["type"]);

	if (!is_valid_id($userid))
		show_error_msg(T_("EDITING_FAILED"), T_("INVALID_USERID"),1);

	if (!$reason || !$expiry || !$type){
		show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").".", 1);
	}

	$timenow = get_date_time();

	$expiretime = get_date_time(gmtime() + (86400 * $expiry));

	$ret = SQL_Query_exec("INSERT INTO warnings (userid, reason, added, expiry, warnedby, type) VALUES ('$userid','$reason','$timenow','$expiretime','".$CURUSER['id']."','$type')");

	$ret = SQL_Query_exec("UPDATE users SET warned='yes' WHERE id='$userid'");

	$msg = sqlesc("You have been warned by " . $CURUSER["username"] . " - Reason: ".$reason." - Expiry: ".$expiretime."");
	$added = sqlesc(get_date_time());
	@SQL_Query_exec("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)");

	write_log($CURUSER['username']." has added a warning for user: <a href='account-details.php?id=$userid'>$userid</a>");
	header("Location: account-details.php?id=$userid");
	die;
}


if ($action == "deleteaccount"){
    
    if ($CURUSER["delete_users"] != "yes")//only allow admins to delete users
		show_error_msg(T_("ERROR"), T_("TASK_ADMIN"),1);

	$userid = (int)$_POST["userid"];
	$username = sqlesc($_POST["username"]);
	$delreason = sqlesc($_POST["delreason"]);

	if (!is_valid_id($userid))
		show_error_msg(T_("FAILED"), T_("INVALID_USERID"),1);

    if ($CURUSER["id"] == $userid) 
        show_error_msg(T_("ERROR"), "You cannot delete yourself.", 1);
        
	if (!$delreason){
		show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
	}

	deleteaccount($userid);

	write_log($CURUSER['username']." has deleted account: $username");

	show_error_msg(T_("COMPLETED"), T_("USER_DELETE"), 1);
	die;
}

?>