<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("SEARCH"));
?>
	<center>
	<form method="get" action="torrents-search.php"><br />
	<input type="text" name="search" size="15" value="<?php echo htmlspecialchars($_GET["search"]); ?>" />
	<br /><br />
	<select name="cat">
	<option value="0">(<?php echo T_("ALL_TYPES"); ?>)</option>
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
	<?php echo $catdropdown; ?>
	</select>
	<br /><br />
	<select name="incldead">
	<option value="0"><?php echo T_("ACTIVE"); ?></option>
	<option value="1"><?php echo T_("INCLUDE_DEAD"); ?></option>
	<option value="2"><?php echo T_("ONLY_DEAD"); ?></option>
	</select>
	<?php if ($site_config["ALLOWEXTERNAL"]){?>
		<br /><br />
		<select name="inclexternal">
		<option value="0"><?php echo T_("LOCAL"); ?>/<?php echo T_("EXTERNAL"); ?></option>
		<option value="1"><?php echo T_("LOCAL_ONLY"); ?></option>
		<option value="2"><?php echo T_("EXTERNAL_ONLY"); ?></option>
		</select>
		<?php } ?>
	<br /><br />
	<input type="submit" value="<?php echo T_("SEARCH"); ?>" />
	</form>
	</center>
	<?php
	end_block();
}
?>