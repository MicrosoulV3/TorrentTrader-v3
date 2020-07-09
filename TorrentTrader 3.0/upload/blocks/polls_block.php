<?php
begin_block(T_("POLL"));

if (!function_exists("srt")) {
	function srt($a,$b){
		if ($a[0] > $b[0]) return -1;
		if ($a[0] < $b[0]) return 1;
		return 0;
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURUSER && $_POST["act"] == "takepoll"){
	$choice = $_POST["choice"];
	if ($choice != "" && $choice < 256 && $choice == floor($choice)){
		$res = SQL_Query_exec("SELECT * FROM polls ORDER BY added DESC LIMIT 1");
		$arr = mysqli_fetch_assoc($res) or show_error_msg(T_("ERROR"), "No Poll", 1);

		$pollid = $arr["id"];
		$userid = $CURUSER["id"];

		$res = SQL_Query_exec("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid");
		$arr = mysqli_fetch_assoc($res);

		if ($arr){
			show_error_msg(T_("ERROR"), "You have already voted!", 0);
		}else{

			SQL_Query_exec("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)");
			if (mysqli_affected_rows($GLOBALS["DBconnector"]) != 1)
					show_error_msg(T_("ERROR"), "An error occured. Your vote has not been counted.", 0);
		}
	}else{
		show_error_msg(T_("ERROR"), "Please select an option.", 0);
	}
}

// Get current poll
if ($CURUSER){
	$res = SQL_Query_exec("SELECT * FROM polls ORDER BY added DESC LIMIT 1");

	if($pollok=(mysqli_num_rows($res))) {
		$arr = mysqli_fetch_assoc($res);
		$pollid = $arr["id"];
		$userid = $CURUSER["id"];
		$question = $arr["question"];

		$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
    	$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
    	$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
    	$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

		// Check if user has already voted
  		$res = SQL_Query_exec("SELECT * FROM pollanswers WHERE pollid=$pollid AND userid=$userid");
  		$arr2 = mysqli_fetch_assoc($res);
	}

	//Display Current Poll
	if($pollok) {
		print("<center><b>$question</b></center>\n");
  		$voted = $arr2;

		// If member has voted already show results
  		if ($voted) {
    		if ($arr["selection"])
      			$uservote = $arr["selection"];
    		else
      			$uservote = -1;

			// we reserve 255 for blank vote.
    		$res = SQL_Query_exec("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20");

    		$tvotes = mysqli_num_rows($res);

    		$vs = array(); // array of
    		$os = array();

    		// Count votes
    		while ($arr2 = mysqli_fetch_row($res))
      		$vs[$arr2[0]] += 1;

    		reset($o);
    		for ($i = 0; $i < count($o); ++$i)
      		if ($o[$i])
        		$os[$i] = array($vs[$i], $o[$i]);

    		// now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
    		if ($arr["sort"] == "yes")
    			usort($os, srt);

    		print("<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n");
    		$i = 0;

    		while ($a = $os[$i]){
      			if ($i == $uservote)
        			$a[1] .= "&nbsp;*";
      			if ($tvotes == 0)
      				$p = 0;
      			else
      				$p = round($a[0] / $tvotes * 100);
      			if ($i % 2)
        			$c = "";
      			else
        			$c = " class='poll-alt'";
      			print("<tr><td width='1%'$c>" . format_comment($a[1]) . "&nbsp;&nbsp;</td><td width='99%'$c><img src='".$site_config["SITEURL"]."/images/poll/bar_left.gif' alt='' /><img src='".$site_config["SITEURL"]."/images/poll/bar.gif' height='9' width='" . ($p / 2) . "' alt='' /><img src='".$site_config["SITEURL"]."/images/poll/bar_right.gif' alt='' />$p%</td></tr>\n");
      			++$i;
    		}

		print("</table>\n");
		$tvotes = number_format($tvotes);
    	print("<center>".T_("VOTES").": $tvotes</center>\n");

  	}else{//User has not voted, show options

    	print("<form method='post' action='". encodehtml($_SERVER["REQUEST_URI"]) ."'>\n");
	print("<input type='hidden' name='act' value='takepoll' />");
    	$i = 0;

    	while ($a = $o[$i]){
      		print("<input type='radio' name='choice' value='$i' />".format_comment($a)."<br />\n");
      		++$i;
    	}

    	print("<br />");
    	print("<input type='radio' name='choice' value='255' />".T_("BLANK_VOTE")."<br />\n");
    	print("<center><input type='submit' value='".T_("VOTE")."!' /></center></form><br />");
  	}

	} else {
  		echo"<br /><br /><center>No Active Polls</center><br /><br />\n";
	}
} else {
	echo"<br /><br /><center>".T_("POLL_MUST_LOGIN")."</center><br /><br />\n";
}

end_block();
?>
