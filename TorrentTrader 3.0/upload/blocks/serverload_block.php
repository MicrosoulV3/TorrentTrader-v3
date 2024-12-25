<?php
//SERVER LOAD BLOCK
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
    begin_block(T_("SERVER_LOAD"));
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td align="center">
    <?php
    if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
        if (!class_exists("COM"))
            echo "COM support not available.";
    } else {
        // Load information
        $reguptime = exec("uptime");
        if ($reguptime) {
            if (preg_match("/up\s+(?:(\d+)\s+days?,\s+)?(?:(\d+):(\d+))?,/", $reguptime, $uptimeMatches)) {
                $days = isset($uptimeMatches[1]) ? $uptimeMatches[1] : 0;
                $hours = isset($uptimeMatches[2]) ? $uptimeMatches[2] : 0;
                $minutes = isset($uptimeMatches[3]) ? $uptimeMatches[3] : 0;

                // Build a user-friendly uptime string
                $up = [];
                if ($days > 0) $up[] = "$days day" . ($days > 1 ? "s" : "");
                if ($hours > 0) $up[] = "$hours hour" . ($hours > 1 ? "s" : "");
                if ($minutes > 0) $up[] = "$minutes minute" . ($minutes > 1 ? "s" : "");

                $up = implode(", ", $up);
            } else {
                $up = "--";
            }

            // Extract load averages and user count
            if (preg_match("/(\d+) (users?), .*: (.*), (.*), (.*)/", $reguptime, $loadMatches)) {
                $users[0] = $loadMatches[1];
                $users[1] = $loadMatches[2];
                $loadnow = $loadMatches[3];
                $load5 = $loadMatches[4];
                $load15 = $loadMatches[5];
            } else {
                $users[0] = "NA";
                $users[1] = "--";
                $loadnow = "NA";
                $load5 = "--";
                $load15 = "--";
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
        $operatingsystem = defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : php_uname('s') . " " . php_uname('r');

        // RAM usage
        $meminfo = file_get_contents("/proc/meminfo");
        preg_match("/^MemTotal:\s*(?P<total>\d+) kB.*^MemFree:\s*(?P<free>\d+) kB.*^Buffers:\s*(?P<buffers>\d+) kB.*^Cached:\s*(?P<cached>\d+) kB/ms", $meminfo, $matches);

        $memtotal = $matches['total'] * 1024;
        $memfree = $matches['free'] * 1024;
        $buffers = $matches['buffers'] * 1024;
        $cached = $matches['cached'] * 1024;

        $memused = mksize($memtotal - $memfree - $buffers - $cached);
        $memtotal = mksize($memtotal);
        $phpload = round(memory_get_usage() / 1000000, 2);

        // Disk space
        $fs = "/";
        $disk_total_space = disk_total_space($fs);
        $disk_free_space = disk_free_space($fs);
        $disk_used_space = $disk_total_space - $disk_free_space;

        echo ("<b>Total HDD: </b>" . round($disk_total_space / (1024 * 1024 * 1024)) . " GB<br />");
        echo ("<b>HDD Used: </b>" . round($disk_used_space / (1024 * 1024 * 1024)) . " GB<br />");
        echo ("<b>Percent Used: </b>" . round(($disk_used_space / $disk_total_space) * 100) . " %<br /><hr />");

        // CPU Information
        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^model name\s+\:\s+(.*)$/m', $cpuinfo, $modelMatches);
        $totalCores = count($modelMatches[1]);
        $cpuModel = $modelMatches[1][0] ?? "Unknown";

        echo "<b>CPU:</b> $cpuModel<br />";
        echo "<b>Total Cores:</b> $totalCores<br /><hr />";

        // Display server stats
        echo("<b>" . T_("CURRENT_LOAD") . ":</b> $loadnow<br />");
        echo("<b>" . T_("LOAD_5_MINS") . ":</b> $load5<br />");
        echo("<b>" . T_("LOAD_15_MINS") . ":</b> $load15<br /><hr />");
        echo("<b>PHP Load:</b> $phpload MB<br />");
        echo("<b>OS:</b> $operatingsystem<br />");
        echo("<b>" . T_("RAM_USED") . ":</b> $memused of $memtotal<br />");
        echo("<b>" . T_("UPTIME") . ":</b> $up<br />");
    }
    ?>
    </td>
    </tr>
    </table>
    <?php
    end_block();
}
?>
