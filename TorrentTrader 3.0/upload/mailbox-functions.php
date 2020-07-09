<?php
function begin_form($action='', $extra='', $method='post')
{
 //if (!$action) $action = substr($_SERVER['REQUEST_URI'], 1);
 print("<form method=\"$method\" action=\"$action\" $extra>\n");
}

function end_form()
{
 print("</form>\n");
}

function submenu($pages, $default, $prefix='', $class='submenu', $space=false)
{
 if ($space) $menu = "<p style=\"text-align:center;\">";
 else $menu = "<table class=\"$class ttable_headinner\" width=\"100%\"><tr class=\"ttable_head\">";
 $arr = explode(',', $pages);
 if ($prefix)
 {
   if (!isset($_GET[$prefix])) $_GET[$prefix] = $default;
   elseif (!preg_match('/(^|,)'.$_GET[$prefix].'(,|$)/i', $pages)) $_GET[$prefix] = $default;
   else $_GET[$prefix] = $_GET[$prefix];
 } else {
   foreach ($arr as $page) if (isset($_GET[$page])) break;
   if (!isset($_GET[$page])) $_GET[$default] = '';
 }
 foreach ($arr as $page)
 {
   $name = ucwords(str_replace("_"," ",$page));
   if (!$space)
   {
     if ((!$prefix && isset($_GET[$page])) || ($prefix && $_GET[$prefix] == $page)) $menu .= '<td style="width:'.floor(100/count($arr)).'%"><b>'.$name.'</b></td>';
     else $menu .= '<td style="width:'.floor(100/count($arr)).'%"><a href="mailbox.php?'.$prefix.($prefix ? '=' : '').$page.'">'.$name.'</a></td>';
   } else {
     if ((!$prefix && isset($_GET[$page])) || ($prefix && $_GET[$prefix] == $page)) $menu .= "&nbsp;<b>$name</b>";
     else $menu .= '&nbsp;<a href="mailbox.php?'.$prefix.($prefix ? '=' : '').$page.'">'.$name.'</a>';
   }
 }
 return $menu.($space ? "</p>" : "</tr></table>");
}

function order($fields, $default, $reverse=false)
{
 if (isset($_GET['sort']))
 {
   $sort = trim($_GET['sort']);
   if (!preg_match('/(^|,)'.$sort.'(,|$)/i', $fields)) unset($sort);
 }
 elseif ($reverse) $_GET['reverse'] = '';
 if (!isset($sort)) $sort = $default;
 $_GET['sort'] = $sort;
 if (strpos($sort,'.')) $sort = implode('`.`', explode('.', $sort));
 return "ORDER BY `$sort`".(isset($_GET['reverse']) ? " DESC" : "");
}

function tr2($x, $y='', $noesc=0, $style='')
{
 print('<tr>');
 if ($y !== '')
 {
   if (!$style) $style = 'text-align:left;';
   print(th_right($x, 1));
   print(td($y, $noesc, $style));
 }
 else
 {
   if (!$style) $style = 'text-align:center;';
   print(td($x, 1, $style.'border:0;', 2));
 }
 print('</tr>');
}

function tr_left($x, $y='', $noesc=0, $style='', $col=1)   { return tr($x, $y, $noesc, 'text-align:left;'.$style, $col); }
function tr_center($x, $y='', $noesc=0, $style='', $col=1) { return tr($x, $y, $noesc, 'text-align:center;'.$style, $col); }
function tr_right($x, $y='', $noesc=0, $style='', $col=1)  { return tr($x, $y, $noesc, 'text-align:right;'.$style, $col); }

function td($x, $noesc=0, $style='', $col=1)
{
 $col = ($col > 1 ? " colspan=\"$col\"" : '');
 if (!$noesc) $x = str_replace("\n", "<br />\n", htmlspecialchars($x));
 if ($style)return "<td style=\"$style\"$col>$x</td>";
 return "<td$col>$x</td>";
}
function td_left($x, $noesc=0, $style='', $col=1)   { return td($x, $noesc, 'text-align:left;'.$style, $col); }
function td_center($x, $noesc=0, $style='', $col=1) { return td($x, $noesc, 'text-align:center;'.$style, $col); }
function td_right($x, $noesc=0, $style='', $col=1)  { return td($x, $noesc, 'text-align:right;'.$style, $col); }

