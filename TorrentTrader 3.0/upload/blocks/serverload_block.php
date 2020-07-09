<?php
//SERVER LOAD BLOCK
begin_block(T_("SERVER_LOAD"));

if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
	if (!class_exists("COM"))
		echo "COM support not available.";
	else {
		function mkprettytime2($s){
			foreach (array("60:sec","60:min","24:hour","1:day") as $x) {
				$y = explode(":", $x);
				if ($y[0] > 1) {
					$v = $s % $y[0];
					$s = floor($s / $y[0]);
				} else
					$v = $s;
				$t[$y[1]] = $v;
			}

			if ($t['week'] > 1 || $t['week'] == 0) $wk = " weeks";
			else $wk = " week";
			if ($t['day'] > 1 || $t['day'] == 0) $day = " days";
			else $day = " day";
			if ($t['hour'] > 1 || $t['hour'] == 0) $hr = " hrs";
			else $hr = " hr";
			if ($t['min'] > 1 || $t['min'] == 0) $min = " mins";
			else $min = " min";
			if ($t['sec'] > 1 || $t['sec'] == 0) $sec = " secs";
			else $sec = " sec";

			if ($t["month"])
				return "{$t['month']}$mth {$t['week']}$wk {$t['day']}$day ".sprintf("%d$hr %02d$min %02d$sec", $t["hour"], $t["min"], $t["sec"], $f["month"]);
			if ($t["week"])
				return "{$t['week']}$wk {$t['day']}$day ".sprintf("%d$hr %02d$min %02d$sec", $t["hour"], $t["min"], $t["sec"], $f["month"]);
			if ($t["day"])
				return "{$t['day']}$day ".sprintf("%d$hr %02d$min %02d$sec", $t["hour"], $t["min"], $t["sec"]);
			if ($t["hour"])
				return sprintf("%d$hr %02d$min %02d$sec", $t["hour"], $t["min"], $t["sec"]);
			if ($t["min"])
				return sprintf("%d$min %02d$sec", $t["min"], $t["sec"]);
			return $t["sec"].$sec;
		}

		if (version_compare(PHP_VERSION, '5.0.0', '<'))
			require("backend/serverload4.php");
		else
			require("backend/serverload5.php");
	}
} else {
	// Users and load information
	$reguptime = exec("uptime");
	if ($reguptime) {
		if (preg_match("/up (.*), *(\d) (users?), .*: (.*), (.*), (.*)/", $reguptime, $uptime)) {
			$up = preg_replace("!(\d\d):(\d\d)!", '\1h\2m', $uptime[1]);
			$users[0] = $uptime[2];
			$users[1] = $uptime[3];
			$loadnow = $uptime[4];
			$load5 = $uptime[5];
			$load15 = $uptime[6];
		}
	} else {
		$up = "--";
		$users[0] = "NA";
		$users[1] = "--";
		$loadnow = "NA";
		$load5 = "--";
		$load15 = "--";
	}

	// Operating system
	$temp = file_get_contents("/proc/version");
	if ($temp) {

		$osarray = explode(" ", $temp);

		$distros = array(
			"Gentoo", "/etc/gentoo-release",
			"Fedora Core", "/etc/fedora-release",
			"Slackware", "/etc/slackware-version",
			"Cobalt", "/etc/cobalt-release",
			"Debian", "/etc/debian_version",
			"Mandrake", "/etc/mandrake-release",
			"Mandrake", "/etc/mandrakelinux-release",
			"Yellow Dog", "/etc/yellowdog-release",
			"Red Hat", "/etc/redhat-release",
			"Arch Linux", "/etc/arch-release"
		);

		$distro = "";
		if (file_exists("/etc/lsb-release")) {
			$lsb = file_get_contents("/etc/lsb-release");
			preg_match('!DISTRIB_DESCRIPTION="(.*)"!', $lsb, $distro);
			$distro = $distro[1];
		} else do {
			if (file_exists($distros[1])) {
				$distro = file_get_contents($distros[1]);
				$distro = "$distros[0] ".preg_replace("/[^0-9]*([0-9.]+)[^0-9.]{0,1}.*/", "\\1", $distro);
				break;
			}
			array_shift($distros); array_shift($distros);
		} while (count($distros));

		if (!$distro) {
			$distro = "Unknown Distro";
		}

		$operatingsystem = "$distro ($osarray[0] $osarray[2])";

	} else {
		$operatingsystem = "(N/A)";
	}

	// RAM usage
	$meminfo = file_get_contents("/proc/meminfo");
	preg_match("!^MemTotal:\s*(.*) kB!m", $meminfo, $memtotal);
	$memtotal = $memtotal[1] * 1024;
	preg_match("!^MemFree:\s*(.*) kB!m", $meminfo, $memfree);
	$memfree = $memfree[1] * 1024;
	preg_match("!^Buffers:\s*(.*) kB!m", $meminfo, $buffers);
	$buffers = $buffers[1] * 1024;
	preg_match("!^Cached:\s*(.*) kB!m", $meminfo, $cached);
	$cached = $cached[1] * 1024;

	$memused = mksize($memtotal - $memfree - $buffers - $cached);
	$memtotal = mksize($memtotal);


	//echo("<b>Current Users:</b> $users[0]<br>
	echo("<b>".T_("CURRENT_LOAD").":</b> $loadnow<br /><b>".T_("LOAD_5_MINS").":</b> $load5<br /><b>".T_("LOAD_15_MINS").":</b> $load15<br /><hr />");

	echo("<b>OS:</b> $operatingsystem<br />");
	echo("<b>".T_("RAM_USED").":</b> $memused/$memtotal<br />");
	echo("<b>".T_("UPTIME").":</b> $up<br />");

}
end_block();
?>