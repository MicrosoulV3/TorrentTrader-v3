<?php
require_once("backend/functions.php");
dbconn();

// check access and rights
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["can_upload"]=="no")
		show_error_msg(T_("ERROR"), T_("UPLOAD_NO_PERMISSION"), 1);
	if ($site_config["UPLOADERSONLY"] && $CURUSER["class"] < 4)
		show_error_msg(T_("ERROR"), T_("UPLOAD_ONLY_FOR_UPLOADERS"), 1);
}

$announce_urls = explode(",", strtolower($site_config["announce_list"]));  //generate announce_urls[] from config.php

if ($_POST["takeupload"] == "yes") {
	require_once("backend/parse.php");

    // Check form data.
    if ( ! isset($_POST['type'], $_POST['name'], $_FILES['torrent']) )
          $message = T_('MISSING_FORM_DATA'); 
    
    if (($num = $_FILES['torrent']['error']))
         show_error_msg(T_('ERROR'), T_("UPLOAD_ERR[$num]"), 1);

	$f = $_FILES["torrent"];
	$fname = $f["name"];

	if (empty($fname))
		$message = T_("EMPTY_FILENAME");

    $nfo = 'no';
        
	if ($_FILES['nfo']['size'] != 0) {
		$nfofile = $_FILES['nfo'];

		if ($nfofile['name'] == '')
			$message = T_("NO_NFO_UPLOADED");
			
		if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches))
			$message = T_("UPLOAD_NOT_NFO");

		if ($nfofile['size'] == 0)
			$message = T_("NO_NFO_SIZE");

		if ($nfofile['size'] > 65535)
			$message = T_("NFO_UPLOAD_SIZE");

		$nfofilename = $nfofile['tmp_name'];

        if (($num = $_FILES['nfo']['error']))
             $message = T_("UPLOAD_ERR[$num]");
        
		$nfo = 'yes';
	}

	$descr = $_POST["descr"];

	if (!$descr)
		$descr = T_("UPLOAD_NO_DESC");

	$langid = (int) $_POST["lang"];
	
	/*if (!is_valid_id($langid))
		$message = "Please be sure to select a torrent language";*/

	$catid = (int) $_POST["type"];

	if (!is_valid_id($catid))
		$message = T_("UPLOAD_NO_CAT");

	if (!validfilename($fname))
		$message = T_("UPLOAD_INVALID_FILENAME");

	if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
		$message = T_("UPLOAD_INVALID_FILENAME_NOT_TORRENT");

		$shortfname = $torrent = $matches[1];

	if (!empty($_POST["name"]))
		$name = $_POST["name"];
        
    $tmpname = $f['tmp_name'];

	//end check form data

	if (!$message) {
	//parse torrent file
	$torrent_dir = $site_config["torrent_dir"];	
	$nfo_dir = $site_config["nfo_dir"];	

	//if(!copy($f, "$torrent_dir/$fname"))
	if(!move_uploaded_file($tmpname, "$torrent_dir/$fname"))
		show_error_msg(T_("ERROR"), T_("ERROR"). ": " . T_("UPLOAD_COULD_NOT_BE_COPIED")." $tmpname - $torrent_dir - $fname",1);

    $TorrentInfo = array();
    $TorrentInfo = ParseTorrent("$torrent_dir/$fname");


	$announce = $TorrentInfo[0];
	$infohash = $TorrentInfo[1];
	$creationdate = $TorrentInfo[2];
	$internalname = $TorrentInfo[3];
	$torrentsize = $TorrentInfo[4];
	$filecount = $TorrentInfo[5];
	$annlist = $TorrentInfo[6];
	$comment = $TorrentInfo[7];
	$filelist = $TorrentInfo[8];

/*
//for debug...
	print ("<br /><br />announce: ".$announce."");
	print ("<br /><br />infohash: ".$infohash."");
	print ("<br /><br />creationdate: ".$creationdate."");
	print ("<br /><br />internalname: ".$internalname."");
	print ("<br /><br />torrentsize: ".$torrentsize."");
	print ("<br /><br />filecount: ".$filecount."");
	print ("<br /><br />annlist: ".$annlist."");
	print ("<br /><br />comment: ".$comment."");
*/
	
	//check announce url is local or external
	if (!in_array($announce, $announce_urls, 1)){
		$external='yes';
    }else{
		$external='no';
	}

	//if externals is turned off
	if (!$site_config["ALLOWEXTERNAL"] && $external == 'yes')
		$message = T_("UPLOAD_NO_TRACKER_ANNOUNCE");
	}
	if ($message) {
		@unlink("$torrent_dir/$fname");
		@unlink($tmpname);
		@unlink("$nfo_dir/$nfofilename");
		show_error_msg(T_("UPLOAD_FAILED"), $message,1);
	}

	//release name check and adjust
	if ($name ==""){
		$name = $internalname;
	}
	$name = str_replace(".torrent","",$name);
	$name = str_replace("_", " ", $name);

	//upload images
	$allowed_types = &$site_config["allowed_image_types"];

	$inames = array();
	for ($x=0; $x < 2; $x++) {
		if (!($_FILES['image'.$x]['name'] == "")) {
			$y = $x + 1;

			//if (!preg_match('/^(.+)\.(jpg|gif|png)$/si', $_FILES[image.$x]['name']))
			//	show_error_msg(T_("INVAILD_IMAGE"), T_("THIS_FILETYPE_NOT_IMAGE"), 1);

			if ($_FILES['image$x']['size'] > $site_config['image_max_filesize'])
				show_error_msg(T_("ERROR"), T_("INVAILD_FILE_SIZE_IMAGE"), 1);

			$uploaddir = $site_config["torrent_dir"]."/images/";

			$ifile = $_FILES['image'.$x]['tmp_name'];

			$im = getimagesize($ifile);

			if (!$im[2])
				show_error_msg(T_("ERROR"), sprintf(T_("INVALID_IMAGE"), $y), 1);

			if (!array_key_exists($im['mime'], $allowed_types))
				show_error_msg(T_("ERROR"), T_("INVALID_FILETYPE_IMAGE"), 1);

			$ret = SQL_Query_exec("SHOW TABLE STATUS LIKE 'torrents'");
			$row = mysqli_fetch_array($ret);
			$next_id = $row['Auto_increment'];

			$ifilename = $next_id . $x . $allowed_types[$im['mime']];

			$copy = copy($ifile, $uploaddir.$ifilename);

			if (!$copy)
				show_error_msg(T_("ERROR"), sprintf(T_("IMAGE_UPLOAD_FAILED"), $y), 1);

			$inames[] = $ifilename;

		}

	}
	//end upload images

	//anonymous upload
	$anonyupload = $_POST["anonycheck"]; 
	if ($anonyupload == "yes") {
		$anon = "yes";
	}else{
		$anon = "no";
	}

	$ret = SQL_Query_exec("INSERT INTO torrents (filename, owner, name, descr, image1, image2, category, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon, last_action) VALUES (".sqlesc($fname).", '".$CURUSER['id']."', ".sqlesc($name).", ".sqlesc($descr).", '".$inames[0]."', '".$inames[1]."', '".$catid."', '" . get_date_time() . "', '".$infohash."', '".$torrentsize."', '".$filecount."', ".sqlesc($fname).", '".$announce."', '".$external."', '".$nfo."', '".$langid."','$anon', '".get_date_time()."')");

	$id = mysqli_insert_id($GLOBALS["DBconnector"]);
	
	if (mysqli_errno($GLOBALS["DBconnector"]) == 1062)
		show_error_msg(T_("UPLOAD_FAILED"), T_("UPLOAD_ALREADY_UPLOADED"), 1);

	//Update the members uploaded torrent count
	/*if ($ret){
		SQL_Query_exec("UPDATE users SET torrents = torrents + 1 WHERE id = $userid");*/
        
	if($id == 0){
		unlink("$torrent_dir/$fname");
		$message = T_("UPLOAD_NO_ID");
		show_error_msg(T_("UPLOAD_FAILED"), $message, 1);
	}
    
    rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent"); 

	if (is_array($filelist)) {
		foreach ($filelist as $file) {
			$dir = '';
			$size = $file["length"];
			$count = count($file["path"]);
			for ($i=0; $i<$count;$i++) {
				if (($i+1) == $count)
					$fname = $dir.$file["path"][$i];
				else
					$dir .= $file["path"][$i]."/";
			}
			SQL_Query_exec("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES($id, ".sqlesc($fname).", $size)");
		}
	} else {
		SQL_Query_exec("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES($id, ".sqlesc($TorrentInfo[3]).", $torrentsize)");
	}

	if (!is_array($annlist)) {
		$annlist = array(array($announce));
	}
	foreach ($annlist as $ann) {
		foreach ($ann as $val) {
			if (strtolower(substr($val, 0, 4)) != "udp:") {
				SQL_Query_exec("INSERT INTO `announce` (`torrent`, `url`) VALUES($id, ".sqlesc($val).")");
			}
		}
	}

	if ($nfo == 'yes') { 
            move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo"); 
    } 

	//EXTERNAL SCRAPE
	if ($external=='yes' && $site_config['UPLOADSCRAPE']){
		$tracker=str_replace("/announce","/scrape",$announce);	
		$stats 			= torrent_scrape_url($tracker, $infohash);
		$seeders 		= (int) strip_tags($stats['seeds']);
		$leechers 		= (int) strip_tags($stats['peers']);
		$downloaded 	= (int) strip_tags($stats['downloaded']);

		SQL_Query_exec("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'"); 
	}
	//END SCRAPE

    write_log( sprintf(T_("TORRENT_UPLOADED"), htmlspecialchars($name), $CURUSER["username"]) );

	//Uploaded ok message (update later)
	if ($external=='no')
		$message = sprintf( T_("TORRENT_UPLOAD_LOCAL"), $name, $id, $id );
	else
		$message = sprintf( T_("TORRENT_UPLOAD_EXTERNAL"), $name, $id );
	show_error_msg(T_("UPLOAD_COMPLETE"), $message, 1);

	die();
}//takeupload