// If field is a string, then it's a sort field, otherwise if a number/true it's don't escape the string, if empty then escape
function th($x, $field=0, $style='', $col=1)
{
 if (is_string($field))
 {
   if (isset($_GET['sort']) && $field != $_GET['sort']) $x = "<a href=\"".add_get('sort',$field)."\">$x</a>";
   elseif (!isset($_GET['reverse'])) $x = "<a href=\"".add_get('reverse')."\">$x</a> &darr;";
   else $x = "<a href=\"".rem_get('reverse')."\">$x</a> &uarr;";
 }
 $col = ($col > 1 ? " colspan=\"$col\"" : '');
 if (!empty($noesc)) $x = str_replace("\n", "<br />\n", htmlspecialchars($x));
 if ($style)return "<th style=\"$style\"$col>$x</th>";
 return "<th>$x</th>";
}
function th_left($x, $noesc=0, $style='', $col=1, $field='')   { return th($x, $noesc, 'text-align:left;'.$style, $col, $field); }
function th_center($x, $noesc=0, $style='', $col=1, $field='') { return th($x, $noesc, 'text-align:center;'.$style, $col, $field); }
function th_right($x, $noesc=0, $style='', $col=1, $field='')  { return th($x, $noesc, 'text-align:right;'.$style, $col, $field); }

function table($arr, $format='', $extratr='')
{
 if ($extratr) print("<tr $extratr>\n");
 else print("<tr>\n");
 if (is_array($arr))
 {
   if (!$format) foreach($arr as $td) print($td."\n");
   else
   {
     $list = explode(',' ,$format);
     foreach($list as $id)
     {
       if (isset($arr[$id])) print($arr[$id]."\n");
       else print("<td>&nbsp;</td>\n");
     }
   }
 }
 else print(substr($arr,0,3).' colspan="'.(substr_count($format, ',') + 1).'"'.substr($arr,3));
 print("</tr>\n");
}

function add_get($key, $value='')
{
 $get = $_GET;
 unset($get[$key]); $get[$key] = ''; // force it to be last!!
 foreach($get as $k => $v)$get2[] = $k . ($v ? '='.urlencode($v) : '');
 return $_SERVER["SCRIPT_NAME"].'?'.implode('&amp;',$get2).($value ? '='.urlencode($value) : '');
}

function rem_get($key)
{
 $get = $_GET;
 unset($get[$key]);
 if (!count($get)) return $_SERVER["SCRIPT_NAME"];
 foreach($get as $k => $v)$get2[] = $k . ($v ? '='.urlencode($v) : '');
 return $_SERVER["SCRIPT_NAME"].'?'.implode('&amp;',$get2);
}

function pager2($rpp, $count, $opts = array())
{
 $href = add_get('page').'=';
 $pages = ceil($count / $rpp);
 if (!@$opts["lastpagedefault"]) $pagedefault = 0;
 else
 {
   $pagedefault = floor(($count - 1) / $rpp);
   if ($pagedefault < 0) $pagedefault = 0;
 }
 if (isset($_GET["page"]))
 {
   $page = (int) $_GET["page"];
   if ($page < 0) $page = $pagedefault;
 }
 else $page = $pagedefault;
 $pager = "";
 $mp = $pages - 1;
 $as = "<b>&lt;&lt;&nbsp;Prev</b>";
 if ($page >= 1) $pager .= '<a href="'.$href.($page - 1).'">'.$as.'</a>';
 else $pager .= $as;
 $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 $as = "<b>".T_("NEXT")."&nbsp;&gt;&gt;</b>";
 if ($page < $mp && $mp >= 0) $pager .= '<a href="'.$href.($page + 1).'">'.$as.'</a>';
 else $pager .= $as;
 if ($count)
 {
   $pagerarr = array();
   $dotted = 0;
   $dotspace = 3;
   $dotend = $pages - $dotspace;
   $curdotend = $page - $dotspace;
   $curdotstart = $page + $dotspace;
   for ($i = 0; $i < $pages; $i++)
   {
     if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend))
     {
       if (!$dotted) $pagerarr[] = "...";
       $dotted = 1;
       continue;
     }
     $dotted = 0;
     $start = $i * $rpp + 1;
     $end = $start + $rpp - 1;
     if ($end > $count) $end = $count;
     $text = "$start&nbsp;-&nbsp;$end";
     if ($i != $page) $pagerarr[] = '<a href="'.$href.$i.'"><b>'.$text.'</b></a>';
     else $pagerarr[] = '<b>'.$text.'</b>';
   }
   $pagerstr = join(" | ", $pagerarr);
   $pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
   $pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
 } else {
   $pagertop = "<p align=\"center\">$pager</p>\n";
   $pagerbottom = $pagertop;
 }
 $start = $page * $rpp;
 return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}
?>