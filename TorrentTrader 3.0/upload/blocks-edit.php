<?php                     
require_once("backend/functions.php");
dbconn();
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
    show_error_msg(T_("ERROR"), T_("_ACCESS_DEN_"), 1);
}
                         
if ($_GET["preview"]) {
	$site_config["LEFTNAV"] = $site_config["RIGHTNAV"] = $site_config["MIDDLENAV"] = false;
}

stdhead(T_("_BLC_MAN_"));

if($_GET["preview"]){
	$name = cleanstr($_GET["name"]);
	if (!file_exists("blocks/{$name}_block.php"))
		show_error_msg(T_("ERROR"), "Possible XSS attempt.", 1);

	echo "<a name=\"".$name."\"></a>";
	begin_frame(T_("_BLC_PREVIEW_"));
	
		echo "<br /><center><b>".T_("_BLC_USE_SITE_SET_")."</b></center><hr />";
		echo "<table border=\"0\" width=\"180\" align=\"center\"><tr><td>";
		include("blocks/".$name."_block.php");
		echo "</td></tr></table><hr />";
		echo "<center><a href=\"javascript: self.close();\">".T_("_CLS_WIN_")."</a></center>";
		
	end_frame();
	stdfoot();
	die();
}

begin_frame(T_("_BLC_MAN_"));

// == addnew

if(@count($_POST["addnew"])){
	foreach($_POST["addnew"] as $addthis){
		$i = $addthis;
		
		$addblock = $_POST["addblock_".$i];
		$wantedname = sqlesc($_POST["wantedname_".$i]);
		$name = sqlesc(str_replace("_block.php","",cleanstr($addblock)));
		$description = sqlesc($_POST["wanteddescription_".$i]);

		SQL_Query_exec("INSERT INTO blocks (named, name, description, position, enabled, sort) VALUES ($wantedname, $name, $description, 'left', 0, 0)")  or ((mysqli_errno($GLOBALS["DBconnector"]) == 1062) ? show_error_msg(T_("ERROR"),"Sorry, this block is in database already!",1) : show_error_msg(T_("ERROR"),"Database Query failed: " . mysqli_error($GLOBALS["DBconnector"])));
		if(mysqli_affected_rows($GLOBALS["DBconnector"]) != 0){
			$success = "<center><font size=\"3\"><b>".T_("_SUCCESS_ADD_")."</b></font></center><br />";
		}else{
			$success = "<center><font size=\"3\"><b>".T_("_FAIL_ADD_")."</b></font></center><br />";
		}
	}
	echo $success;
}// end addnew

// == permanent delete
if(@count($_POST["deletepermanent"])){
	foreach($_POST["deletepermanent"] as $delpthis){
		unlink("blocks/".$delpthis);
		if(file_exists("blocks/".$delpthis))
			$delmessage="<center><font size=\"3\"><b>".T_("_FAIL_DEL_")."</b></font></center><br />";
		else
			$delmessage="<center><font size=\"3\"><b>".T_("_SUCCESS_DEL_")."</b></font></center><br />";
	}
	echo $delmessage;
}// end addnew

