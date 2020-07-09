<?php
require_once("backend/functions.php");
dbconn();

$id = (int) $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

if (!$id || !$md5 || !$email)
	show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);

$res = SQL_Query_exec("SELECT `editsecret` FROM `users` WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `editsecret` != '' AND `id` = '$id'");
$row = mysqli_fetch_assoc($res);

if (!$row)
	show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);

$sec = $row["editsecret"];

if ($md5 != md5($sec . $email . $sec))
	show_error_msg(T_("ERROR"), T_("NOTHING_FOUND"), 1);

SQL_Query_exec("UPDATE `users` SET `editsecret` = '', `email` = ".sqlesc($email)." WHERE `id` = '$id' AND `editsecret` = " . sqlesc($row["editsecret"]));

header("Refresh: 0; url=account.php");
header("Location: account.php");

?>