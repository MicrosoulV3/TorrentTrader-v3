<?php
//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify"){
    global $THEME, $site_config;
    
    $blockId = 'f-' . sha1($caption);
    print("<div class='myFrame'>
        <div class='myFrame-caption'>$caption<a style='float:right; clear:right;' href='#' class='showHide' id='$blockId'></a></div>
        <div class='myFrame-content'>
        <div class='slidingDiv$blockId'>");
}


//END FRAME
function end_frame() {
    global $THEME, $site_config;
    print("</div></div>
        <div class='myFrame-foot'></div>
      </div>
	  <br />");
}

//BEGIN BLOCK
function begin_block($caption = "-", $align = "justify"){
    global $THEME, $site_config;
    
    $blockId = 'b-' . sha1($caption);
    print("
      <div class='myBlock'>
        <div class='myBlock-caption'>$caption<a style='float:right; clear:right;' href='#' class='showHide' id='$blockId'></a></div>
        <div class='myBlock-content'>
        <div class='slidingDiv$blockId'>");
}

//END BLOCK
function end_block(){
    global $THEME, $site_config;
    print("</div></div>
        <div class='myBlock-foot'></div>
      </div>
	  <br />");
}

function begin_table(){
    print("<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headouter\" width=\"100%\"><tr><td><table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headinner\" width=\"100%\">\n");
}

function end_table()  {
    print("</table></td></tr></table>\n");
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=\"left\">$a</td></tr>\n");
}
?>