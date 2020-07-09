<?php
$wmi = new COM("Winmgmts://");
$cpus = $wmi->InstancesOf("Win32_Processor");
$os = $wmi->InstancesOf("Win32_OperatingSystem");
foreach ($os as $os)
	$os = "$os->Caption $os->CSDVersion";

$os = preg_replace("/(Microsoft|\(R\)|, \w+ Edition)/", "", $os);
$os = str_replace("Service Pack ", "SP", $os);

$system = $wmi->InstancesOf("Win32_ComputerSystem");

$ram = $wmi->InstancesOf("Win32_LogicalMemoryConfiguration");
foreach ($ram as $ram)
	$ramtotal = $ram->TotalPhysicalMemory*1024;

$ram = $wmi->InstancesOf("Win32_PerfRawData_PerfOS_Memory");
foreach ($ram as $ram);
$ramused = $ramtotal-$ram->AvailableBytes;
$ramused = mksize($ramused);
$ramtotal = mksize($ramtotal);

$uptime = $wmi->InstancesOf("Win32_PerfFormattedData_PerfOS_System");
foreach ($uptime as $uptime);
$uptime = mkprettytime2($uptime->SystemUpTime);

foreach ($cpus as $cpu) {
	$cpus1[] = $cpu->LoadPercentage;
	$totalusage += $cpu->LoadPercentage;
}
	
$cpucount = count($cpus1);
$totalusage = round($totalusage/$cpucount, 2);	

echo "<b>OS:</b> $os<br />";
echo "<b>Number of CPUs:</b> $cpucount<br />";
for ($i=0;$i<count($cpus1);$i++)
	echo "<b>CPU$i Usage:</b> $cpus1[$i]%<br />";
echo "<b>Total CPU Usage:</b> $totalusage%<br />";
echo "<b>RAM Usage:</b> $ramused/$ramtotal<br />";
echo "<b>".T_("UPTIME").":</b> $uptime";
?>