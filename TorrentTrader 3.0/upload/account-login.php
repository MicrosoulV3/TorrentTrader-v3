<?php
// Updated login for sql security May 14 2023
require_once("backend/functions.php");
dbconn();
session_start();

if (!empty($_REQUEST["returnto"])) {
    if (!isset($_GET["nowarn"])) {
        $nowarn = T_("MEMBERS_ONLY");
    }
}

if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = htmlspecialchars($_POST["username"]);
        $password = htmlspecialchars($_POST["password"]);
        $password_hash = passhash($password);
		$stmt = $GLOBALS["DBconnector"]->prepare("SELECT id, password, secret, status, enabled FROM users WHERE username = ? LIMIT 1");
		$stmt->bind_param("s", $username);
		$stmt->execute();

		if ($stmt->error) {
			die("Query failed: " . $stmt->error);
		}
		$res = $stmt->get_result();
		$row = $res->fetch_assoc();

		if ($row) {
			if ($row["password"] !== $password_hash) {
				$message = T_("LOGIN_INCORRECT");
			} elseif ($row["status"] === "pending") {
				$message = T_("ACCOUNT_PENDING");
			} elseif ($row["enabled"] === "no") {
				$message = T_("ACCOUNT_DISABLED");
        }
    } else {
        $message = T_("NO_EMPTY_FIELDS");
    }
    

    if (!$message) {
        logincookie($row["id"], $row["password"], $row["secret"]);
        if (!empty($_POST["returnto"])) {
            header("Refresh: 0; url=" . $_POST["returnto"]);
            die();
        } else {
            header("Refresh: 0; url=index.php");
            die();
        }
    } else {
        show_error_msg(T_("ACCESS_DENIED"), $message, 1);
    }
}
}
logoutcookie();

stdhead(T_("LOGIN"));

if (isset($nowarn)) {
    show_error_msg(T_("ERROR"), $nowarn, 0);
}


begin_frame(T_("LOGIN"));
?>

<form method="post" action="account-login.php">
    <table border="0" cellpadding="3" align="center">
        <tr>
            <td align="center">
                <b><?php echo T_("USERNAME"); ?>:</b>
                <input type="text" size="40" name="username" />
            </td>
        </tr>
        <tr>
            <td align="center">
                <b><?php echo T_("PASSWORD"); ?>:</b>
                <input type="password" size="40" name="password" />
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="<?php echo T_("LOGIN"); ?>" />
                <br />
                <br />
                <i><?php echo T_("COOKIES");?></i>
            </td>
        </tr>
    </table>

    <?php
    if (!empty($_REQUEST["returnto"])) {
        ?>
        <input type="hidden" name="returnto" value="<?php echo cleanstr($_REQUEST["returnto"]); ?>" />
        <?php
    }
    ?>

</form>

<p align="center">
    <a href="account-signup.php"><?php echo T_("SIGNUP"); ?></a> |
    <a href="account-recover.php"><?php echo T_("RECOVER_ACCOUNT"); ?></a>
</p>

<?php
end_frame();
stdfoot();
?>
