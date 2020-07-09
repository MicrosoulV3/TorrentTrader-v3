<?php
require_once("backend/functions.php");
dbconn();
loggedinonly();

$id = (int) $_REQUEST["id"];
if (!is_valid_id($id)) show_error_msg(T_("ERROR"), T_("INVALID_ID"), 1);
$action = $_REQUEST["action"];

$row = mysqli_fetch_assoc(SQL_Query_exec("SELECT `owner` FROM `torrents` WHERE id=$id"));
if($CURUSER["edit_torrents"]=="no" && $CURUSER['id'] != $row['owner'])
    show_error_msg(T_("ERROR"), T_("NO_TORRENT_EDIT_PERMISSION"), 1);


function uploadimage($x, $imgname, $tid) {
    global $site_config;

    $imagesdir = $site_config["torrent_dir"]."/images";

    $allowed_types = &$site_config["allowed_image_types"];  

    if ( !( $_FILES["image$x"]["name"] == "" ) ) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            $del = unlink($img);
        }

        $y = $x + 1;
 
	$im = getimagesize($_FILES["image$x"]["tmp_name"]);

	if (!$im[2])
		show_error_msg(T_("ERROR"), "Invalid Image $y.", 1);

	if (!array_key_exists($im['mime'], $allowed_types))
		show_error_msg(T_("ERROR"), T_("INVALID_FILETYPE_IMAGE"), 1);

        if ($_FILES["image$x"]["size"] > $site_config['image_max_filesize'])
            show_error_msg(T_("ERROR"), sprintf(T_("INVAILD_FILE_SIZE_IMAGE"), $y), 1);

        $uploaddir = "$imagesdir/";

	    $ifilename = $tid . $x . $allowed_types[$im['mime']];
                                              
        $copy = copy($_FILES["image$x"]["tmp_name"], $uploaddir.$ifilename);

        if (!$copy)
            show_error_msg(T_("ERROR"), sprintf(T_("ERROR_UPLOADING_IMAGE"), $y), 1);

        return $ifilename;
    }
}//end func


//GET DATA FROM DB
$res = SQL_Query_exec("SELECT * FROM torrents WHERE id = $id");
$row = mysqli_fetch_array($res);
if (!$row){
    show_error_msg(T_("ERROR"), T_("TORRENT_ID_GONE"), 1);
}

$torrent_dir = $site_config["torrent_dir"];    
$nfo_dir = $site_config["nfo_dir"];    

//DELETE TORRENT
if ($action=="deleteit"){
    $torrentid = (int) $_POST["torrentid"];
    $delreason = sqlesc($_POST["delreason"]);
    $torrentname = $_POST["torrentname"];

    if (!is_valid_id($torrentid))
        show_error_msg(T_("FAILED"), T_("INVALID_TORRENT_ID"), 1);

    if (!$delreason){
        show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA"), 1);
    }

    deletetorrent($torrentid);

    write_log($CURUSER['username']." has deleted torrent: ID:$torrentid - ".htmlspecialchars($torrentname)." - Reason: ".htmlspecialchars($delreason));
    if ($CURUSER['id'] != $row['owner']) {
	$delreason = $_POST["delreason"];
	SQL_Query_exec("INSERT INTO messages (sender, receiver, added, subject, msg, unread, location) VALUES(0, ".$row['owner'].", '".get_date_time()."', 'Your torrent \'$torrentname\' has been deleted by ".$CURUSER['username']."', ".sqlesc("'$torrentname' was deleted by ".$CURUSER['username']."\n\nReason: $delreason").", 'yes', 'in')");
    }

    show_error_msg(T_("COMPLETED"), htmlspecialchars($torrentname)." ".T_("HAS_BEEN_DEL_DB"),1);
    die;
}

