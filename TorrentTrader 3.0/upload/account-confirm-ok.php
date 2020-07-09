<?php
// Confirm account OK!
require_once("backend/functions.php");
dbconn();

$type = $_GET["type"];
$email = $_GET["email"];

if (!$type)
	die;


if ($type =="noconf"){//email conf is disabled?
	stdhead(T_("ACCOUNT_ALREADY_CONFIRMED"));
	begin_frame(T_("PLEASE_NOW_LOGIN"));
	print(T_("PLEASE_NOW_LOGIN_REST"));
	end_frame();
	stdfoot();
	die();
}

if ($type == "signup" && validemail($email)) {
	stdhead(T_("ACCOUNT_USER_SIGNUP"));
 begin_frame(T_("ACCOUNT_SIGNUP_SUCCESS"));
        if (!$site_config["ACONFIRM"]) {
            print(T_("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"). " (" . htmlspecialchars($email) . "). " .T_("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"). " <br/ >");
        } else {
            print(T_("EMAIL_CHANGE_SEND"). " (" . htmlspecialchars($email) . "). " .T_("ACCOUNT_CONFIRM_SENT_TO_ADDY_ADMIN"). " <br/ >");
        }
    end_frame();
}
elseif ($type == "confirmed") {
	stdhead(T_("ACCOUNT_ALREADY_CONFIRMED"));
        begin_frame(T_("ACCOUNT_ALREADY_CONFIRMED"));
	print(T_("ACCOUNT_ALREADY_CONFIRMED"). "\n");
	end_frame();
}

//invite code
elseif ($type == "invite" && $_GET["email"]) {
stdhead(T_("INVITE_USER"));
     begin_frame();
		Print("<center>".T_("INVITE_SUCCESSFUL")."!</center><br /><br />".T_("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT")." (" . htmlspecialchars($email) . "). ".T_("THEY_NEED_TO_READ_AND_RESPOND_TO_THIS_EMAIL")."");
	end_frame();
stdfoot();
die;
}//end invite code

elseif ($type == "confirm") {
	if (isset($CURUSER)) {
		stdhead(T_("ACCOUNT_SIGNUP_CONFIRMATION"));
		begin_frame(T_("ACCOUNT_SUCCESS_CONFIRMED"));
		print(T_("ACCOUNT_ACTIVATED"). " <a href='". $site_config["SITEURL"] ."/index.php'>" .T_("ACCOUNT_ACTIVATED_REST"). "\n");
		print(T_("ACCOUNT_BEFOR_USING"). " " . $site_config["SITENAME"] . " " .T_("ACCOUNT_BEFOR_USING_REST")."\n");
		end_frame();
	}
	else {
		stdhead(T_("ACCOUNT_SIGNUP_CONFIRMATION"));
		begin_frame(T_("ACCOUNT_SUCCESS_CONFIRMED"));
		print(T_("ACCOUNT_ACTIVATED"));
		end_frame();
	}
}
else
	die();

stdfoot();
?>
