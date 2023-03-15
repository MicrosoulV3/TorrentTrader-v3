<?php
//MYSQL CONNECTION INFO, DONT PASS IT OUT!

//Access Security check
if (basename($_SERVER['PHP_SELF']) === 'mysql.php') {
    die;
}

//Change the settings below to match your MYSQL server connection settings
$mysql_host = "localhost";  //leave this as localhost if you are unsure
$mysql_user = "tt";  //Username to connect
$mysql_pass = "tt"; //Password to connect
$mysql_db = "tt";  //Database name
?>