//DO THE SAVE TO DB HERE
if ($action=="doedit"){
    $updateset = array();

    $nfoaction = $_POST['nfoaction'];
    if ($nfoaction == "update"){
      $nfofile = $_FILES['nfofile'];
      if (!$nfofile) die("No data " . var_dump($_FILES));
      if ($nfofile['size'] > 65535)
        show_error_msg("NFO is too big!", "Max 65,535 bytes.",1);
      $nfofilename = $nfofile['tmp_name'];
      if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0){
            @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
            $updateset[] = "nfo = 'yes'";
        }//success
    }

    if (!empty($_POST["name"]))
         $updateset[] = "name = " . sqlesc($_POST["name"]);
    
    $updateset[] = "descr = " . sqlesc($_POST["descr"]);
    $updateset[] = "category = " . (int) $_POST["type"];
    $updateset[] = "torrentlang = " . (int) $_POST["language"];

    if ($CURUSER["edit_torrents"] == "yes") {
        if ($_POST["banned"]) {
            $updateset[] = "banned = 'yes'";
            $_POST["visible"] = 0;
        } else {
            $updateset[] = "banned = 'no'";
        }
    }

    $updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";

    if ($CURUSER["edit_torrents"] == "yes")
        $updateset[] = "freeleech = '".$_POST["freeleech"]."'";

    $updateset[] = "anon = '" . ($_POST["anon"] ? "yes" : "no") . "'";

    //update images
    $img1action = $_POST['img1action'];
    if ($img1action == "update")
        $updateset[] = "image1 = " .sqlesc(uploadimage(0, $row["image1"], $id));
    if ($img1action == "delete") {
        if ($row[image1]) {
            $del = unlink($site_config["torrent_dir"]."/images/$row[image1]");
            $updateset[] = "image1 = ''";
        }
    }

    $img2action = $_POST['img2action'];
    if ($img2action == "update")
        $updateset[] = "image2 = " .sqlesc(uploadimage(1, $row["image2"], $id));
    if ($img2action == "delete") {
        if ($row[image2]) {
            $del = unlink($site_config["torrent_dir"]."/images/$row[image2]");
            $updateset[] = "image2 = ''";
        }
    }


    SQL_Query_exec("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");

    $returl = "torrents-edit.php?id=$id&edited=1";
    if (isset($_POST["returnto"])){
        $returl = $_POST["returnto"];
    }

    write_log("Torrent $id (".htmlspecialchars($_POST["name"]).") was edited by $CURUSER[username]");

    header("Location: $returl");
    die();
}//END SAVE TO DB

//UPDATE CATEGORY DROPDOWN
$catdropdown = "<select name=\"type\">\n";
$cats = genrelist();
    foreach ($cats as $catdropdownubrow) {
        $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
        if ($catdropdownubrow["id"] == $row["category"])
            $catdropdown .= " selected=\"selected\"";
        $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
    }
$catdropdown .= "</select>\n";
//END CATDROPDOWN

//UPDATE TORRENTLANG DROPDOWN
$langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
$lang = langlist();
foreach ($lang as $lang) {
    $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
    if ($lang["id"] == $row["torrentlang"])
        $langdropdown .= " selected=\"selected\"";
    $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
}
$langdropdown .= "</select>\n";
//END TORRENTLANG


$char1 = 55;
$shortname = CutName(htmlspecialchars($row["name"]), $char1);

if ($_GET["edited"]){
    show_error_msg("Edited OK", T_("TORRENT_EDITED_OK"), 1);
}

stdhead(T_("EDIT_TORRENT")." \"$shortname\"");

begin_frame(T_("EDIT_TORRENT")." \"$shortname\"");

print("<br /><br /><form method='post' name=\"bbform\" enctype=\"multipart/form-data\" action=\"torrents-edit.php?action=doedit\">\n");
print("<input type=\"hidden\" name=\"id\" value=\"$id\" />\n");

if (isset($_GET["returnto"]))
    print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");

