<?php
require_once("backend/functions.php");
require_once("mailbox-functions.php");
dbconn();
loggedinonly();

$readme = add_get('read').'=';
$unread = false;

if (isset($_REQUEST['compose'])); // This blocks everything until done...

if (isset($_GET['inbox']))
{
$pagename = T_("INBOX");
$tablefmt = "&nbsp;,Sender,Subject,Date";
$where = "`receiver` = $CURUSER[id] AND `location` IN ('in','both')";
$type = "Mail";
}
elseif (isset($_GET['outbox']))
{
$pagename = "Outbox";
$tablefmt = "&nbsp;,Sent_to,Subject,Date";
$where = "`sender` = $CURUSER[id] AND `location` IN ('out','both')";
$type = "Mail";
}
elseif (isset($_GET['draft']))
{
$pagename = "Draft";
$tablefmt = "&nbsp;,Sent_to,Subject,Date";
$where = "`sender` = $CURUSER[id] AND `location` = 'draft'";
$type = "Mail";
}
elseif (isset($_GET['templates']))
{
$pagename = "Templates";
$tablefmt = "&nbsp;,Subject,Date";
$where = "`sender` = $CURUSER[id] AND `location` = 'template'";
$type = "Mail";
}
else
{
$pagename = "Mail Overview";
$type = "Overview";
}

