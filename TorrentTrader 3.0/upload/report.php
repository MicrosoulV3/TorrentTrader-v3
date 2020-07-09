<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

stdhead("Report");

begin_frame("Report");

$takeuser = (int) $_POST["user"];
$taketorrent = (int) $_POST["torrent"];
$takeforumid = (int) $_POST["forumid"];
$takecomment = (int) $_POST["comment"];
$takeforumpost = (int) $_POST["forumpost"];
$takereason = $_POST["reason"];

$user = (int)$_GET["user"];
$torrent = (int)$_GET["torrent"];
$comment = (int)$_GET["comment"];
$forumid = (int)$_GET["forumid"];
$forumpost = (int)$_GET["forumpost"];

//take report user
if (!empty($takeuser)){
    if (empty($takereason)){
        show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
        end_frame();
        stdfoot();
        die;
    }

    $res = SQL_Query_exec("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor = $takeuser AND type = 'user'");

    if (mysqli_num_rows($res) == 0){
        SQL_Query_exec("INSERT into reports (addedby,votedfor,type,reason) VALUES ($CURUSER[id],$takeuser,'user', ".sqlesc($takereason).")");
        print("User: $takeuser, Reason: ".htmlspecialchars($takereason)."<p>Successfully Reported</p>");
        end_frame();
        stdfoot();
        die();
    }else{
        print(T_("YOU_HAVE_ALREADY_REPORTED")." user $takeuser");
        end_frame();
        stdfoot();
        die();
    }
}

//take report torrent
if (($taketorrent !="") && ($takereason !="")){
    if (!$takereason){
        show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
        stdfoot();
        end_frame();
        die;
    }

    $res = SQL_Query_exec("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor = $taketorrent AND type = 'torrent'");
    if (mysqli_num_rows($res) == 0){
        SQL_Query_exec("INSERT into reports (addedby,votedfor,type,reason) VALUES ($CURUSER[id],$taketorrent,'torrent', ".sqlesc($takereason).")");
        print("Torrent: $taketorrent, Reason: ".htmlspecialchars($takereason)."<p>Successfully Reported</p>");
        end_frame();
        stdfoot();
        die();
    }else{
        print(T_("YOU_HAVE_ALREADY_REPORTED")." torrent $taketorrent");
        end_frame();
        stdfoot();
        die();
    }
}

//take report comment
if (($takecomment !="") && ($takereason !="")){
    if (!$takereason){
        show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
        stdfoot();
        end_frame();
        die;
    }

    $res = SQL_Query_exec("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor = $takecomment AND type = 'comment'");
    if (mysqli_num_rows($res) == 0){
        SQL_Query_exec("INSERT into reports (addedby,votedfor,type,reason) VALUES ($CURUSER[id],$takecomment,'comment', ".sqlesc($takereason).")");
        print("Comment: $takecomment, Reason: ".htmlspecialchars($takereason)."<p>Successfully Reported</p>");
        end_frame();
        stdfoot();
        die();
    }else{
        print(T_("YOU_HAVE_ALREADY_REPORTED")." torrent $takecomment");
        end_frame();
        stdfoot();
        die();
    }
}

//take forum post report
if (($takeforumid !="") && ($takereason !="")){
    if (!$takereason){
        show_error_msg(T_("ERROR"), T_("YOU_MUST_ENTER_A_REASON"), 0);
        stdfoot();
        end_frame();
        die;
    }

    $res = SQL_Query_exec("SELECT id FROM reports WHERE addedby = $CURUSER[id] AND votedfor= $takeforumid AND votedfor_xtra= $takeforumpost AND type = 'forum'");

    if (mysqli_num_rows($res) == 0){
        SQL_Query_exec("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason) VALUES ($CURUSER[id],$takeforumid,$takeforumpost ,'forum', ".sqlesc($takereason).")");
        print("User: $takeuser, Reason: ".htmlspecialchars($takereason)."<p>Successfully Reported</p>");
        end_frame();
        stdfoot();
        die();
    }else{
        print(T_("YOU_HAVE_ALREADY_REPORTED")." post $takeforumid");
        end_frame();
        stdfoot();
        die();
    }

}

//report user form
if ($user !=""){
    $res = SQL_Query_exec("SELECT username, class FROM users WHERE id=$user");
    if (mysqli_num_rows($res) == 0){
        print(T_("INVALID_USERID"));
        end_frame();
        stdfoot();
        die();
    }    

    $arr = mysqli_fetch_assoc($res);
    
    print("<b>Are you sure you would like to report user:</b><br /><a href='account-details.php?id=$user'><b>$arr[username]</b></a>?<br />");
    print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>");
    print("<b>Reason</b> (required): <form method='post' action='report.php'><input type='hidden' name='user' value='$user' /><input type='text' size='100' name='reason' /><input type='submit' value='Confirm' /></form>");
    end_frame();
    stdfoot();
    die();
}

//report torrent form
if ($torrent !=""){
    $res = SQL_Query_exec("SELECT name FROM torrents WHERE id=$torrent");

    if (mysqli_num_rows($res) == 0){
        print("Invalid TorrentID");
        end_frame();
        stdfoot();
        die();
    }

    $arr = mysqli_fetch_array($res);
    print("<b>Are you sure you would like to report torrent:</b><br /><a href='torrents-details.php?id=$torrent'><b>$arr[name]</b></a>?<br />");
    print("<b>Reason</b> (required): <form method='post' action='report.php'><input type='hidden' name='torrent' value='$torrent' /><input type='text' size='100' name='reason' /><input type='submit' value='Confirm' /></form>");
    end_frame();
    stdfoot();
    die();
}

//report forum post form
if (($forumid !="") && ($forumpost !="")){
    $res = SQL_Query_exec("SELECT subject FROM forum_topics WHERE id=$forumid");

    if (mysqli_num_rows($res) == 0){
        print("Invalid Forum ID");
        end_frame();
        stdfoot();
        die();
    }

    $arr = mysqli_fetch_array($res);
    print("<b>Are you sure you would like to report the following forum post:</b><br /><a href='forums.php?action=viewtopic&amp;topicid=$forumid&amp;page=p#post$forumpost'><b>$arr[subject]</b></a>?<br />");
    print("<b>Reason</b> (required): <form method='post' action='report.php'><input type='hidden' name='forumid' value='$forumid' /><input type='hidden' name='forumpost' value='$forumpost'><input type='text' size='100' name='reason' /><input type='submit'  value='Confirm' /></form>");
    end_frame();
    stdfoot();
    die;
}

//report comment form
if ($comment !=""){
    $res = SQL_Query_exec("SELECT id, text FROM comments WHERE id=$comment");
    if (mysqli_num_rows($res) == 0){
        print("Invalid Comment");
        end_frame();
        stdfoot();
        die();
    }    

    $arr = mysqli_fetch_assoc($res);
    
    print("<b>Are you sure you would like to report Comment:</b><br /><br /><b>".format_comment($arr["text"])."</b>?<br />");
    print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>");
    print("<b>Reason</b> (required): <form method='post' action='report.php'><input type='hidden' name='comment' value='$comment' /><input type='text' size='100' name='reason' /><input type='submit'  value='Confirm' /></form>");
    end_frame();
    stdfoot();
    die();
}

//error
if (($user !="") && ($torrent !="")){
    print("<h1>".T_("MISSING_INFO")."</h1>");
    end_frame();
    stdfoot();
    die();
}

show_error_msg(T_("ERROR"), T_("MISSING_INFO").".", 0);
end_frame();
stdfoot();
?>