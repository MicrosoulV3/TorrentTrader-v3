<?php

 require_once("functions.php");  
 
 $smilies = array
 (
   ":)"    => "smile.png",
   ":("    => "sad.png",
   ";)"    => "wink.png",
   ":P"    => "razz.png",
   ":D"    => "grin.png",
   ":|"    => "plain.png",
   ":O"    => "suprise.png",
   ":?"    => "confused.png",
   "8)"    => "glasses.png",
   "8o"    => "eek.png",
   "B)"    => "cool.png",
   ":-)"   => "smile-big.png",
   ":-("   => "crying.png",
   ":-*"   => "kiss.png",
   "O:-D"  => "angel.png",
   ":-@"   => "devilish.png",
   ":o)"   => "monkey.png",
   ":help" => "help.png",
   ":love" => "love.png",
   ":warn" => "warn.png",
   ":bomb" => "bomb.png",
   ":idea" => "idea.png",
   ":bad"  => "bad.png",
   ":!"    => "important.png",
   "brb"   => "brb.png",
 );


// New (TorrentialStorm)
function insert_smilies_frame() {
	GLOBAL $site_config, $smilies;

	echo "<table><tr><td>Type...</td><td>To make a...</td></tr>";
	foreach ($smilies as $code => $url) {
		echo "<tr><td>$code</td><td><a href=\"javascript:window.opener.SmileIT('$code', '".cleanstr($_GET["form"])."', '".htmlspecialchars($_GET["text"])."')\"><img src=\"$site_config[SITEURL]/images/smilies/$url\" alt=\"$code\" title=\"$code\" border=\"0\"></a></td></tr>";
	}
	echo "</table>";
}

if ($_GET['action'] == "display"){
	insert_smilies_frame();
}

?>