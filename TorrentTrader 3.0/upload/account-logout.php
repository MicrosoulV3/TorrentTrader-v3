<?php
if ($_SERVER['PHP_SELF'] != $_SERVER['REQUEST_URI']) {
    die("Invalid access");
}
 require_once("backend/functions.php");
 dbconn();
 
 logoutcookie();
 header("Location: index.php");
 exit;
?>