print("<table class='table_table' cellspacing='0' cellpadding='4' width='586' align='center'>\n");
echo "<tr><td class='table_col1' align='right' width='60'><b>".T_("NAME").": </b></td><td class='table_col2' ><input type=\"text\" name=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" size=\"60\" /></td></tr>";
echo "<tr><td class='table_col1'  align='right'><b>".T_("IMAGE").": </b></td><td class='table_col2'><b>".T_("IMAGE")." 1:</b>&nbsp;&nbsp;<input type='radio' name='img1action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img1action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image0' size='60' /> <br /><br /> <b>".T_("IMAGE")." 2:</b>&nbsp;&nbsp;<input type='radio' name='img2action' value='keep' checked='checked' />".T_("KEEP_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='delete' />".T_("DELETE_IMAGE")."&nbsp;&nbsp;"."<input type='radio' name='img2action' value='update' />".T_("UPDATE_IMAGE")."<br /><input type='file' name='image1' size='60' /></td></tr>";
echo "<tr><td class='table_col1'  align='right'><b>".T_("NFO").": </b><br /></td><td class='table_col2' ><input type='radio' name='nfoaction' value='keep' checked='checked' />Keep NFO &nbsp; <input type='radio' name='nfoaction' value='update' />Update NFO:";
if ($row["nfo"] == "yes"){
    echo "&nbsp;&nbsp;<a href='nfo-view.php?id=".$row["id"]."' target='_blank'>[".T_("VIEW_CURRENT_NFO")."]</a>";
} else{
    echo "&nbsp;&nbsp;<font color='#ff0000'>".T_("NO_NFO_UPLOADED")."</font>";
}
echo "<br /><input type='file' name='nfofile' size='60' /></td></tr>";

echo "<tr><td class='table_col1' align='right'><b>".T_("CATEGORIES").": </b></td><td class='table_col2'>".$catdropdown."</td></tr>";

echo "<tr><td class='table_col1' align='right'><b>".T_("LANG").": </b></td><td class='table_col2'>".$langdropdown."</td></tr>";

if ($CURUSER["edit_torrents"] == "yes")
    echo "<tr><td class='table_col1' align='right'><b>".T_("BANNED").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> ".T_("BANNED")."?<br /></td></tr>";
echo "<tr><td class='table_col1' align='right'><b>".T_("VISIBLE").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"visible\"" . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> " .T_("VISIBLEONMAIN"). "<br /></td></tr>";

if ($row["external"] != "yes" && $CURUSER["edit_torrents"] == "yes"){
    echo "<tr><td class='table_col1' align='right'><b>".T_("FREE_LEECH").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"freeleech\"" . (($row["freeleech"] == "1") ? " checked=\"checked\"" : "" ) . " value=\"1\" />".T_("FREE_LEECH_MSG")."<br /></td></tr>";
}

if ($site_config['ANONYMOUSUPLOAD']) {
	echo "<tr><td class='table_col1' align='right'><b>".T_("ANONYMOUS_UPLOAD").": </b></td><td class='table_col2'><input type=\"checkbox\" name=\"anon\"" . (($row["anon"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" />(".T_("ANONYMOUS_UPLOAD_MSG").")<br /></td></tr>";
}
print ("<tr><td class='table_head' align='center' colspan='2'><b>" .T_("DESCRIPTION"). ":</b></td></tr></table>");
require_once("backend/bbcode.php");
print textbbcode("bbform","descr", htmlspecialchars($row["descr"]));

    
print("<br /><center><input type=\"submit\" value='".T_("SUBMIT")."' /> <input type='reset' value='".T_("UNDO")."' /></center>\n");
print("</form>\n");
end_frame();

begin_frame(T_("DELETE_TORRENT"));
        print("<center><form method='post' action='torrents-edit.php?action=deleteit&amp;id=$id'>\n");
        print("<input type='hidden' name='torrentid' value='$id' />\n");
        print("<input type='hidden' name='torrentname' value='".htmlspecialchars($row["name"])."' />\n");
        echo "<b>".T_("REASON_FOR_DELETE")."</b><input type='text' size='30' name='delreason' />";
        echo "&nbsp;<input type='submit' value='".T_("DELETE_TORRENT")."' /></form></center>";
end_frame();

stdfoot();

?>