///////////////////// FORMAT PAGE ////////////////////////

stdhead(T_("UPLOAD"));

begin_frame(T_("UPLOAD_RULES"));
	echo "<b>".stripslashes($site_config["UPLOADRULES"])."</b>";
	echo "<br />";
end_frame();

begin_frame(T_("UPLOAD"));
?>
<form name="upload" enctype="multipart/form-data" action="torrents-upload.php" method="post">
<input type="hidden" name="takeupload" value="yes" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<?php
print ("<tr><td align='right' valign='top'>" . T_("ANNOUNCE_URL") . ": </td><td align='left'>");

while (list($key,$value) = thisEach($announce_urls)) {
	echo "<b>$value</b><br />";
}

if ($site_config["ALLOWEXTERNAL"]){
	echo "<br /><b>".T_("THIS_SITE_ACCEPTS_EXTERNAL")."</b>";
}
print ("</td></tr>");
print ("<tr><td align='right'>" . T_("TORRENT_FILE") . ": </td><td align='left'> <input type='file' name='torrent' size='50' value='" . $_FILES['torrent']['name'] . "' />\n</td></tr>");
print ("<tr><td align='right'>" .T_("NFO"). ": </td><td align='left'> <input type='file' name='nfo' size='50' value='" . $_FILES['nfo']['name'] . "' /><br />\n</td></tr>");
print ("<tr><td align='right'>" . T_("TORRENT_NAME") . ": </td><td align='left'><input type='text' name='name' size='60' value='" . $_POST['name'] . "' /><br />".T_("THIS_WILL_BE_TAKEN_TORRENT")." \n</td></tr>");
print ("<tr><td colspan='2' align='center'>".T_("MAX_FILE_SIZE").": ".mksize($site_config['image_max_filesize'])."<br />".T_("ACCEPTED_FORMATS").": ".implode(", ", array_unique($site_config["allowed_image_types"]))."<br /></td></tr><tr><td align='right'>".T_("IMAGE")." 1:&nbsp;&nbsp;</td><td><input type='file' name='image0' size='50' /></td></tr><tr><td align='right'>".T_("IMAGE")." 2:&nbsp;&nbsp;</td><td><input type='file' name='image1' size='50' /></td></tr>");

