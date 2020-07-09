<?php function_exists('T_') or die; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $site_config["CHARSET"]; ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="author" content="Nikkbu, TorrentTrader" />
<meta name="generator" content="TorrentTrader <?php echo $site_config['ttversion']; ?>" />
<meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/MySQL Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.org for the support forums. " />
<meta name="keywords" content="http://nikkbu.info, http://www.torrenttrader.org" />
<!-- CSS -->
<!-- PNG FIX for IE6 -->
<!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
<!--[if lte IE 6]>
    <script type="text/javascript" src="<?php echo $site_config["SITEURL"]; ?>/themes/default/js/pngfix/supersleight-min.js"></script>
<![endif]-->
<!-- Theme css -->
<link rel="shortcut icon" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/images/favicon.ico" />
<link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/theme.css" />
<!-- JS -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script> 
<script type="text/javascript" src="<?php echo $site_config["SITEURL"]; ?>/backend/java_klappe.js"></script>
</head>
<body>
    <div class='wrapper'>
      <!-- START HEADER -->
      <div class='header'> <a class='logo' href='index.php' title='Home' target='_self'><img src='<?php echo $site_config["SITEURL"]; ?>/themes/default/images/logo.gif' alt='logo' width='312' height='46' /></a>
        <!-- Start Infobar -->
        <div class='infobar'>
          <?php
                if (!$CURUSER){
                    echo "[<a href=\"account-login.php\">".T_("LOGIN")."</a>]<b> ".T_("OR")." </b>[<a href=\"account-signup.php\">".T_("SIGNUP")."</a>]";
                }else{
                    print (T_("LOGGED_IN_AS").": ".$CURUSER["username"].""); 
                    echo " [<a href=\"account-logout.php\">".T_("LOGOUT")."</a>] ";
                    if ($CURUSER["control_panel"]=="yes") {
                        print("[<a href='admincp.php'>".T_("STAFFCP")."</a>] ");
                    }
            
                    //check for new pm's
                    $res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')");
                    $arr = mysqli_fetch_row($res);
                    $unreadmail = $arr[0];
                    if ($unreadmail){
                        print("[<a href='mailbox.php?inbox'><b><font color='#ff0000'>$unreadmail</font> ".P_("NEWPM", $unreadmail)."</b></a>]");  
                    }else{
                        print("[<a href='mailbox.php'>".T_("YOUR_MESSAGES")."</a>] ");
                    }
                    //end check for pm's
                }
                ?>
        </div>
        <!-- End Infobar -->
      </div>
      <!-- END HEADER -->
      <!-- START NAVIGATION -->
      <div class='navigation'>
        <div class='menu'>
          <ul id='nav-one' class='dropmenu'>
            <li><a href="index.php"><?php echo T_("HOME");?></a></li>
            <li><a href="forums.php"><?php echo T_("FORUMS");?></a></li>
            <li><a href="torrents-upload.php"><?php echo T_("UPLOAD_TORRENT");?></a></li>
            <li><a href="torrents.php"><?php echo T_("BROWSE_TORRENTS");?></a></li>
            <li><a href="torrents-today.php"><?php echo T_("TODAYS_TORRENTS");?></a></li>
            <li><a href="torrents-search.php"><?php echo T_("SEARCH_TORRENTS");?></a></li>
          </ul>
        </div>
      </div>
      <!-- END NAVIGATION -->
  <!-- Start Content -->
  <div id='main'>
    <table cellspacing="0" cellpadding="7" width="100%" border="0">
     <tr>
          <?php if ($site_config["LEFTNAV"]){?>
          <!-- START LEFT COLUM -->
          <td valign="top" width="170">
		  <?php leftblocks();?>
          </td>
          <!-- END LEFT COLUM -->
          <?php } //LEFTNAV ON/OFF END?>
          <!-- START MAIN COLUM -->
          <td valign="top">
