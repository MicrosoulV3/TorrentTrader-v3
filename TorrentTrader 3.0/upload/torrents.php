<?php
require_once("backend/functions.php");
dbconn();

//check permissions
if ($site_config["MEMBERSONLY"]){
    loggedinonly();

    if($CURUSER["view_torrents"]=="no")
        show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

//get http vars
$addparam = "";
$wherea = array();
$wherea[] = "visible = 'yes'";
$thisurl = "torrents.php?";

if ($_GET["cat"]) {
    $wherea[] = "category = " . sqlesc($_GET["cat"]);
    $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
    $thisurl .= "cat=".urlencode($_GET["cat"])."&amp;";
}

if ($_GET["parent_cat"]) {
    $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
    $thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&amp;";
    $wherea[] = "categories.parent_cat=".sqlesc($_GET["parent_cat"]);
}

$parent_cat = $_GET["parent_cat"];
$category = (int) $_GET["cat"];

$where = implode(" AND ", $wherea);
$wherecatina = array();
$wherecatin = "";
$res = SQL_Query_exec("SELECT id FROM categories");
while($row = mysqli_fetch_array($res)){
    if ($_GET["c$row[id]"]) {
        $wherecatina[] = $row["id"];
        $addparam .= "c$row[id]=1&amp;";
        $thisurl .= "c$row[id]=1&amp;";
    }
    $wherecatin = implode(", ", $wherecatina);
}

if ($wherecatin)
    $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

if ($where != "")
    $where = "WHERE $where";

if ($_GET["sort"] || $_GET["order"]) {

    switch ($_GET["sort"]) {
        case 'name': $sort = "torrents.name"; $addparam .= "sort=name&amp;"; break;
        case 'times_completed':    $sort = "torrents.times_completed"; $addparam .= "sort=times_completed&amp;"; break;
        case 'seeders':    $sort = "torrents.seeders"; $addparam .= "sort=seeders&amp;"; break;
        case 'leechers': $sort = "torrents.leechers"; $addparam .= "sort=leechers&amp;"; break;
        case 'comments': $sort = "torrents.comments"; $addparam .= "sort=comments&amp;"; break;
        case 'size': $sort = "torrents.size"; $addparam .= "sort=size&amp;"; break;
        default: $sort = "torrents.id";
    }

    if ($_GET["order"] == "asc" || ($_GET["sort"] != "id" && !$_GET["order"])) {
        $sort .= " ASC";
        $addparam .= "order=asc&amp;";
    } else {
        $sort .= " DESC";
        $addparam .= "order=desc&amp;";
    }

    $orderby = "ORDER BY $sort";

    }else{
        $orderby = "ORDER BY torrents.id DESC";
        $_GET["sort"] = "id";
        $_GET["order"] = "desc";
    }

//Get Total For Pager
$res = SQL_Query_exec("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $where");
$row = mysqli_fetch_row($res);
$count = $row[0];

//get sql info
if ($count) {
    list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents.php?" . $addparam);
    $query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
    $res = SQL_Query_exec($query);
}else{
    unset($res);
}

stdhead(T_("BROWSE_TORRENTS"));
begin_frame(T_("BROWSE_TORRENTS"));

// get all parent cats
echo "<center><b>".T_("CATEGORIES").":</b> ";
$catsquery = SQL_Query_exec("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
echo " - <a href='torrents.php'>".T_("SHOW_ALL")."</a>";
while($catsrow = mysqli_fetch_assoc($catsquery)){
        echo " - <a href='torrents.php?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a>";
}

?>
<br /><br />
<form method="get" action="torrents.php">
<table align="center">
<tr align='right'>
<?php
$i = 0;
$cats = SQL_Query_exec("SELECT * FROM categories ORDER BY parent_cat, name");
while ($cat = mysqli_fetch_assoc($cats)) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
    print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href='torrents.php?cat={$cat["id"]}'>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
    $i++;
}
echo "</tr><tr align='center'><td colspan='$catsperrow' align='center'><input type='submit' value='".T_("GO")."' /></td></tr>";
echo "</table></form>";

//if we are browsing, display all subcats that are in same cat
if ($parent_cat){
    $thisurl .= "parent_cat=".urlencode($parent_cat)."&amp;";
    echo "<br /><br /><b>".T_("YOU_ARE_IN").":</b> <a href='torrents.php?parent_cat=".urlencode($parent_cat)."'>".htmlspecialchars($parent_cat)."</a><br /><b>".T_("SUB_CATS").":</b> ";
    $subcatsquery = SQL_Query_exec("SELECT id, name, parent_cat FROM categories WHERE parent_cat=".sqlesc($parent_cat)." ORDER BY name");
    while($subcatsrow = mysqli_fetch_assoc($subcatsquery)){
        $name = $subcatsrow['name'];
        echo " - <a href='torrents.php?cat=$subcatsrow[id]'>$name</a>";
    }
}

if (is_valid_id($_GET["page"]))
    $thisurl .= "page=$_GET[page]&amp;";

echo "</center><br /><br />";//some spacing

// New code (TorrentialStorm)
    echo "<div align='right'><form id='sort' action=''>".T_("SORT_BY").": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
    echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">".T_("ADDED")."</option>";
    echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">".T_("NAME")."</option>";
    echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">".T_("COMMENTS")."</option>";
    echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">".T_("SIZE")."</option>";
    echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">".T_("COMPLETED")."</option>";
    echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">".T_("SEEDERS")."</option>";
    echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">".T_("LEECHERS")."</option>";
    echo "</select>&nbsp;";
    echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
    echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">".T_("ASCEND")."</option>";
    echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">".T_("DESCEND")."</option>";
    echo "</select>";
    echo "</form></div>";

// End

if ($count) {
    torrenttable($res);
    print($pagerbottom);
}else {
    
     print("<div class='f-border'>");
     print("<div class='f-cat' width='100%'>".T_("NOTHING_FOUND")."</div>");
     print("<div>");
     print T_("NO_UPLOADS");
     print("</div>");
     print("</div>");
    
}

if ($CURUSER)
    SQL_Query_exec("UPDATE users SET last_browse=".gmtime()." WHERE id=$CURUSER[id]");

end_frame();
stdfoot();
?>