$category = "<select name=\"type\">\n<option value=\"0\">" . T_("CHOOSE_ONE") . "</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";

$category .= "</select>\n";
print ("<tr><td align='right'>" . T_("CATEGORY") . ": </td><td align='left'>".$category."</td></tr>");


$language = "<select name=\"lang\">\n<option value=\"0\">".T_("UNKNOWN_NA")."</option>\n";

$langs = langlist();
foreach ($langs as $row)
	$language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$language .= "</select>\n";
print ("<tr><td align='right'>".T_("LANGUAGE").": </td><td align='left'>".$language."</td></tr>");

if ($site_config['ANONYMOUSUPLOAD'] && $site_config["MEMBERSONLY"] ){ ?>
    <tr><td align="right"><?php echo T_("UPLOAD_ANONY");?>: </td><td><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />".T_("YES")." <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />".T_("NO").""); ?> &nbsp;<i><?php echo T_("UPLOAD_ANONY_MSG");?></i>
    </td></tr>
	<?php
}

print ("<tr><td align='center' colspan='2'>" . T_("DESCRIPTION") . "</td></tr></table>");

require_once("backend/bbcode.php");
print textbbcode("upload","descr","$descr");
?>

<br /><br /><center><input type="submit" value="<?php echo T_("UPLOAD_TORRENT"); ?>" /><br />
<i><?php echo T_("CLICK_ONCE_IMAGE");?></i>
</center>
</form>

<?php
end_frame();
stdfoot();
?>
