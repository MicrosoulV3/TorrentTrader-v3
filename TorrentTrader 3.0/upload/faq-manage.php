<?php
require_once("backend/functions.php");
dbconn(false);
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
 show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}

stdhead(T_("FAQ_MANAGEMENT"));
begin_frame(T_("FAQ_MANAGEMENT"));

// make the array that has all the faq in a nice structured
$res = SQL_Query_exec("SELECT `id`, `question`, `flag`, `order` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = mysqli_fetch_array($res)) {
 $faq_categ[$arr['id']]['title'] = $arr['question'];
 $faq_categ[$arr['id']]['flag'] = $arr['flag'];
 $faq_categ[$arr['id']]['order'] = $arr['order'];
}

$res = SQL_Query_exec("SELECT `id`, `question`, `flag`, `categ`, `order` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = mysqli_fetch_array($res)) {
 $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['order'] = $arr['order'];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id][items] as $id2 => $temp) {
    $faq_orphaned[$id2][question] = $faq_categ[$id][items][$id2][question];
    $faq_orphaned[$id2][flag] = $faq_categ[$id][items][$id2][flag];
    unset($faq_categ[$id]);
   }
  }
 }

// print the faq table
 print("<form method=\"post\" action=\"faq-actions.php?action=reorder\">");

 foreach ($faq_categ as $id => $temp) {
  print("<br />\n<table border=\"0\" class=\"table_head\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n");
  print("<tr><th class=\"table_head\" colspan=\"2\">Position</th><th class=\"table_head\">Section/Item ".T_("TITLE").": </th><th class=\"table_head\">Status</th><th class=\"table_head\">Actions</th></tr>\n");

  print("<tr><td class=\"table_col1\" align=\"center\" width=\"40px\"><select name=\"order[". $id ."]\">");
  for ($n=1; $n <= count($faq_categ); $n++) {
   $sel = ($n == $faq_categ[$id][order]) ? " selected=\"selected\"" : "";
   print("<option value=\"$n\"". $sel .">". $n ."</option>");
  }
  $status = ($faq_categ[$id][flag] == "0") ? "<font color=\"red\">Hidden</font>" : "Normal";
  print("</select></td><td class=\"table_col2\" align=\"center\" width=\"40px\">&nbsp;</td><td class=\"table_col1\"><b>". stripslashes($faq_categ[$id][title]) ."</b></td><td class=\"ttable_col2\" align=\"center\" width=\"60px\">". $status ."</td><td class=\"ttable_col1\" align=\"center\" width=\"60px\"><a href=\"faq-actions.php?action=edit&amp;id=". $id ."\">edit</a> <a href=\"faq-actions.php?action=delete&amp;id=". $id ."\">delete</a></td></tr>\n");

  if (array_key_exists("items", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    print("<tr><td class=\"ttable_col1\" align=\"center\" width=\"40px\">&nbsp;</td><td class=\"table_col2\" align=\"center\" width=\"40px\"><select name=\"order[". $id2 ."]\">");
    for ($n=1; $n <= count($faq_categ[$id]['items']); $n++) {
     $sel = ($n == $faq_categ[$id][items][$id2][order]) ? " selected=\"selected\"" : "";
     print("<option value=\"$n\"". $sel .">". $n ."</option>");
    }
    if ($faq_categ[$id][items][$id2][flag] == "0") $status = "<font color=\"#ff0000\">Hidden</font>";
    elseif ($faq_categ[$id][items][$id2][flag] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
    elseif ($faq_categ[$id][items][$id2][flag] == "3") $status = "<font color=\"#008000\">New</font>";
    else $status = "Normal";
    print("</select></td><td class=\"ttable_col1\">". stripslashes($faq_categ[$id][items][$id2][question]) ."</td><td class=\"table_col2\" align=\"center\" width=\"60px\">". $status ."</td><td class=\"ttable_col1\" align=\"center\" width=\"60px\"><a href=\"faq-actions.php?action=edit&amp;id=". $id2 ."\">edit</a> <a href=\"faq-actions.php?action=delete&amp;id=". $id2 ."\">delete</a></td></tr>\n");
   }
  }

  print("<tr><td colspan=\"5\" align=\"center\"><a href=\"faq-actions.php?action=additem&amp;inid=". $id ."\">Add new item</a></td></tr>\n");
  print("</table>\n");
 }
}

// print the orphaned items table
if (isset($faq_orphaned)) {
 print("<br />\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n");
 print("<tr><td align=\"center\" colspan=\"3\"><b style=\"color: #ff0000\">Orphaned Items</b></td>\n");
 print("<tr><td  align=\"left\">Item ".T_("TITLE").": </td><td  align=\"center\">Status</td><td  align=\"center\">Actions</td></tr>\n");
 foreach ($faq_orphaned as $id => $temp) {
  if ($faq_orphaned[$id][flag] == "0") $status = "<font color=\"#ff0000\">Hidden</font>";
  elseif ($faq_orphaned[$id][flag] == "2") $status = "<font color=\"#0000FF\">Updated</font>";
  elseif ($faq_orphaned[$id][flag] == "3") $status = "<font color=\"#008000\">New</font>";
  else $status = "Normal";
  print("<tr><td>". stripslashes($faq_orphaned[$id][question]) ."</td><td align=\"center\" width=\"60px\">". $status ."</td><td align=\"center\" width=\"60px\"><a href=\"faq-actions.php?action=edit&amp;id=". $id ."\">edit</a> <a href=\"faq-actions.php?action=delete&amp;id=". $id ."\">delete</a></td></tr>\n");
 }
 print("</table>\n");
}

print("<br />\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\"><a href=\"faq-actions.php?action=addsection\">Add new section</a></td></tr>\n</table>\n");
print("<p align=\"center\"><input type=\"submit\" name=\"reorder\" value=\"Reorder\" /></p>\n");
print("</form>\n");
print("When the position numbers don't reflect the position in the table, it means the order id is bigger than the total number of sections/items and you should check all the order id's in the table and click \"reorder\"\n");
echo $pagerbottom;

end_frame();
stdfoot();
?>
