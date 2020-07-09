<?php
 # For Security Purposes.
 if ( $_SERVER['PHP_SELF'] != $_SERVER['REQUEST_URI'] ) die; 
 
 require_once("backend/functions.php");
 dbconn();
 
 logoutcookie();
 header("Location: index.php");
?>