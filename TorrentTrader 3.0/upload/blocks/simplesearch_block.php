<?php
begin_block(T_("SEARCH"));
?>
	<center>
	<form method="get" action="torrents-search.php"><br />
	<input type="text" name="search" size="15" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
	<br /><br />
	<input type="submit" value="<?php echo T_("SEARCH"); ?>" />
	</form>
	</center><br />
	<?php
end_block();
?>