//****** Send a message, or save after editing ******
if (isset($_POST['send']) || isset($_POST['draft']) || isset($_POST['template']))
{
if (!isset($_POST['template']) && !isset($_POST['change']) && (!isset($_POST['userid']) || !is_valid_id($_POST['userid']))) $error = "Unknown recipient";
else
{
   $sendto = (@$_POST['template'] ? $CURUSER['id'] : @$_REQUEST['userid']);
   if (isset($_POST['usetemplate']) && is_valid_id($_POST['usetemplate']))
   {
     $res = SQL_Query_exec("SELECT * FROM messages WHERE `id` = $_POST[usetemplate] AND `location` = 'template' LIMIT 1");
     $arr = mysqli_fetch_array($res);
     $subject = $arr['subject'].(@$_POST['oldsubject'] ? " (was ".$_POST['oldsubject'].")" : "");
     $msg = sqlesc($arr['msg']);
   } else {
     $subject = @$_POST['subject'];
     $msg = sqlesc(@$_POST['msg']);
   }
   if ($msg)
   {
     $subject = sqlesc($subject);
     if ((isset($_POST['draft']) || isset($_POST['template'])) && isset($_POST['msgid'])) SQL_Query_exec("UPDATE messages SET `subject` = $subject, `msg` = $msg WHERE `id` = $_POST[msgid] AND `sender` = $CURUSER[id]") or die("arghh");
     else
     {
       $to = (@$_POST['draft'] ? 'draft' : (@$_POST['template'] ? 'template' : (@$_POST['save'] ? 'both' : 'in')));
       $status = (@$_POST['send'] ? 'yes' : 'no');
       SQL_Query_exec("INSERT INTO `messages` (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`) VALUES ('$CURUSER[id]', '$sendto', '".get_date_time()."', $subject, $msg, '$status', '$to')") or die("Aargh!");

       // email notif
        $res = SQL_Query_exec("SELECT id, acceptpms, notifs, email FROM users WHERE id='$sendto'");
        $user = mysqli_fetch_assoc($res);

        if (strpos($user['notifs'], '[pm]') !== false) {
            $cusername = $CURUSER["username"];

            $body = "You have received a PM from ".$cusername."\n\nYou can use the URL below to view the message (you may have to login).\n\n    ".$site_config['SITEURL']."/mailbox.php\n\n".$site_config['SITENAME']."";
        
            sendmail($user["email"], "You have received a PM from $cusername", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
        }
       //end email notif

       if (isset($_POST['msgid'])) SQL_Query_exec("DELETE FROM messages WHERE `location` = 'draft' AND `sender` = $CURUSER[id] AND `id` = $_POST[msgid]") or die("arghh");
     }
     if (isset($_POST['send'])) $info = "Message sent successfully".(@$_POST['save'] ? ", a copy has been saved in your Outbox" : "");
     else $info = "Message saved successfully";
   }
   else $error = "Unable to send message";
}
}

//****** Delete a message ******
if (isset($_POST['remove']) && (isset($_POST['msgs']) || is_array($_POST['remove'])))
{
if (is_array($_POST['remove'])) $tmp[] = key($_POST['remove']);
else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
$msgs = implode(', ', $tmp);
if ($msgs)
{
   if (isset($_GET['inbox']))
   {
     SQL_Query_exec("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)");
     SQL_Query_exec("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)");
   } else {                                                                                                                                                                          
     if (isset($_GET['outbox'])) SQL_Query_exec("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($msgs)");
     SQL_Query_exec("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($msgs)");
   }
   $info = count($tmp)." ".P_("message", count($tmp))." deleted";
}
else $error = "No messages to delete";
}

//****** Mark a message as read - only if you're the recipient ******
if (isset($_POST['mark']) && (isset($_POST['msgs']) || is_array($_POST['mark'])))
{
if (is_array($_POST['mark'])) $tmp[] = key($_POST['mark']);
else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
$msgs = implode(', ', $tmp);
if ($msgs)
{
   SQL_Query_exec("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($msgs) AND `receiver` = $CURUSER[id]");
   $info = count($tmp)." ".P_("message",  count($tmp))." marked as read";
}
else $error = "No messages marked as read";
}


stdhead($pagename, false);

if (isset($_REQUEST['compose']))
{
begin_frame("Compose");
$userid = @$_REQUEST['id'];
$subject = ''; $msg = ''; $to = ''; $hidden = ''; $output = ''; $reply = false;
if (is_array($_REQUEST['compose'])) // In reply or followup to another msg
{
   $msgid = key($_REQUEST['compose']);
   if (is_valid_id($msgid))
   {
     $res = SQL_Query_exec("SELECT * FROM `messages` WHERE `id` = $msgid AND '$CURUSER[id]' IN (`sender`,`receiver`) LIMIT 1");
     if ($arr = mysqli_fetch_assoc($res))
     {
       $subject = htmlspecialchars($arr['subject']);
       $msg .= htmlspecialchars($arr['msg']);
       if (current($_REQUEST['compose']) == 'Reply')
       {
         if ($arr['unread'] == 'yes' && $arr['receiver'] == $CURUSER['id']) SQL_Query_exec("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id]");
         $reply = true;
         $userid = $arr['sender'];
         if (substr($arr['subject'],0,4) != 'Re: ') $subject = "Re: $subject";
       }
       else $userid = $arr['receiver'];
       $hidden .= "<input type=\"hidden\" name=\"msgid\" value=\"$msgid\" />";
     }
   }
}
if (isset($_GET['templates'])) $to = 'who cares';
elseif (is_valid_id($userid))
{                                                
    $where = null;
    if ($CURUSER["view_users"] == "no" && $userid != $CURUSER["id"])
        $where = "AND acceptpms = 'yes'";
    
    # Allow users to PM themself's, Privacy is determined on acceptpms - (From All or Staff Only).   
    $res = SQL_Query_exec("SELECT username FROM users WHERE id = $userid AND status = 'confirmed' AND enabled = 'yes' $where");
    $row = mysqli_fetch_assoc($res);
    
    if ( !$row )
    {
          print("You either do not have permission to pm this user, or they don't exist.");
          end_frame();
          stdfoot();
          die;
    }
    
    $to = $row["username"];
        $hidden .= "<input type=\"hidden\" name=\"userid\" value=\"$userid\" />";
    if ($to == $CURUSER["username"])
        $to = "Yourself";
    $to = "<b>$to</b>";
}
else
{
    $where = null;
    if ($CURUSER["view_users"] == "no")
        $where = "AND acceptpms = 'yes'";
       
    # Don't display yourself, Privacy is determined on acceptpms - (From All or Staff Only). 
    $res = SQL_Query_exec("SELECT id, username FROM users WHERE id != $CURUSER[id] AND enabled = 'yes' AND status = 'confirmed' $where ORDER BY username");
    
    if (mysqli_num_rows($res))
    {
        $to = "<select name=\"userid\">\n";
        while ($arr = mysqli_fetch_assoc($res)) $to .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
        $to .= "</select>\n";
    }
   
}
if (isset($_GET['id']) && !$to) print T_("INVALID_USER_ID");
elseif (!isset($_GET['id']) && !$to) print T_("NO_FRIENDS");
else
{
     /******** compose frame ********/

   begin_form(rem_get('compose'),'name="compose"');
   if ($subject) $hidden .= "<input type=\"hidden\" name=\"oldsubject\" value=\"$subject\" />";
        if ($hidden) print($hidden);
    echo "<table width='600px' border='0' align='center' cellpadding='0' cellspacing='0'>";
   if (!isset($_GET['templates'])){
     tr2("To:", $to, 1);
     $res = SQL_Query_exec("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`");
     if (mysqli_num_rows($res))
     {
       $tmp = "<select name=\"usetemplate\" onchange=\"toggleTemplate(this);\">\n<option name=\"0\">---</option>\n";
       while ($arr = mysqli_fetch_assoc($res)) $tmp .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
       $tmp .= "</select><br />\n";
       tr2("Template:", $tmp, 1);
     }
   }
   tr2("Subject:", "<input name=\"subject\" type=\"text\" size=\"60\" value=\"$subject\" />", 1);
//
//   tr2("Message","<textarea name=\"msg\" cols=\"50\" rows=\"15\">$msg</textarea>", 1);
require_once("backend/bbcode.php");
echo "</table>";
print textbbcode("compose","msg","$msg");
echo "<table width='600px' border='0' align='center' cellpadding='4' cellspacing='0'>";

if (!isset($_GET['templates'])) $output .= "<input type=\"submit\" name=\"send\" value=\"Send\" />&nbsp;<label><input type=\"checkbox\" name=\"save\" checked='checked' />Save Copy In Outbox</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"draft\" value=\"Save Draft\" />&nbsp;";
   tr2($output."<input type=\"submit\" name=\"template\" value=\"Save Template\" />");
   echo "</table>";
   end_form();
   end_frame();
   stdfoot();
   die;
}
end_frame();
}

begin_frame($pagename);

echo "<center>";
print submenu('overview,inbox,outbox,compose,draft,templates','overview');
echo "</center><br />";


if ($type == "Overview")
{
begin_table();
$res = SQL_Query_exec("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $CURUSER[id] AND `location` IN ('in','both')");
$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND `location` IN ('in','both')");
$inbox = mysqli_result($res, 0);
   $res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $CURUSER["id"] . " AND `location` IN ('in','both') AND `unread` = 'yes'");
   $unread = mysqli_result($res, 0);
$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` IN ('out','both')");
$outbox = mysqli_result($res, 0);
$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'draft'");
$draft = mysqli_result($res, 0);
$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'template'");
$template = mysqli_result($res, 0);
tr2('<a href="mailbox.php?inbox">'.T_("INBOX").' </a> ', " $inbox ".P_("message", $inbox)." ($unread ".T_("unread").")");
tr2('<a href="mailbox.php?outbox">'.T_("OUTBOX").' </a> ', " $outbox ".P_("message", $outbox));
tr2('<a href="mailbox.php?draft">'.T_("DRAFT").' </a> ', " $draft ".P_("message", $draft));
tr2('<a href="mailbox.php?templates">'.T_("TEMPLATES").' </a> ', " $template ".P_("message", $template));
end_table();
echo"<br /><br />";
}
elseif ($type == "Mail")
{
$order = order("added,sender,sendto,subject", "added", true);
$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE $where");
$count = mysqli_result($res, 0);
list($pagertop, $pagerbottom, $limit) = pager2(20, $count);

print($pagertop);
begin_form();
begin_table(0,"list");
$table['&nbsp;']  = th("<input type=\"checkbox\" onclick=\"toggleChecked(this.checked);this.form.remove.disabled=true;\" />", 1);
$table['Sender']  = th_left("Sender",'sender');
$table['Sent_to'] = th_left("Sent To",'receiver');
$table['Subject'] = th_left("Subject",'subject');
$table['Date']    = th_left("Date",'added');
table($table, $tablefmt);

$res = SQL_Query_exec("SELECT * FROM messages WHERE $where $order $limit");
while ($arr = mysqli_fetch_assoc($res))
{
   unset($table);
   $userid = 0;
   $format = '';
   $reading = false;

   if ($arr["sender"] == $CURUSER['id']) $sender = "Yourself";
   elseif (is_valid_id($arr["sender"]))
   {
     $res2 = SQL_Query_exec("SELECT username FROM users WHERE `id` = $arr[sender]");
     $arr2 = mysqli_fetch_assoc($res2);
     $sender = "<a href=\"account-details.php?id=$arr[sender]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sender = T_("SYSTEM");
//    $sender = $arr['sendername'];

   if ($arr["receiver"] == $CURUSER['id']) $sentto = "Yourself";
   elseif (is_valid_id($arr["receiver"]))
   {
     $res2 = SQL_Query_exec("SELECT username FROM users WHERE `id` = $arr[receiver]");
     $arr2 = mysqli_fetch_assoc($res2);
     $sentto = "<a href=\"account-details.php?id=$arr[receiver]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sentto = T_("SYSTEM");

   $subject = ($arr['subject'] ? htmlspecialchars($arr['subject']) : "no subject");

   if (@$_GET['read'] == $arr['id'])
   {
     $reading = true;
     if (isset($_GET['inbox']) && $arr["unread"] == "yes") SQL_Query_exec("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $CURUSER[id]");
   }
   if ($arr["unread"] == "yes")
   {
     $format = "font-weight:bold;";
     $unread = true;
   }

   $table['&nbsp;']  = th_left("<input type=\"checkbox\" name=\"msgs[$arr[id]]\" ".($reading ? "checked='checked'" : "")." onclick=\"this.form.remove.disabled=true;\" />", 1);
   $table['Sender']  = th_left("$sender", 1, $format);
   $table['Sent_to'] = th_left("$sentto", 1, $format);
   $table['Subject'] = th_left("<a href=\"javascript:read($arr[id]);\"><img src=\"".$site_config["SITEURL"]."/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=\"0\" alt='' /></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</a>", 1, $format);
   $table['Date']    = th_left(utc_to_tz($arr['added']), 1, $format);

   table($table, $tablefmt);

   $display = "<div>".format_comment($arr['msg'])."<br /><br />";
   if (isset($_GET['inbox']) && is_valid_id($arr["sender"]))   $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Reply\" />&nbsp;\n";
   elseif (isset($_GET['draft']) || isset($_GET['templates'])) $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Edit\" />&nbsp;";
   if (isset($_GET['inbox']) && $arr['unread'] == 'yes') $display .= "<input type=\"submit\" name=\"mark[$arr[id]]\" value=\"Mark as Read\" />&nbsp;\n";
   $display .= "<input type=\"submit\" name=\"remove[$arr[id]]\" value=\"Delete\" />&nbsp;\n";
   $display .= "</div>";
   table(td_left($display, 1, "padding:0 6px 6px 6px"), $tablefmt, "id=\"msg_$arr[id]\" style=\"display:none;\"");
}

// if ($count)
//{
   $buttons = "<input type=\"button\" value=\"".T_("SELECTED_DELETE")."\" onclick=\"this.form.remove.disabled=!this.form.remove.disabled;\" />";
   $buttons .= "<input type=\"submit\" name=\"remove\" value=\"...confirm\" disabled=\"disabled\" />";
   if (isset($_GET['inbox']) && $unread) $buttons .= "&nbsp;<input type=\"button\" value=\"Mark Selected as Read\" onclick=\"this.form.mark.disabled=!this.form.mark.disabled;\" /><input type=\"submit\" name=\"mark\" value=\"...confirm\" disabled=\"disabled\" />";
   if (isset($_GET['templates'])) $buttons .= "&nbsp;<input type=\"submit\" name=\"compose\" value=\"Create New Template\" />";
   table(td_left($buttons, 1, "border:0"), $tablefmt);
//}
end_table();
end_form();
print($pagerbottom);
}
end_frame();

stdfoot();
?>