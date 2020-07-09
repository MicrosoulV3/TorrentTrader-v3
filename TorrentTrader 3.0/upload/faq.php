<?php
require_once("backend/functions.php");
dbconn();

stdhead(T_("FAQ"));

$faq_categ = null;

$res = SQL_Query_exec("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = mysqli_fetch_array($res, MYSQLI_BOTH)) {
 $faq_categ[$arr['id']]['title'] = $arr['question'];
 $faq_categ[$arr['id']]['flag'] = $arr['flag'];
}

$res = SQL_Query_exec("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = mysqli_fetch_array($res, MYSQLI_BOTH)) {
 $faq_categ[$arr['categ']]['items'][$arr['id']]['question'] = $arr['question'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
 $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
    $faq_orphaned[$id2]['question'] = $faq_categ[$id]['items'][$id2]['question'];
	$faq_orphaned[$id2]['answer'] = $faq_categ[$id]['items'][$id2]['answer'];
    $faq_orphaned[$id2]['flag'] = $faq_categ[$id]['items'][$id2]['flag'];
    unset($faq_categ[$id]);
   }
  }
 }
 
 begin_frame(T_("CONTENTS"));
  print("<a name='top'></a>"); 
 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
   //print("<ul>\n<li><a href=\"#". $id ."\"><b>". $faq_categ[$id]['title'] ."</b></a>\n<ul>\n");
   
   print("<ul style='list-style: none'>\n<li><a href=\"#section". $id ."\"><b>". stripslashes($faq_categ[$id]['title']) ."</b></a>\n<ul style='list-style: none'>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] == "1") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a></li>\n");
	 elseif ($faq_categ[$id]['items'][$id2]['flag'] == "2") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src=\"".$site_config["SITEURL"]."/images/faq/updated.png\" alt=\"Updated\" width=\"46\" height=\"13\" align=\"bottom\" /></li>\n");
	 elseif ($faq_categ[$id]['items'][$id2]['flag'] == "3") print("<li><a href=\"#section". $id2 ."\">". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</a> <img src=\"".$site_config["SITEURL"]."/images/faq/new.png\" alt=\"New\" width=\"25\" height=\"12\" align=\"bottom\" /></li>\n");
    }
   }
   print("</ul>\n</li>\n</ul>\n<br />\n");
  }
 }
 end_frame();

 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id]['flag'] == "1") {
   $frame = $faq_categ[$id]['title'] ." - <a href=\"#top\">Top</a>";
   begin_frame($frame);
   print("<a id=\"section". $id ."\"></a>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id]['items'] as $id2 => $temp) {
	 if ($faq_categ[$id]['items'][$id2]['flag'] != "0") {
      print("<br />\n<b>". stripslashes($faq_categ[$id]['items'][$id2]['question']) ."</b><a id=\"section". $id2 ."\"></a>\n<br />\n");
      print("<br />\n". stripslashes($faq_categ[$id]['items'][$id2]['answer']) ."\n<br /><br />\n");
	 }
    }
   }
   end_frame();
  }
 }

}


stdfoot();
?>
