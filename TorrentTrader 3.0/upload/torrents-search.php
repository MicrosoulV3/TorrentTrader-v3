<?php
require_once("backend/functions.php");
dbconn();

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

function sqlwildcardesc($x){
    return str_replace(array("%","_"), array("\\%","\\_"), mysqli_real_escape_string($GLOBALS["DBconnector"],$x));
}

//GET SEARCH STRING
$searchstr = trim($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

$thisurl = "torrents-search.php?";

$addparam = "";
$wherea = array();
$wherecatina = array();
$wherea[] = "banned = 'no'";

$wherecatina = array();
$wherecatin = "";
$res = SQL_Query_exec("SELECT id FROM categories");
while($row = mysqli_fetch_assoc($res)){
    if ($_GET["c$row[id]"]) {
        $wherecatina[] = $row[id];
        $addparam .= "c$row[id]=1&amp;";
        $addparam .= "c$row[id]=1&amp;";
        $thisurl .= "c$row[id]=1&amp;";
    }
    $wherecatin = implode(", ", $wherecatina);
}
if ($wherecatin)
    $wherea[] = "category IN ($wherecatin)";


//include dead
if ($_GET["incldead"] == 1) {
	$addparam .= "incldead=1&amp;";
	$thisurl .= "incldead=1&amp;";
}elseif ($_GET["incldead"] == 2){
	$wherea[] = "visible = 'no'";
	$addparam .= "incldead=2&amp;";
	$thisurl .= "incldead=2&amp;";
}else
	$wherea[] = "visible = 'yes'";

// Include freeleech
if ($_GET["freeleech"] == 1) {
	$addparam .= "freeleech=1&amp;";
	$thisurl .= "freeleech=1&amp;";
	$wherea[] = "freeleech = '0'";
} elseif ($_GET["freeleech"] == 2) {
	$addparam .= "freeleech=2&amp;";
	$thisurl .= "freeleech=2&amp;";
	$wherea[] = "freeleech = '1'";
}



//include external
if ($_GET["inclexternal"] == 1) {
	$addparam .= "inclexternal=1&amp;";
	$wherea[] = "external = 'no'";
}

if ($_GET["inclexternal"] == 2) {
	$addparam .= "inclexternal=2&amp;";
	$wherea[] = "external = 'yes'";
}

//cat
if ($_GET["cat"]) { 
        $wherea[] = "category = " . sqlesc($_GET["cat"]);
		$wherecatina[] = sqlesc($_GET["cat"]);
        $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
	$thisurl .= "cat=".urlencode($_GET["cat"])."&amp;";
}

//language
if ($_GET["lang"]) {
    $wherea[] = "torrentlang = " . sqlesc($_GET["lang"]);
    $addparam .= "lang=" . urlencode($_GET["lang"]) . "&amp;";
    $thisurl .= "lang=".urlencode($_GET["lang"])."&amp;";
}

//parent cat
if ($_GET["parent_cat"]) {
	$addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
	$thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&amp;";
}

$parent_cat = $_GET["parent_cat"];

$wherebase = $wherea;

if (isset($cleansearchstr)) {
	$wherea[] = "MATCH (torrents.name) AGAINST ('".mysqli_real_escape_string($GLOBALS["DBconnector"],$searchstr)."' IN BOOLEAN MODE)";

	$addparam .= "search=" . urlencode($searchstr) . "&amp;";
	$thisurl .= "search=".urlencode($searchstr)."&amp;";
}

//order by
if ($_GET['sort'] && $_GET['order']) {
	$column = '';
	$ascdesc = '';
	switch($_GET['sort']) {
		case 'id': $column = "id"; break;
		case 'name': $column = "name"; break;
		case 'comments': $column = "comments"; break;
		case 'size': $column = "size"; break;
		case 'times_completed': $column = "times_completed"; break;
		case 'seeders': $column = "seeders"; break;
		case 'leechers': $column = "leechers"; break;
		case 'category': $column = "category"; break;
		default: $column = "id"; break;
	}

	switch($_GET['order']) {
		case 'asc': $ascdesc = "ASC"; break;
		case 'desc': $ascdesc = "DESC"; break;
		default: $ascdesc = "DESC"; break;
	}
} else {
	$_GET["sort"] = "id";
	$_GET["order"] = "desc";
	$column = "id";
	$ascdesc = "DESC";
}

	$orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
	$pagerlink = "sort=" . $_GET['sort'] . "&amp;order=" . $_GET['order'] . "&amp;";

if (is_valid_id($_GET["page"]))
	$thisurl .= "page=$_GET[page]&amp;";


$where = implode(" AND ", $wherea);

if ($where != "")
	$where = "WHERE $where";

$parent_check = "";
if ($parent_cat){
	$parent_check = " AND categories.parent_cat=".sqlesc($parent_cat);
}


//GET NUMBER FOUND FOR PAGER
$res = SQL_Query_exec("SELECT COUNT(*) FROM torrents $where $parent_check");
$row = mysqli_fetch_array($res);
$count = $row[0];


if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	$searcha = explode(" ", $cleansearchstr);
	$sc = 0;
	foreach ($searcha as $searchss) {
		if (strlen($searchss) <= 1)
		continue;
		$sc++;
		if ($sc > 5)
		break;
		$ssa = array();
		foreach (array("torrents.name") as $sss)
		$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
		$wherea[] = "(" . implode(" OR ", $ssa) . ")";
	}
	if ($sc) {
		$where = implode(" AND ", $wherea);
		if ($where != "")
		$where = "WHERE $where";
		$res = SQL_Query_exec("SELECT COUNT(*) FROM torrents $where $parent_check");
		$row = mysqli_fetch_array($res);
		$count = $row[0];
	}
}

