<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
   show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

// ACTION: reorder - reorder sections and items
if ($_GET[action] == "reorder") {
 foreach($_POST[order] as $id => $position) SQL_Query_exec("UPDATE `faq` SET `order`='$position' WHERE id='$id'");
 header("Refresh: 0; url=faq-manage.php"); 
}

// ACTION: edit - edit a section or item
elseif ($_GET[action] == "edit" && is_valid_id($_GET[id])) {
 stdhead(T_("FAQ_MANAGEMENT"));
 begin_frame();
 print("<h1 align=\"center\">Edit Section or Item</h1>");

 $res = SQL_Query_exec("SELECT * FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1");
 while ($arr = mysqli_fetch_array($res, MYSQL_BOTH)) {
  $arr[question] = stripslashes(htmlspecialchars($arr[question]));
  $arr[answer] = stripslashes(htmlspecialchars($arr[answer]));
  if ($arr[type] == "item") {
   print("<form method=\"post\" action=\"faq-actions.php?action=edititem\">");
   print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
   print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
   print("<tr><td class='table_col2'>Question:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
   print("<tr><td class='table_col1' style=\"vertical-align: top;\">Answer:</td><td class='table_col1'><textarea rows='3' cols='35' name=\"answer\">$arr[answer]</textarea></td></tr>\n");
   if ($arr[flag] == "0") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   elseif ($arr[flag] == "2") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   elseif ($arr[flag] == "3") print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
   else print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   print("<tr><td class='table_col1'>Category:</td><td class='table_col1'><select style=\"width: 300px;\" name=\"categ\">");
   $res2 = SQL_Query_exec("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
   while ($arr2 = mysqli_fetch_array($res2, MYSQL_BOTH)) {
    $selected = ($arr2[id] == $arr[categ]) ? " selected=\"selected\"" : "";
    print("<option value=\"$arr2[id]\"". $selected .">$arr2[question]</option>");
   }
   print("</select></td></tr>\n");
   print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
   print("</table></form>");
  }
  elseif ($arr[type] == "categ") {
   print("<form method=\"post\" action=\"faq-actions.php?action=editsect\">");
   print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
   print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
   print("<tr><td class='table_col2'>Title:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"$arr[question]\" /></td></tr>\n");
   if ($arr[flag] == "0") print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option></select></td></tr>");
   else print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
   print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
   print("</table></form>");
  }
 }

 end_frame();
 stdfoot();
}

// subACTION: edititem - edit an item
elseif ($_GET[action] == "edititem" && is_valid_id($_POST[id]) && $_POST[question] != NULL && $_POST[answer] != NULL && is_valid_int($_POST[flag]) && is_valid_id($_POST[categ])) {
 $question = sqlesc($_POST[question]);
 $answer = sqlesc($_POST[answer]);
 SQL_Query_exec("UPDATE `faq` SET `question`=$question, `answer`=$answer, `flag`='$_POST[flag]', `categ`='$_POST[categ]' WHERE id='$_POST[id]'");
 header("Refresh: 0; url=faq-manage.php"); 
}

// subACTION: editsect - edit a section
elseif ($_GET[action] == "editsect" && is_valid_id($_POST[id]) && $_POST[title] != NULL && is_valid_int($_POST[flag])) {
 $title = sqlesc($_POST[title]);
 SQL_Query_exec("UPDATE `faq` SET `question`=$title, `answer`='', `flag`='$_POST[flag]', `categ`='0' WHERE id='$_POST[id]'");
 header("Refresh: 0; url=faq-manage.php"); 
}

// ACTION: delete - delete a section or item
elseif ($_GET[action] == "delete" && isset($_GET[id])) {
 if ($_GET[confirm] == "yes") {
  SQL_Query_exec("DELETE FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1");
  header("Refresh: 0; url=faq-manage.php"); 
 }
 else {
  stdhead(T_("FAQ_MANAGEMENT"));
  begin_frame();
  print("<h1 align=\"center\">Confirmation required</h1>");
  print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\">Please click <a href=\"faq-actions.php?action=delete&amp;id=$_GET[id]&amp;confirm=yes\">here</a> to confirm.</td></tr>\n</table>\n");
  end_frame();
  stdfoot();
 }
}

// ACTION: additem - add a new item
elseif ($_GET[action] == "additem" && $_GET[inid]) {
 stdhead(T_("FAQ_MANAGEMENT"));
 begin_frame();
 print("<h1 align=\"center\">Add Item</h1>");
 print("<form method=\"post\" action=\"faq-actions.php?action=addnewitem\">");
 print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td class='table_col1'>Question:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"\" /></td></tr>\n");
 print("<tr><td class='table_col2' style=\"vertical-align: top;\">Answer:</td><td class='table_col2'><textarea rows='3' cols='35' name=\"answer\"></textarea></td></tr>\n");
 print("<tr><td class='table_col1'>Status:</td><td class='table_col1'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
 print("<tr><td class='table_col2'>Category:</td><td class='table_col2'><select style=\"width: 300px;\" name=\"categ\">");
 $res = SQL_Query_exec("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
 while ($arr = mysqli_fetch_array($res, MYSQL_BOTH)) {
  $selected = ($arr[id] == $_GET[inid]) ? " selected=\"selected\"" : "";
  print("<option value=\"$arr[id]\"". $selected .">$arr[question]</option>");
 }
 print("</select></td></tr>\n");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
 print("</table></form>");
 end_frame();
 stdfoot();
}

// ACTION: addsection - add a new section
elseif ($_GET[action] == "addsection") {
 stdhead(T_("FAQ_MANAGEMENT"));
 begin_frame();
 print("<h1 align=\"center\">Add Section</h1>");
 print("<form method=\"post\" action=\"faq-actions.php?action=addnewsect\">");
 print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td class='table_col1'>Title:</td><td class='table_col1'><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"\" /></td></tr>\n");
 print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\" /></td></tr>\n");
 print("</table></form>");
 end_frame();
 stdfoot();
}

// subACTION: addnewitem - add a new item to the db
elseif ($_GET[action] == "addnewitem" && $_POST[question] != NULL && $_POST[answer] != NULL && is_valid_int($_POST[flag]) && is_valid_int($_POST[categ])) {
 $question = sqlesc($_POST[question]);
 $answer = sqlesc($_POST[answer]);
 $res = SQL_Query_exec("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
 while ($arr = mysqli_fetch_array($res, MYSQL_BOTH)) $order = $arr[0] + 1;
 SQL_Query_exec("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('item', $question, $answer, '$_POST[flag]', '$_POST[categ]', '$order')");
 header("Refresh: 0; url=faq-manage.php"); 
}

// subACTION: addnewsect - add a new section to the db
elseif ($_GET[action] == "addnewsect" && $_POST[title] != NULL && is_valid_int($_POST[flag])) {
 $title = sqlesc($_POST[title]);
 $res = SQL_Query_exec("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
 while ($arr = mysqli_fetch_array($res, MYSQL_BOTH)) $order = $arr[0] + 1;
 SQL_Query_exec("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('categ', $title, '', '$_POST[flag]', '0', '$order')");
 header("Refresh: 0; url=faq-manage.php");
}

else header("Refresh: 0; url=faq-manage.php");
?>