$nextleft=(mysqli_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='left' AND enabled=1"))+1);
$nextmiddle=(mysqli_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='middle' AND enabled=1"))+1);
$nextright=(mysqli_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='right' AND enabled=1"))+1);

// upload block
if($_POST["upload"] == "true"){    
	$uplfailmessage = "";
	$uplsuccessmessage = "";
	if ($_FILES['blockupl']) {

		$blockfile = $_FILES['blockupl'];

		if ($blockfile["name"] == ""){
			$uplfailmessage .= "<br />".T_("_SEND_NOTHING_");
		}
		if (($blockfile["size"] == 0) && ($blockfile["name"] != "")){ 
			$uplfailmessage .= "<br />".T_("_SEND_EMPTY_");
		}
		if ((!preg_match('/^(.+)\.php$/si', $blockfile['name'], $fmatches)) && ($blockfile["name"] != "")){
			$uplfailmessage .= "<br />".T_("_SEND_INVALID_");
		}
		if ((!preg_match('/^(.+)\_block.php$/si', $blockfile['name'], $fmatches)) && ($blockfile["name"] != "")){
			$uplfailmessage .= "<br />".T_("_SEND_NO_BLOCK_");
		}

		$blockfilename = $blockfile['tmp_name'];
		if (@!is_uploaded_file($blockfilename)){
			$uplfailmessage .= "<br />".T_("_FAIL_UPL_");
		}
		
	}

	if(!$uplfailmessage){
		$blockfilename = $site_config['blocks_dir'] . "/" . $blockfile['name'];
		if($_POST["uploadonly"]){
			if(file_exists($blockfilename)){
				$uplfailmessage .= "<center><font size=\"3\">\"".$blockfile['name']."\"<b> ".T_("_BLC_EXIST_")."</b></font></center><br />";
			}else{
				if(@!move_uploaded_file($blockfile["tmp_name"], $blockfilename)){
					$uplfailmessage .= "<center><font size=\"3\"><b>".T_("_CANNOT_MOVE_")." </b> \"".$blockfile['name']."\" <b>".T_("_TO_DEST_DIR_")."</b></font></center><br />".T_("_CONFIG_DEST_DIR_").": <b>\"".$site_config['blocks_dir']. "\"</b><br />".T_("_PLS_CHECK_")." <b>config.php</b> ".T_("_SURE_FULL_PATH_").". ".T_("_YOUR_CASE_").": <b>\"".$_SERVER['DOCUMENT_ROOT']."\"</b> + <b>\"/".T_("_SUB_DIR_")."\"</b> (".T_("_IF_ANY_").") ".T_("_AND_")." + <b>\"/blocks\"</b>.";
				}else{
					$uplsuccessmessage .= "<center><font size=\"3\">\"".$blockfile['name']."\" <b>".T_("_SUCCESS_UPL_")."</b></font></center><br />";
				}
			}
		}else{
			if(file_exists($blockfilename)){
				$uplfailmessage .= "<center><font size=\"3\">\"".$blockfile['name']."\"<b> ".T_("_BLC_EXIST_")."</b></font></center><br />";
			}else{
				if(@!move_uploaded_file($blockfile["tmp_name"], $blockfilename)){
					$uplfailmessage .= "<center><font size=\"3\"><b>".T_("_CANNOT_MOVE_")." </b> \"".$blockfile['name']."\" <b>".T_("_TO_DEST_DIR_")."</b></font></center><br />".T_("_CONFIG_DEST_DIR_").": <b>\"".$site_config['blocks_dir']. "\"</b><br />".T_("_PLS_CHECK_")." <b>config.php</b> ".T_("_SURE_FULL_PATH_").". ".T_("_YOUR_CASE_").": <b>\"".$_SERVER['DOCUMENT_ROOT']."\"</b> + <b>\"/".T_("_SUB_DIR_")."\"</b> (".T_("_IF_ANY_").") ".T_("_AND_")." + <b>\"/blocks\"</b>.";
				}else{
					$named = ($_POST["wantedname"] ? $_POST["wantedname"] : str_replace("_block.php","",$blockfile['name']));
					$name  = str_replace("_block.php","",$blockfile['name']);
					$description = $_POST["description"];
					$position = $_POST["position"];
					$sort = ($_POST["enabledyes"] ? $uplsort : 0);
					$enabled = ($_POST["enabledyes"] ? 1 : 0);
					
                    SQL_Query_exec("INSERT INTO blocks (named, name, description, position, sort, enabled) VALUES (
                    ".sqlesc($named).", ".sqlesc($name).", ".sqlesc($description).", ".sqlesc($position).", ".sqlesc($sort).", ".sqlesc($enabled).")");

					if(mysqli_affected_rows($GLOBALS["DBconnector"]) != 0){
						$uplsuccessmessage .= "<center><font size='3'><b>".T_("_SUCCESS_UPL_ADD_")."</b></font></center><br />";
					}else{
						$uplfailmessage .= "<center><font size='3'><b>".T_("_FAIL_UPL_ADD_")."</b></font></center><br />";
					}
					echo $uplsuccessmessage;
				}
			}
		}
	}
}// end upload block			

// == edit
if ($_REQUEST["edit"] == "true")
{
    # Prune Block Cache.
	$TTCache->Delete("blocks_left");
	$TTCache->Delete("blocks_middle");
	$TTCache->Delete("blocks_right");
    
	//resort left blocks
	function resortleft(){
		$sortleft = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysqli_fetch_assoc($sortleft)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}
	//resort middle blocks
	function resortmiddle(){
		$sortmiddle = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysqli_fetch_assoc($sortmiddle)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}
	//resort right blocks
	function resortright(){
		$sortright = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysqli_fetch_assoc($sortright)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}

	// == delete

	if(@count($_POST["delete"])){
		foreach($_POST["delete"] as $delthis){
			SQL_Query_exec("DELETE FROM blocks WHERE id=".sqlesc($delthis));
		}
			resortleft();
			resortmiddle();
			resortright();
	}// == end delete

	// == move to left
	if(is_valid_id($_GET["left"])){
		SQL_Query_exec("UPDATE blocks SET position = 'left', sort = $nextleft WHERE id = " . $_GET["left"]);
		resortmiddle();
		resortright();
	}// end move to left
	
	// == move to center
	if(is_valid_id($_GET["middle"])){
		SQL_Query_exec("UPDATE blocks SET position = 'middle', sort = $nextmiddle WHERE id = " . $_GET["middle"]);
		resortleft();
		resortright();
	}// end move to center
	
	// == move to right
	if(is_valid_id($_GET["right"])){
		SQL_Query_exec("UPDATE blocks SET position = 'right', sort = $nextright WHERE enabled=1 AND id = " . $_GET["right"]);
		resortleft();
		resortmiddle();
	}// end move to right
	
	// == move upper
	if(is_valid_id($_GET["up"])){
		$cur = SQL_Query_exec("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["up"]);
		$curent = mysqli_fetch_assoc($cur);

        $sort = ( int ) $_GET["sort"];
        
		SQL_Query_exec("UPDATE blocks SET sort = ".$sort." WHERE sort = ".($sort-1)." AND id != " . $_GET["up"] . " AND position = " . sqlesc($_GET["position"]) . "");
		SQL_Query_exec("UPDATE blocks SET sort = ".($sort-1)." WHERE id = " . $_GET["up"]);
	}// end move to upper
	
	// == move lower
	if(is_valid_id($_GET["down"])){
		$cur = SQL_Query_exec("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["down"]);
		$curent = mysqli_fetch_assoc($cur);

        $sort = ( int ) $_GET["sort"];
        
		SQL_Query_exec("UPDATE blocks SET sort = ".($sort+1)." WHERE id = " . $_GET["down"]);
		SQL_Query_exec("UPDATE blocks SET sort = ".$sort." WHERE sort = ".($sort+1)." AND id != " . $_GET["down"] . " AND position = " . sqlesc($_GET["position"]) ."");
	}// end move lower
	
	// == update
	$res=SQL_Query_exec("SELECT * FROM blocks ORDER BY id");

	if(!$_GET["up"] && !$_GET["down"] && !$_GET["right"] && !$_GET["left"] && !$_GET["middle"]){
         
        $update = array();
        
		while($upd = mysqli_fetch_assoc($res)){
			$id = $upd["id"];
			$update[] = "enabled = ".$_POST["enable_".$upd["id"]];
			$update[] = "named = '".mysqli_real_escape_string($GLOBALS["DBconnector"],$_POST["named_".$upd["id"]])."'";
			$update[] = "description = '".mysqli_real_escape_string($GLOBALS["DBconnector"],$_POST["description_".$upd["id"]])."'";
			
			if(($upd["enabled"] == 0) && ($upd["position"] == "left") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextleft;
			elseif(($upd["enabled"] == 0) && ($upd["position"] == "middle") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextmiddle;
			elseif(($upd["enabled"] == 0) && ($upd["position"] == "right") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextright;
			
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "left") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "middle") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "right") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			else
				$update[] = "sort = ".$upd["sort"];
				
			SQL_Query_exec("UPDATE blocks SET ". implode(", ", $update). " WHERE id=$id") or show_error_msg(T_("ERROR"), "".T_("_FAIL_DB_QUERY_").": ".mysqli_error($GLOBALS["DBconnector"]));
		}
	}
	resortleft();
	resortmiddle();
	resortright();
}// == end edit

echo "<center><a href=\"index.php\">".T_("HOME")."</a>&nbsp;&#8226;&nbsp;<a href=\"admincp.php\">".T_("ADMIN_CP")."</a>&nbsp;&#8226;&nbsp;<a href=\"admincp.php?action=blocks&amp;do=view\">".T_("_BLC_MAN_")."</a></center>";

// ---- <table> for blocks in database -----------------------------------------
print("<hr />");
$res = SQL_Query_exec("SELECT * FROM blocks ORDER BY enabled DESC, position, sort");

print("<table align=\"center\"><tr><td>".
	"<form name=\"blocks\" method=\"post\" action=\"blocks-edit.php\">".
	"<input type=\"hidden\" name=\"edit\" value=\"true\" />".
	"<table align=\"center\" class=\"table_table\" cellspacing=\"0\" width=\"650\">".
		"<tr>".
			"<td class=\"table_head\" align=\"center\"><font size=\"2\"><b>".T_("_BLC_MAN_")."</b></font></td>".
		"</tr>".
	"</table><br />".
	"<table cellspacing=\"0\" class=\"table_table\" align=\"center\" width=\"650\">".
		"<tr>".
			"<th rowspan=\"2\" class=\"table_head\">".T_("_NAMED_")."<br />(".T_("_FL_NM_IF_NO_SET_").")</th>".
			"<th rowspan=\"2\" class=\"table_head\">".T_("_FILE_NAME_")."</th>".
			"<th rowspan=\"2\" class=\"table_head\">".T_("DESCRIPTION")."<br />(".T_("_MAX_")." 255 ".T_("_CHARS_").")</th>".
			"<th rowspan=\"2\" colspan=\"3\" class=\"table_head\">".T_("_POSITION_")."</th>".
			"<th rowspan=\"2\" colspan=\"2\" class=\"table_head\">".T_("_SORT_ORDER_")."</th>".
			"<th colspan=\"2\" class=\"table_head\">".T_("ENABLED")."</th>".
			"<th rowspan=\"2\" class=\"table_head\">".T_("_DEL_")."</th>".
		"</tr>".
		"<tr>".
			"<th class=\"table_head\">".T_("YES")."</th>".
			"<th class=\"table_head\">".T_("NO")."</th>".
		"</tr>");

while($blocks2 = mysqli_fetch_assoc($res)){
	$down=$blocks["id"];
	if(!$setclass){
		$class="table_col2";$setclass=true;}
	else{
		$class="table_col1";$setclass=false;}
	switch($blocks2["position"]){
		case "left":
			$pos = T_("_LEFT_");
			break;
		case "middle":
			$pos = T_("_MIDDLE_");
			break;
		case "right":
			$pos = T_("_RIGHT_");
			break;
		}

	print("<tr>".     # id=\"qq"\" - removed
			"<td rowspan=\"2\" class=\"$class\"><input type=\"text\" name=\"named_".$blocks2["id"]."\" value=\"".($blocks2["named"] ? $blocks2["named"] : $blocks2["name"])."\" /></td>".
			"<td rowspan=\"2\" class=\"$class\">".$blocks2["name"]."</td>".
			"<td rowspan=\"2\" class=\"$class\"><textarea name=\"description_".$blocks2["id"]."\" rows=\"2\" cols=\"20\">".$blocks2["description"]."</textarea></td>".
			"<td colspan=\"3\" class=\"$class\" align=\"center\">".$pos."</td>".
			"<td colspan=\"2\" class=\"$class\" align=\"center\">".$blocks2["sort"]."</td>".
			"<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"radio\" name=\"enable_".$blocks2["id"]."\"".($blocks2["enabled"] ? " checked=\"checked\"" : "")." value=\"1\" /></td>".
			"<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"radio\" name=\"enable_".$blocks2["id"]."\"".(!$blocks2["enabled"] ? " checked=\"checked\"" : "")." value=\"0\" /></td>".
			"<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"checkbox\" name=\"delete[]\" value=\"".$blocks2["id"]."\"/></td>".
		"</tr>".
		"<tr>".
			"<td class=\"$class\" height=\"1%\">".((($blocks2["position"] != "left") && ($blocks2["enabled"] == 1)) ? "<a href=\"blocks-edit.php?edit=true&amp;position=left&amp;left=".$blocks2["id"]."\"><img border=\"0\" src=\"images/blocks/leftenable.gif\" width=\"18\" height=\"15\"  /></a>" : "<img border=\"0\" src=\"images/blocks/leftdisable.gif\" width=\"18\" height=\"15\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_LEFT_")."\"" : "")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_LEFT_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")."  />")."</td>".
			"<td class=\"$class\" height=\"1%\">".((($blocks2["position"] != "middle") && ($blocks2["enabled"] == 1)) ? "<a href=\"blocks-edit.php?edit=true&amp;position=middle&amp;middle=".$blocks2["id"]."\"><img border=\"0\" src=\"images/blocks/middleenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"images/blocks/middledisable.gif\" width=\"18\" height=\"15\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_CENTER_")."\"" : "")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_CENTER_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")."  />")."</td>".
			"<td class=\"$class\" height=\"1%\">".((($blocks2["position"] != "right") && ($blocks2["enabled"] == 1)) ? "<a href=\"blocks-edit.php?edit=true&amp;position=right&amp;right=".$blocks2["id"]."\"><img border=\"0\" src=\"images/blocks/rightenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"images/blocks/rightdisable.gif\" width=\"18\" height=\"15\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_RIGHT_")."\"" : "")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_RIGHT_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")."  />")."</td>".
			"<td class=\"$class\" height=\"1%\">".((($blocks2["sort"]!= 1) && ($blocks2["enabled"] != 0)) ? "<a href=\"blocks-edit.php?edit=true&amp;position=".$blocks2["position"]."&amp;sort=".$blocks2["sort"]."&amp;up=".$blocks2["id"]."\"><img border=\"0\" src=\"images/blocks/upenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border='0' src=\"images/blocks/updisable.gif\" width=\"18\" height=\"15\" alt=\"".($blocks2["enabled"] ? "".T_("_AT_TOP_")."" : "")."\" ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_TOP_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." />")."</td>".
			"<td class=\"$class\" height=\"1%\">".(((($blocks2["sort"] != ($nextleft-1)) && ($blocks2["position"] == "left") || ($blocks2["sort"] != ($nextright-1)) && ($blocks2["position"] == "right") || ($blocks2["sort"] != ($nextmiddle-1)) && ($blocks2["position"] == "middle")) && ($blocks2["enabled"] != 0)) ? "<a href=\"blocks-edit.php?edit=true&amp;position=".$blocks2["position"]."&amp;sort=".$blocks2["sort"]."&amp;down=".$blocks2["id"]."\"><img border=\"0\" src=\"images/blocks/downenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"images/blocks/downdisable.gif\" width=\"18\" height=\"15\" alt=\"".($blocks2["enabled"] ? "".T_("_AT_BOTTOM_")."" : "")."\" ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_BOTTOM_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." />")."</td>".
		"</tr>");
}	
print("<tr>".
		"<td colspan=\"11\" align=\"center\" class=\"table_head\"><input type=\"submit\" value=\"".T_("_BTN_UPDT_")."\" /></td>".
	"</tr>".
	"</table>".
	"</form></td></tr></table>");
// ---- </table> for blocks in database -----------------------------------------
	
// ---- <table> for blocks exist but not in database ----------------------------
$exist=SQL_Query_exec("SELECT name FROM blocks");
while($fileexist = mysqli_fetch_assoc($exist)){
	$indb[] = $fileexist["name"]."_block.php";
}

if ($folder = opendir('blocks')) {
    while (false !== ($file = readdir($folder))) {
        if ($file != "." && $file != ".." && !in_array($file, $indb)) {
            if (preg_match("/_block.php/i", $file))
                $infolder[] = $file;
        }
    }
    closedir($folder);
}

if($infolder){
	print("<a name=\"anb\"></a>");
	print("<hr />");
	echo $success.$delmessage;
	
	print("<table align=\"center\"><tr><td>");
	print("<form name=\"addnewblock\" method=\"post\" action=\"blocks-edit.php#anb\">".        
		"<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"650\">".
			"<tr>".
				"<th class=\"table_head\" align=\"center\"><font size=\"2\"><b>".T_("_BLC_AVAIL_")."</b></font><br />(".T_("_IN_FOLDER_").")</th>".
			"</tr>".
		"</table><br />".
		"<table width=\"650\" cellspacing=\"1\" class=\"table_table\" align=\"center\">".
			"<tr>".
				"<th class=\"table_head\">".T_("_NAMED_")."<br />(".T_("_FL_NM_IF_NO_SET_").")</th>".
				"<th class=\"table_head\">".T_("FILE")."</th>".
				"<th class=\"table_head\">".T_("DESCRIPTION")."<br />(".T_("_MAX_")." 255 ".T_("_CHARS_").")</th>".
				"<th class=\"table_head\">".T_("_ADD_")."</th>".
				"<th class=\"table_head\">".T_("_DEL_")."</th>".
			"</tr>");
	
			/* loop over the blocks directory and take file names witch are not in database. */
			if ($folder = opendir('blocks')) {
				$i=0;
				while (false !== ($file = readdir($folder))) {
					if ($file != "." && $file != ".." && !in_array($file, $indb)) {
						if (preg_match("/_block.php/i", $file)){
							if(!$setclass){
								$class="table_col2";$setclass=true;}
							else{
								$class="table_col1";$setclass=false;}
							print("<tr>".
										"".
										"<td class=\"$class\"><input type=\"hidden\" name=\"addblock_".$i."\" value=\"".$file."\" /><input type=\"text\" name=\"wantedname_".$i."\" value=\"".str_replace("_block.php","",$file)."\"/></td>".
										"<td class=\"$class\">$file</td>".
										"<td class=\"$class\" align=\"center\"><textarea name=\"wanteddescription_".$i."\" rows='2' cols='20'></textarea></td>".
										"<td class=\"$class\" align=\"center\"><div id=\"addn_".$i."\" ><input type='checkbox' name='addnew[]' value=\"".$i."\" onclick=\"javascript: if(dltp_".$i.".style.display=='none'){dltp_".$i.".style.display='block'}else{dltp_".$i.".style.display='none'}; \" /></div></td>".
										"<td class=\"$class\" align=\"center\"><div id=\"dltp_".$i."\" ><input type='checkbox' name='deletepermanent[]' value=\"".$file."\" onclick=\"javascript: if(addn_".$i.".style.display=='none'){addn_".$i.".style.display='block'}else{addn_".$i.".style.display='none'}\" /></div></td>".
									"</tr>");
							$i++;
						}
					}
				}
			closedir($folder);
			}
			/* end loop over the blocks directory and take names. */
	
	print("<tr>".
				"<td colspan=\"5\" class=\"table_head\" align=\"center\"><input type=\"submit\" name=\"submit\" class=\"btn\" value=\"".T_("_BTN_DOIT_")."\" />&nbsp;<input type=\"reset\" class=\"btn\" value=\"".T_("RESET")."\" /></td>".
			"</tr>".
			"</table>".
		"</form></td></tr></table>");
	print("<center>(".T_("_DLT_WIL_PER_")." <font color='#ff0000'>".T_("_NO_ADD_WAR_")."</font>)</center><br />");
}
// ---- </table> for blocks exist but not in database ----------------------------

// ---- <table> for upload block -------------------------------------------------
print("<a name=\"upload\"></a>");
print("<hr />");

if($_POST["upload"]){
	if($uplfailmessage){
		echo $uplfailmessage;
	}else{
	echo $uplsuccessmessage;
	}
}

print("<table align=\"center\" width=\"350\"><tr><td>");
print("<form enctype=\"multipart/form-data\"  action=\"blocks-edit.php#upload\" method=\"post\" >".
			"<input type=\"hidden\" name=\"upload\" value=\"true\" />".
		"<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
			"<tr>".
					"<td class=\"table_head\" align=\"center\"><font size=\"2\"><b>".T_("_BLC_UPL_")."</b></font><br /></td>".
			"</tr>".
		"</table><br />".
		"<table class=\"table_table\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
			"<tr>".
				"<td class=\"table_col1\" valign=\"middle\">".T_("_NAMED_")."</td>".
				"<td class=\"table_col2\" valign=\"top\"><input type=\"text\" size=\"33\" name=\"wantedname\" /><br />(".T_("_FL_NM_IF_NO_SET_").")</td>".
			"</tr>".
			"<tr>".
				"<td class=\"table_col1\" valign=\"middle\">".T_("DESCRIPTION")."</td>".
				"<td class=\"table_col2\" valign=\"top\"><textarea name=\"description\" rows=\"2\" cols=\"25\"></textarea><br />(".T_("_MAX_")." 255 ".T_("_CHARS_").")</td>".
			"</tr>".
			"<tr>".
				"<td class=\"table_col1\" valign=\"middle\">".T_("FILE")."</td>".
				"<td class=\"table_col2\" valign=\"top\"><input type=\"file\" name=\"blockupl\" /></td>".
			"</tr>".
		"</table><br />".
"<div id=\"pos\">".
"<table class=\"table_table\" cellspacing=\"0\" align=\"center\" width=\"100%\">".
  "<tr>".
    "<th class=\"table_head\" colspan=\"3\">".T_("_POSITION_")."</th>".
    "<th class=\"table_head\">".T_("_SORT_")."</th>".
    "<th class=\"table_head\">".T_("ENABLED")."</th>".
    "<th class=\"table_head\">".T_("_JUST_UPL_")."</th>".
  "</tr>".
  "<tr>".
    "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" checked=\"checked\" value=\"left\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$nextleft';}else{uplsort.value = '0';} \" /></td>".
    "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" value=\"middle\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$nextmiddle';}else{uplsort.value = '0';} \" /></td>".
    "<td align=\"center\" class=\"table_col2\"><input type=\"radio\" name=\"position\" value=\"right\" onclick=\"javascript: if(enabledyes.checked){uplsort.value = '$nextright';}else{uplsort.value = '0';} \" /></td>".
    "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"text\" name=\"uplsort\" size=\"1\" readonly=\"readonly\" value=\"0\" style=\"text-align: center;\" onclick=\"javascript: alert('".T_("_CLICK_POS_")."');\" /></td>".
    "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"checkbox\" name=\"enabledyes\" onclick=\"javascript: uploadonly.disabled = enabledyes.checked; if(enabledyesnotice.style.display == 'block'){enabledyesnotice.style.display = 'none'}else{enabledyesnotice.style.display = 'block'}; if(!checked){uplsort.value = '0'}\"   /></td>".
    "<td rowspan=\"2\" align=\"center\" class=\"table_col2\"><input type=\"checkbox\" name=\"uploadonly\" onclick=\"javascript: wantedname.disabled = enabledyes.disabled = description.disabled = pos.disabled = uploadonly.checked; if(uploadonlynotice.style.display == 'block'){uploadonlynotice.style.display = 'none'}else{uploadonlynotice.style.display = 'block'};\"   /></td>".
  "</tr>".
  "<tr>".
    "<td align=\"center\" class=\"table_col1\">[".T_("L")."]</td>".
    "<td align=\"center\" class=\"table_col1\">[".T_("_M_")."]</td>".
    "<td align=\"center\" class=\"table_col1\">[".T_("_R_")."]</td>".
  "</tr>".
  "<tr>".
    "<td colspan=\"6\" align=\"center\" class=\"table_head\"><input type=\"submit\" class=\"btn\" value=\"".T_("UPLOAD")."\" /><div id=\"uploadonlynotice\" style=\"display: none;\">(".T_("_UPL_ONLY_").")</div><div id=\"enabledyesnotice\" style=\"display: none;\">(".T_("_UPL_ADD_").")</div></td>".
    "</tr>".
"</table>".
"</div>".
	"</form>");
print("</td></tr></table>");	
// ---- </table> for upload block -------------------------------------------------
	
end_frame();
stdfoot();

?>