//Sort by
if ($addparam != "") { 
	if ($pagerlink != "") {
		if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
			$addparam = $addparam . "&amp;" . $pagerlink;
		} else {
			$addparam = $addparam . $pagerlink;
		}
	}
} else {
	$addparam = $pagerlink;
}



if ($count) {

	//SEARCH QUERIES! 
	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents-search.php?" . $addparam);
	$query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $parent_check $orderby $limit";
	$res = SQL_Query_exec($query);

	}else{
		unset($res);
}

if (isset($cleansearchstr))
	stdhead(T_("SEARCH_RESULTS_FOR")." \"" . htmlspecialchars($searchstr) . "\"");
else
	stdhead(T_("BROWSE_TORRENTS"));

begin_frame(T_("SEARCH_TORRENTS"));

// get all parent cats
echo "<center><b>".T_("CATEGORIES").":</b> ";
$catsquery = SQL_Query_exec("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
echo " - <a href='torrents.php'>".T_("SHOWALL")."</a>";
while($catsrow = mysqli_fetch_assoc($catsquery)){
		echo " - <a href='torrents.php?parent_cat=".urlencode($catsrow['parent_cat'])."'>$catsrow[parent_cat]</a>";
}
echo "</center>";

?>
<br /><br />

<center>
<form method="get" action="torrents-search.php">
<table border="0" align="center">
<tr align='right'>
<?php
$i = 0;
$cats = SQL_Query_exec("SELECT * FROM categories ORDER BY parent_cat, name");
while ($cat = mysqli_fetch_assoc($cats)) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
    print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href='torrents.php?cat={$cat["id"]}'>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"]  ? "checked='checked' " : "") . "value='1' /></td>\n");
    $i++;                                                                                                                                                                                                                                                                                                                 
}
echo "</tr></table>";

