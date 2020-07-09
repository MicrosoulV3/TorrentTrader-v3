<?php

/*array info for ref:
announce
infohash
creation date
intenal name
torrentsize
filecount
announceruls
comment
filelist
*/


function ParseTorrent($filename) {
	require_once("BDecode.php") ;
	require_once("BEncode.php") ;

	$TorrentInfo = array();

	global $array;

	//check file type is a torrent
	$torrent = explode(".", $filename);
    $fileend = end($torrent);
    $fileend = strtolower($fileend);

	if ( $fileend == "torrent" ) {
		$parseme = @file_get_contents("$filename");

	if ($parseme == FALSE) {
		show_error_msg(T_("ERROR"), T_("PARSE_CONTENTS"),1);
	}

	if(!isset($parseme)){
		show_error_msg(T_("ERROR"), T_("PARSE_OPEN"),1);
	}else{
		$array = BDecode($parseme);
		if ($array === FALSE){
			show_error_msg(T_("ERROR"), T_("PARSE_DECODE"),1);
		}else{
			if(!@count($array['info'])){
				show_error_msg(T_("ERROR"), T_("PARSE_OPEN"), 1);
			}else{
				//Get Announce URL
				$TorrentInfo[0] = $array["announce"];

				//Get Announce List Array
				if (isset($array["announce-list"])){
					$TorrentInfo[6] = $array["announce-list"];
				}

				//Read info, store as (infovariable)
				$infovariable = $array["info"];
				
				// Calculates SHA1 Hash
				$infohash = sha1(BEncode($infovariable));
				$TorrentInfo[1] = $infohash ;
				
				// Calculates date from UNIX Epoch
				$makedate = date('r' , $array["creation date"]);
				$TorrentInfo[2] = $makedate ;

				// The name of the torrent is different to the file name
				$TorrentInfo[3] = $infovariable['name'] ;

				//Get File List
				if (isset($infovariable["files"]))  {
					// Multi File Torrent
					$filecount = "";

					//Get filenames here
					$TorrentInfo[8] = $infovariable["files"];

					foreach ($infovariable["files"] as $file) {
						if(is_numeric($filecount))
						$filecount += "1";
						$multiname = $file['path'];//Not needed here really
						$multitorrentsize = $file['length'];
						$torrentsize += $file['length'];
					}

					$TorrentInfo[4] = $torrentsize;  //Add all parts sizes to get total
					$TorrentInfo[5] = $filecount;  //Get file count
				}else {
					// Single File Torrent
					$torrentsize = $infovariable['length'];
					$TorrentInfo[4] = $torrentsize;//Get file count
					$TorrentInfo[5] = "1";
				}

				// Get Torrent Comment
				if(isset($array['comment'])) {
					 $TorrentInfo[7] = $array['comment'];
				}
			}
		}
	}
}
return $TorrentInfo;
}//End Function
?>