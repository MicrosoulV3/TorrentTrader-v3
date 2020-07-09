<?php
require_once("backend/functions.php");
dbconn(); 

if (isset($_GET["custom"])){
	stdhead(T_("CUSTOM_RSS_XML_FEED"));
	begin_frame(T_("CUSTOM_RSS_XML_FEED"));

	$rqt = "SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC";
	$resqn = SQL_Query_exec($rqt);

	if ($_POST) {
		$params = array();

		if ($cats = $_POST["cats"]) {
			$catlist = array();
			foreach ($cats as $cat) {
				if (is_numeric($cat)) {
					$catlist[] = $cat;
				}
			}
			if ($catlist)
				$params[] = "cat=".implode(",", $catlist);
		}

		if ($_POST["incldead"])
			$params[] = "incldead=1";

		if ($_POST["dllink"])
			$params[] = "dllink=1";

		if (!$_POST["cookies"] && $CURUSER)
			$params[] = "passkey=$CURUSER[passkey]";

		if ($params)
			$param = "?".implode("&amp;", $params);
		else
			$param = "";

		echo "Your RSS link is: <a href=\"$site_config[SITEURL]/rss.php$param\">$site_config[SITEURL]/rss.php$param</a><br/><br/>";
	}
	?>
	What is RSS? Take a look at the <a href="http://wikipedia.org/wiki/RSS_%28file_format%29">Wiki</a> to <a href="http://wikipedia.org/wiki/RSS_%28file_format%29">learn more</a>.<br /><br />

	<form action="rss.php?custom" method="post">
	<table border="0" cellpadding="5" cellspacing="0" class="table_table">
	<tr>
		<td class="table_col1" valign="top">Categories:</td>
		<td class="table_col2" valign="top">(Leave blank for All)<br /><br />
		<?php while ($row = mysqli_fetch_array($resqn)) {
			echo '<input type="checkbox" name="cats[]" value="'.$row['id'].'" /> '.htmlspecialchars("$row[parent_cat] - $row[name]").'<br />';
		}
		?>
		</td>
	</tr>
	<tr>
		<td class="table_col1"><?php echo T_("FEED_TYPE");?>:</td>
		<td class="table_col2">
			<input type="radio" name="dllink" value="0" checked="checked" />Details link<br />
			<input type="radio" name="dllink" value="1" /> Download link<br />
		</td>
	</tr>
	<tr>
		<td class="table_col1"><?php echo T_("LOGIN_TYPE");?>:</td>
		<td class="table_col2">
			<input type="radio" name="cookies" value="1" checked="checked" /> Standard (cookies)<br/>
			<input type="radio" name="cookies" value="0" /> Alternative (no cookies)<br/>
        </td>
	</tr>
	<tr>
		<td class="table_col1"><?php echo T_("INCLUDE_DEAD");?>:</td>
		<td class="table_col2"><input type="checkbox" name="incldead" value="1" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="Get Link" /></td>
	</tr>
	</table>
	</form>
	<br /><br />
	<div align="left">
	Quick information regarding our RSS:
	<ul>
	<li>Our RSS feeds are properly validated by true RSS 2.0 XML Parsing Standards. Visit FeedValidator.org to validate.</li>
	<li>Our feeds display only the latest 50 uploaded Torrents as default.</li>
	</ul>
	</div>
	<?php
	end_frame();
	stdfoot();
	die();
}

$cat = $_GET["cat"];
$dllink  = (int)$_GET["dllink"];

$passkey = $_GET["passkey"];
if (!get_row_count("users", "WHERE passkey=".sqlesc($passkey)))
	$passkey = "";


$where = "";
$wherea = array();
if (!$incldead) {
	$wherea[] = "visible='yes'";
}

if ($cat) {
	$cats = implode(", ", array_unique(array_map("intval", explode(",", ( string ) $cat))));
	$wherea[] = "category in ($cats)";
}

if (is_valid_id($_GET["user"])) {
	$wherea[] = "owner=$_GET[user]";
}

if ($wherea)
	$where = "WHERE ".implode(" AND ", $wherea);

$limit = "LIMIT 50";

// start the RSS feed output
header("Content-Type: application/xhtml+xml; charset=$site_config[CHARSET]"); 
echo("<?xml version=\"1.0\" encoding=\"$site_config[CHARSET]\"?>");
echo("<rss version=\"2.0\"><channel><generator>" . htmlspecialchars($site_config["SITENAME"]) . " RSS 2.0</generator><language>en</language>" . 
"<title>" . $site_config["SITENAME"] . "</title><description>" . htmlspecialchars($site_config["SITENAME"]) . " RSS Feed</description><link>" . $site_config["SITEURL"] . "</link><copyright>Copyright " . htmlspecialchars($site_config["SITENAME"]) . "</copyright><pubDate>".date("r")."</pubDate>"); 

$res = SQL_Query_exec("SELECT torrents.id, torrents.name, torrents.size, torrents.category, torrents.added, torrents.leechers, torrents.seeders, categories.parent_cat as cat_parent, categories.name AS cat_name FROM torrents LEFT JOIN categories ON category = categories.id $where ORDER BY added DESC $limit");

while ($row = mysqli_fetch_array ($res)){ 
	list($id,$name,$size,$category,$added,$leechers,$seeders,$catname) = $row; 

	if ($dllink) {
		if ($passkey)
			$link = "$site_config[SITEURL]/download.php?id=$id&amp;passkey=$passkey"; 
		else
			$link = "$site_config[SITEURL]/download.php?id=$id"; 
	} else {
		$link = $site_config["SITEURL"]."/torrents-details.php?id=$id&amp;hit=1"; 
	}

	$pubdate = date("r", sql_timestamp_to_unix_timestamp($added));


	echo("<item><title>" . htmlspecialchars($name) . "</title><guid>" . $link . "</guid><link>" . $link . "</link><pubDate>" . $pubdate . "</pubDate>	<category> " . $row["cat_parent"] . ": " . $row["cat_name"] . "</category><description>Category: " . $row["cat_parent"] . ": " . $row["cat_name"] . "  Size: " . mksize($size) . " Added: " . $added . " Seeders: " . $seeders . " Leechers: " . $leechers . "</description></item>"); 
} 


echo("</channel></rss>"); 