//if we are browsing, display all subcats that are in same cat
if ($parent_cat){
    echo "<br /><br /><b>".T_("YOU_ARE_IN").":</b> <a href='torrents.php?parent_cat=$parent_cat'>$parent_cat</a><br /><b>".T_("SUB_CATS").":</b> ";
	$subcatsquery = SQL_Query_exec("SELECT id, name, parent_cat FROM categories WHERE parent_cat='$parent_cat' ORDER BY name");
	while($subcatsrow = mysqli_fetch_assoc($subcatsquery)){
		$name = $subcatsrow['name'];
		echo " - <a href='torrents.php?cat=$subcatsrow[id]'>$name</a>";
	}
}	

echo "<br /><br />";//some spacing

?>

    
	<?php print(T_("SEARCH")); ?>
	<input type="text" name="search" size="40" value="<?php echo  stripslashes(htmlspecialchars($searchstr)) ?>" />
	<?php print(T_("IN")); ?>
	<select name="cat">
	<option value="0"><?php echo "(".T_("ALL")." ".T_("TYPES").")";?></option>
	<?php


	$cats = genrelist();
	$catdropdown = "";
	foreach ($cats as $cat) {
		$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
		if ($cat["id"] == $_GET["cat"])
			$catdropdown .= " selected=\"selected\"";
		$catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
	}	
	?>
	<?php echo  $catdropdown ?>
	</select>

	<br /><br />
	<select name="incldead">
 	<option value="0"><?php echo T_("ACTIVE_TRANSFERS"); ?></option>
	<option value="1" <?php if ($_GET["incldead"] == 1) echo "selected='selected'"; ?>><?php echo T_("INC_DEAD");?></option>
	<option value="2" <?php if ($_GET["incldead"] == 2) echo "selected='selected'"; ?>><?php echo T_("ONLY_DEAD");?></option>
	</select>
	<select name="freeleech">
	<option value="0"><?php echo T_("ALL"); ?></option>
	<option value="1" <?php if ($_GET["freeleech"] == 1) echo "selected='selected'"; ?>><?php echo T_("NOT_FREELEECH");?></option>
	<option value="2" <?php if ($_GET["freeleech"] == 2) echo "selected='selected'"; ?>><?php echo T_("ONLY_FREELEECH");?></option>
 	</select>

	<?php if ($site_config["ALLOWEXTERNAL"]){?>
		<select name="inclexternal">
 		<option value="0"><?php echo T_("LOCAL_EXTERNAL"); ?></option>
		<option value="1" <?php if ($_GET["inclexternal"] == 1) echo "selected='selected'"; ?>><?php echo T_("LOCAL_ONLY");?></option>
		<option value="2" <?php if ($_GET["inclexternal"] == 2) echo "selected='selected'"; ?>><?php echo T_("EXTERNAL_ONLY");?></option>
 		</select>
	<?php } ?>

	<select name="lang">
	<option value="0"><?php echo "(".T_("ALL").")";?></option>
	<?php
	$lang = langlist();
	$langdropdown = "";
	foreach ($lang as $lang) {
		$langdropdown .= "<option value=\"" . $lang["id"] . "\"";
		if ($lang["id"] == $_GET["lang"])
			$langdropdown .= " selected=\"selected\"";
		$langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
	}
	
	?>
	<?php echo  $langdropdown ?>
	</select>
	<input type="submit" value="<?php print T_("SEARCH"); ?>" />
	<br />
	</form>
	<?php print T_("SEARCH_RULES"); ?><br />
    </center>
    
<?php

if ($count) {
// New code (TorrentialStorm)
	echo "<form id='sort' action=''><div align='right'>".T_("SORT_BY").": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
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
    echo "</div>";
	echo "</form>";

// End
	torrenttable($res);
	print($pagerbottom);
}else {
    
     print("<div class='f-border'>");
     print("<div class='f-cat' width='100%'>".T_("NOTHING_FOUND")."</div>");
     print("<div>");
     print T_("NO_RESULTS");
     print("</div>");
     print("</div>");
     
}

if ($CURUSER)
	SQL_Query_exec("UPDATE users SET last_browse=".gmtime()." WHERE id=$CURUSER[id]");


end_frame();
stdfoot();

?>
