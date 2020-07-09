<?php
require_once("backend/functions.php");
dbconn();

if (!empty($_REQUEST["returnto"])) {
	if (!$_GET["nowarn"]) {    
		 $nowarn = T_("MEMBERS_ONLY");
	}
}

if ($_POST["username"] && $_POST["password"]) {
	$password = passhash($_POST["password"]);

	if (!empty($_POST["username"]) && !empty($_POST["password"])) {
		$res = SQL_Query_exec("SELECT id, password, secret, status, enabled FROM users WHERE username = " . sqlesc($_POST["username"]) . "");
		$row = mysqli_fetch_assoc($res);

		if ( ! $row || $row["password"] != $password )
			$message = T_("LOGIN_INCORRECT");
		elseif ($row["status"] == "pending")
			$message = T_("ACCOUNT_PENDING");
		elseif ($row["enabled"] == "no")
			$message = T_("ACCOUNT_DISABLED");
	} else
		$message = T_("NO_EMPTY_FIELDS");

	if (!$message){
		logincookie($row["id"], $row["password"], $row["secret"]);
		if (!empty($_POST["returnto"])) {
			header("Refresh: 0; url=" . $_POST["returnto"]);
			die();
		}
		else {
			header("Refresh: 0; url=index.php");
			die();
		}
	}else{
		show_error_msg(T_("ACCESS_DENIED"), $message, 1);
	}
}

logoutcookie();

stdhead(T_("LOGIN"));
 
 if ($nowarn)
      show_error_msg(T_("ERROR"), $nowarn, 0);
      
begin_frame(T_("LOGIN"));

?>

<form method="post" action="account-login.php">
  <table border="0" cellpadding="3" align="center">
		<tr><td align="center"><b><?php echo T_("USERNAME"); ?>:</b> <input type="text" size="40" name="username" /></td></tr>
		<tr><td align="center"><b><?php echo T_("PASSWORD"); ?>:</b> <input type="password" size="40" name="password" /></td></tr>
		<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("LOGIN"); ?>" /><br /><br /><i><?php echo T_("COOKIES");?></i></td></tr>
	</table>
<?php

if ( ! empty($_REQUEST["returnto"]) )
{ 
      print("<input type=\"hidden\" name=\"returnto\" value=\"" . cleanstr($_REQUEST["returnto"]) . "\" />\n");
}
?>

</form>
<p align="center"><a href="account-signup.php"><?php echo T_("SIGNUP"); ?></a> | <a href="account-recover.php"><?php echo T_("RECOVER_ACCOUNT"); ?></a></p>

<?php
end_frame();
stdfoot();
?>