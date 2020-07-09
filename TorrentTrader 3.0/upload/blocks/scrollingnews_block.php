<?php

if ($site_config['NEWSON']){ //check news is turned on first   
	begin_block(T_("LATEST_NEWS"));

	$res = SQL_Query_exec("SELECT * FROM news ORDER BY added DESC LIMIT 10");

	?>
	<style type="text/css">

	#marqueecontainer{
	position: relative;
	/*width: 200px; marquee width */
	height: 200px; /*marquee height */
	background-color: white;
	overflow: hidden;
	/*border: 3px solid orange;*/
	padding: 2px;
	padding-left: 4px;
	}

	</style>

	<script type="text/javascript">

	/***********************************************
	* Cross browser Marquee II- ? Dynamic Drive (www.dynamicdrive.com)
	* This notice MUST stay intact for legal use
	* Visit http://www.dynamicdrive.com/ for this script and 100s more.
	***********************************************/

	var delayb4scroll=2000 //Specify initial delay before marquee starts to scroll on page (2000=2 seconds)
	var marqueespeed=1 //Specify marquee scroll speed (larger is faster 1-10)
	var pauseit=1 //Pause marquee onMousever (0=no. 1=yes)?

	////NO NEED TO EDIT BELOW THIS LINE////////////

	var copyspeed=marqueespeed
	var pausespeed=(pauseit==0)? copyspeed: 0
	var actualheight=''

	function scrollmarquee(){
	if (parseInt(cross_marquee.style.top)>(actualheight*(-1)+8))
	cross_marquee.style.top=parseInt(cross_marquee.style.top)-copyspeed+"px"
	else
	cross_marquee.style.top=parseInt(marqueeheight)+8+"px"
	}

	function initializemarquee(){
	cross_marquee=document.getElementById("vmarquee")
	cross_marquee.style.top=0
	marqueeheight=document.getElementById("marqueecontainer").offsetHeight
	actualheight=cross_marquee.offsetHeight
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
	cross_marquee.style.height=marqueeheight+"px"
	cross_marquee.style.overflow="scroll"
	return
	}
	setTimeout('lefttime=setInterval("scrollmarquee()",30)', delayb4scroll)
	}

<?php if (mysqli_num_rows($res) > 3) {?>
	if (window.addEventListener)
	window.addEventListener("load", initializemarquee, false)
	else if (window.attachEvent)
	window.attachEvent("onload", initializemarquee)
	else if (document.getElementById)
	window.onload=initializemarquee
<?php } ?>

	</script>

	<div id="marqueecontainer" onmouseover="copyspeed=pausespeed" onmouseout="copyspeed=marqueespeed" style="background-color: transparent;">
	<div id="vmarquee" style="position: absolute; width: 100%; background-color: transparent;">

	<!--YOUR SCROLL CONTENT HERE-->
	<?php
	if (mysqli_num_rows($res)){
		while($array = mysqli_fetch_assoc($res)){
			print("<a href='comments.php?type=news&amp;id=". $array['id'] . "'><b>". $array['title'] . "</b></a><br /><b>".T_("POSTED").":</b> " . gmdate("d-M-y", utc_to_tz_time($array["added"])) . "<br /><br />");
		}
	}else{
		echo T_("NO_NEWS");
	}
	?>
	</div>
	</div>
	<?php

	end_block();
}//end newson check
?>